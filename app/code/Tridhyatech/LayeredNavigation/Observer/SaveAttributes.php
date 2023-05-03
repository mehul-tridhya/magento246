<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Observer;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Tridhyatech\LayeredNavigation\Helper\Config\Attribute as tridhyaAttribute;

/**
 * @since 1.0.0
 */
class SaveAttributes implements ObserverInterface
{

    /**
     * @var FilterableAttributeList
     */
    private $_filterableAttributes;

    /**
     * @var tridhyaAttribute
     */
    private $attributeConfig;

    /**
     * @param CollectionFactory $filterableAttributes
     * @param tridhyaAttribute  $attributeConfig
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $filterableAttributes,
        tridhyaAttribute $attributeConfig
    ) {
        $this->_filterableAttributes = $filterableAttributes;
        $this->attributeConfig = $attributeConfig;
    }

    /**
     * Changing attribute values
     *
     * @param  \Magento\Framework\Event\Observer $observer
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
