<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Observer;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Event\ObserverInterface;

/**
 * @since 1.0.0
 */
class SaveAttributes implements ObserverInterface
{

    /**
     * @var \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
     */
    private $_filterableAttributes;

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config\Attribute
     */
    private $attributeConfig;

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $filterableAttributes
     * @param \Tridhyatech\LayeredNavigation\Helper\Config\Attribute                $attributeConfig
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $filterableAttributes,
        \Tridhyatech\LayeredNavigation\Helper\Config\Attribute $attributeConfig
    ) {
        $this->_filterableAttributes = $filterableAttributes;
        $this->attributeConfig = $attributeConfig;
    }

    /**
     * Changing attribute values
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $this->_filterableAttributes->create();
        $collection->setItemObjectClass(Attribute::class)
            ->setOrder('position', 'ASC');
        $activeAttrs = $this->attributeConfig->getSelectedAttributeCodes();
        $collection->addFieldToFilter('attribute_code', ['in' => $activeAttrs]);
        $showEmpty = $this->attributeConfig->shouldShowEmpty();

        foreach ($collection as $attribute) {
            $save = false;
            $isFilterable = $showEmpty ? 2 : 1;
            if ($attribute->getIsFilterable() != $isFilterable) {
                $attribute->setIsFilterable($isFilterable)
                    ->setIsFilterableInSearch(1);
                $save = true;
            }

            if (!$attribute->getIsFilterableInSearch()) {
                $attribute->setIsFilterableInSearch(1);
                $save = true;
            }

            if ($save) {
                $attribute->save();
            }
        }
    }
}
