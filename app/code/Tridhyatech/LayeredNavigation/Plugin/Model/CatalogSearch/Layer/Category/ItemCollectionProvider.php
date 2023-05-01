<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Model\CatalogSearch\Layer\Category;

/**
 * @since 1.0.0
 */
class ItemCollectionProvider
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    protected $config;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\ResourceModel\Product\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Tridhyatech\LayeredNavigation\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Tridhyatech\LayeredNavigation\Helper\Config                                 $config
     */
    public function __construct(
        \Tridhyatech\LayeredNavigation\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Tridhyatech\LayeredNavigation\Helper\Config $config
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->config = $config;
    }

    /**
     * Around get collection
     *
     * @param \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $provider
     * @param \Closure                                                           $result
     * @param \Magento\Catalog\Model\Category                                    $category
     * @return array
     */
    public function aroundGetCollection(
        \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $provider,
        $result,
        \Magento\Catalog\Model\Category $category
    ) {

        if ($this->config->isModuleEnabled()) {
            $collection = $this->collectionFactory->create();
            $collection->addCategoryFilter($category);
            return $collection;
        }

        return $result($category);
    }
}
