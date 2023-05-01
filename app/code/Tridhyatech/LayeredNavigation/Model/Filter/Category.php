<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Filter;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Helper\Elasticsearch;
use Tridhyatech\LayeredNavigation\Helper\SearchEngine;
use Tridhyatech\LayeredNavigation\Model\CollectionFilterApplier;
use Tridhyatech\LayeredNavigation\Model\FacetedData\CategoryResolver;

/**
 * @since 1.0.0
 */
class Category extends \Magento\CatalogSearch\Model\Layer\Filter\Category
{

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Category
     */
    private $dataProvider;

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\FacetedData\CategoryResolver
     */
    private $facetedDataResolver;

    /**
     * @var mixed|\Tridhyatech\LayeredNavigation\Model\CollectionFilterApplier
     */
    private $collectionFilterApplier;

    /**
     * @var mixed|\Tridhyatech\LayeredNavigation\Helper\SearchEngine
     */
    private $searchEngine;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory                      $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface                           $storeManager
     * @param \Magento\Catalog\Model\Layer                                         $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder                 $itemDataBuilder
     * @param \Magento\Framework\Escaper                                           $escaper
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory     $categoryDataProviderFactory
     * @param \Tridhyatech\LayeredNavigation\Helper\Elasticsearch               $elasticsearchHelper
     * @param \Tridhyatech\LayeredNavigation\Helper\Config                      $config
     * @param \Tridhyatech\LayeredNavigation\Model\FacetedData\CategoryResolver $facetedDataResolver
     * @param array                                                                $data
     * @param \Tridhyatech\LayeredNavigation\Model\CollectionFilterApplier|null $collectionFilterApplier
     * @param \Tridhyatech\LayeredNavigation\Helper\SearchEngine|null           $searchEngine
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory,
        Elasticsearch $elasticsearchHelper,
        Config $config,
        CategoryResolver $facetedDataResolver,
        array $data = [],
        CollectionFilterApplier $collectionFilterApplier = null,
        SearchEngine $searchEngine = null
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $escaper,
            $categoryDataProviderFactory,
            $data
        );
        $this->dataProvider = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->config = $config;
        $this->facetedDataResolver = $facetedDataResolver;
        $this->collectionFilterApplier = $collectionFilterApplier
            ?? ObjectManager::getInstance()->get(CollectionFilterApplier::class);
        $this->searchEngine = $searchEngine
            ?? ObjectManager::getInstance()->get(SearchEngine::class);
    }

    /**
     * @inheritDoc
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (! $this->config->isModuleEnabled() || ! $request->getParam($this->_requestVar)) {
            return parent::apply($request);
        }

        $categoryId = $request->getParam($this->_requestVar) ?: $request->getParam('id');
        if (empty($categoryId)) {
            return $this;
        }

        $categoryIds = explode(',', $categoryId);
        $this->dataProvider
            ->setCanProceed(true)
            ->setCategoryIds($categoryIds);

        $categories = $this->dataProvider->getCategories();

        $this->addCategoriesFilter($this->getLayer()->getProductCollection(), $categories->getAllIds());

        foreach ($categories as $category) {
            if (in_array($category->getId(), $categoryIds, false)) {
                $this->getLayer()
                    ->getState()
                    ->addFilter(
                        $this->_createItem(
                            $category->getName(),
                            $category->getId()
                        )->setIsActive(true)
                    );
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function _getItemsData()
    {
        if (! $this->config->isModuleEnabled()) {
            return parent::_getItemsData();
        }

        $currentCategory = $this->getLayer()->getCurrentCategory();
        if (! $currentCategory->getIsActive()) {
            return $this->itemDataBuilder->build();
        }

        try {
            return $this->facetedDataResolver->resolve(
                'cat',
                $currentCategory,
                $this->getLayer()
            );
        } catch (LocalizedException $exception) {
            return parent::_getItemsData();
        }
    }

    /**
     * Filter products by categories
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param array                                                   $categoryIds
     * @return $this
     */
    protected function addCategoriesFilter(Collection $collection, array $categoryIds): Category
    {
        if ($this->searchEngine->isElasticSearch() || $this->searchEngine->isLiveSearch()) {
            $this->collectionFilterApplier->applyInCondition(
                $collection,
                'category_ids',
                $categoryIds
            );
            return $this;
        }

        $connection = $collection->getConnection();
        $categorySelect = $connection->select()->from(
            ['cat' => $collection->getTable('catalog_category_product_index')],
            'cat.product_id'
        )->where($connection->prepareSqlCondition('cat.category_id', ['in' => $categoryIds]))
            ->where($connection->prepareSqlCondition('cat.store_id', ['eq' => $this->getStoreId()]));

        $collection->getSelect()->where(
            $connection->prepareSqlCondition('e.entity_id', ['in' => $categorySelect])
        );
        return $this;
    }
}
