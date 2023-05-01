<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\SerializerInterface;
use Tridhyatech\LayeredNavigation\Helper\Config\Attribute as AttributeConfig;
use Tridhyatech\LayeredNavigation\Model\FilterList;

class Attribute extends AbstractAttribute
{

    /**
     * @var AttributeConfig
     */
    private $attributeConfig;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    private $serializer;

    /**
     * @param \Tridhyatech\LayeredNavigation\Model\FilterList $filterableAttributes
     * @param \Magento\Framework\App\ResourceConnection          $resourceConnection
     * @param \Magento\Backend\Block\Template\Context            $context
     * @param AttributeConfig                                    $attributeConfig
     * @param \Magento\Framework\Serialize\SerializerInterface   $serializer
     * @param array                                              $data
     */
    public function __construct(
        FilterList $filterableAttributes,
        ResourceConnection $resourceConnection,
        Context $context,
        AttributeConfig $attributeConfig,
        SerializerInterface $serializer,
        array $data = []
    ) {
        parent::__construct($filterableAttributes, $resourceConnection, $context, $data);
        $this->attributeConfig = $attributeConfig;
        $this->serializer = $serializer;
    }

    /**
     * Prepare selected attributes
     *
     * @return array
     */
    protected function _prepareValue()
    {
        $selectedAttrs = $this->attributeConfig->getRelatedConfig(AttributeConfig::XML_PATH_ATTRS);
        if (! $selectedAttrs) {
            return [];
        }
        return $this->serializer->unserialize($selectedAttrs);
    }

    /**
     * Get all attributes
     *
     * @return self
     */
    protected function _prepareAttributes()
    {
        $attributes = $this->_filterableAttributes->getFilters();
        $this->_active = array_flip(array_keys($this->_values));

        //Sort attributes by active and non-active
        //value can be an object or array
        foreach ($this->_values as $attrKey => $value) {
            if (!is_array($value)) {
                if (isset($attributes[$attrKey])) {
                    $this->_active[$attrKey] = $attributes[$attrKey];
                    unset($attributes[$attrKey]);
                }
            } else {
                $this->_active[$attrKey] = [
                    'group' => true,
                    'attributes' => []
                ];
                foreach ($value as $_attrKey => $item) {

                    $this->_active[$attrKey]['attributes'][$_attrKey] = $attributes[$_attrKey];
                    unset($attributes[$_attrKey]);
                }
            }
        }

        $this->_notActive = $attributes;

        return $this;
    }
}
