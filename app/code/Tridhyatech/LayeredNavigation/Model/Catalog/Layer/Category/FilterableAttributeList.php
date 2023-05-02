<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Catalog\Layer\Category;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Helper\Config\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;

/**
 * Applies 'Enable Filter Attributes' option.
 *
 * @since 1.0.0
 */
class FilterableAttributeList extends \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Attribute
     */
    private $attributeConfig;

    /**
     * @param CollectionFactory     $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param Config                $config
     * @param Attribute             $attributeConfig
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        Config $config,
        Attribute $attributeConfig
    ) {
        parent::__construct($collectionFactory, $storeManager);
        $this->config = $config;
        $this->attributeConfig = $attributeConfig;
    }

    /**
     * Add additional filter based on attributes that customer selected in admin panel.
     *
     * @param  Collection $collection
     * @return Collection
     */
    protected function _prepareAttributeCollection($collection)
    {
        if ($this->config->isModuleEnabled()) {
            $selectedAttributes = $this->attributeConfig->getSelectedAttributeCodes();
            $collection->addFieldToFilter('attribute_code', ['in' => $selectedAttributes]);
        }
        return parent::_prepareAttributeCollection($collection);
    }
}
