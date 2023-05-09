<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Model\CatalogSearch\Layer\Category;

use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\ResourceModel\Product\CollectionFactory;
use Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider as modelCollectionProvider;

class ItemCollectionProvider
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Config            $config
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Config $config
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
    }

    /**
     * Around get collection
     *
     * @param  modelCollectionProvider         $provider
     * @param  \Closure                        $result
     * @param  \Magento\Catalog\Model\Category $category
     * @return array
     */
    public function aroundGetCollection(
        modelCollectionProvider $provider,
        $result,
        \Magento\Catalog\Model\Category $category
    ) {

        $collection = $this->collectionFactory->create();
        $collection->addCategoryFilter($category);
        return $collection;
    }
}
