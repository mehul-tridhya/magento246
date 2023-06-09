<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
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
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Framework\Escaper;
use Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory;

class Category extends \Magento\CatalogSearch\Model\Layer\Filter\Category
{

    /**
     * @var Category
     */
    private $dataProvider;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CategoryResolver
     */
    private $facetedResolver;

    /**
     * @var mixed|CollectionFilterApplier
     */
    private $collectionApplier;

    /**
     * @var mixed|SearchEngine
     */
    private $searchEngine;

    /**
     * @param ItemFactory                  $filterItemFactory
     * @param StoreManagerInterface        $storeManager
     * @param Layer                        $layer
     * @param DataBuilder                  $itemDataBuilder
     * @param Escaper                      $escaper
     * @param CategoryFactory              $categoryProviderFactory
     * @param Elasticsearch                $elasticsearchHelper
     * @param Config                       $config
     * @param CategoryResolver             $facetedResolver
     * @param array                        $data
     * @param CollectionFilterApplier|null $collectionApplier
     * @param SearchEngine|null            $searchEngine
     */
    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        Escaper $escaper,
        CategoryFactory $categoryProviderFactory,
        Elasticsearch $elasticsearchHelper,
        Config $config,
        CategoryResolver $facetedResolver,
        array $data = [],
        CollectionFilterApplier $collectionApplier = null,
        SearchEngine $searchEngine = null
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $escaper,
            $categoryProviderFactory,
            $data
        );
        $this->dataProvider = $categoryProviderFactory->create(['layer' => $this->getLayer()]);
        $this->config = $config;
        $this->facetedResolver = $facetedResolver;
        $this->collectionApplier = $collectionApplier
            ?? ObjectManager::getInstance()->get(CollectionFilterApplier::class);
        $this->searchEngine = $searchEngine
            ?? ObjectManager::getInstance()->get(SearchEngine::class);
    }

    /**
     * @inheritDoc
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {

        $categoryId = $request->getParam($this->_requestVar) ?: $request->getParam('id');
        if (empty($categoryId)) {
            return $this;
        }

        $categoryIds = explode(',', $categoryId);
        $this->dataProvider
            ->setCanProceed(true)
            ->setCategoryIds($categoryIds);

        $categories = $this->dataProvider->getCategories();

        $this->addCategoriesFilterInCollection($this->getLayer()->getProductCollection(), $categories->getAllIds());

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
         * Filter products by categories
         *
         * @param  Collection $collection
         * @param  array      $categoryIds
         * @return $this
         */
    protected function addCategoriesFilterInCollection(Collection $collection, array $categoryIds): Category
    {
        if ($this->searchEngine->isElasticSearch() || $this->searchEngine->isLiveSearch()) {
            $this->collectionApplier->applyInCondition(
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

    /**
     * @inheritDoc
     */
    protected function _getItemsData()
    {

        $currentCategory = $this->getLayer()->getCurrentCategory();
        if (! $currentCategory->getIsActive()) {
            return $this->itemDataBuilder->build();
        }

        try {
            return $this->facetedResolver->resolve(
                'cat',
                $currentCategory,
                $this->getLayer()
            );
        } catch (LocalizedException $exception) {
            return parent::_getItemsData();
        }
    }
}
