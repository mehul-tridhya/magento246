<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Block\Swatches\LayeredNavigation;

use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Option;
use Tridhyatech\LayeredNavigation\Api\ItemUrlBuilderInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\Variable\Value\Slugify;

class RenderLayered extends \Magento\Swatches\Block\LayeredNavigation\RenderLayered
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @var \Tridhyatech\LayeredNavigation\Api\ItemUrlBuilderInterface
     */
    private $filterItemUrlBuilder;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Value\Slugify
     */
    private $slugify;

    /**
     * @param \Magento\Framework\View\Element\Template\Context                    $context
     * @param \Magento\Eav\Model\Entity\Attribute                                 $eavAttribute
     * @param \Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory  $layerAttribute
     * @param \Magento\Swatches\Helper\Data                                       $swatchHelper
     * @param \Magento\Swatches\Helper\Media                                      $mediaHelper
     * @param \Tridhyatech\LayeredNavigation\Helper\Config                     $config
     * @param \Tridhyatech\LayeredNavigation\Api\ItemUrlBuilderInterface $filterItemUrlBuilder
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Value\Slugify      $slugify
     * @param array                                                               $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        Config $config,
        ItemUrlBuilderInterface $filterItemUrlBuilder,
        Slugify $slugify,
        array $data = []
    ) {
        $this->config = $config;
        $this->filterItemUrlBuilder = $filterItemUrlBuilder;
        parent::__construct(
            $context,
            $eavAttribute,
            $layerAttribute,
            $swatchHelper,
            $mediaHelper,
            $data
        );
        $this->slugify = $slugify;
    }

    /**
     * Get attribute swatch data
     *
     * @return array
     */
    public function getSwatchData()
    {
        $data = parent::getSwatchData();
        if (! $this->config->isModuleEnabled()) {
            return $data;
        }

        $attributeOptions = $data['options'];
        $swatches = $data['swatches'];

        $newAttributeOptions = [];
        $newSwatches = [];

        foreach ($this->eavAttribute->getOptions() as $option) {
            if (isset($attributeOptions[$option->getValue()])) {
                $newAttributeOptions[$this->slugify->execute($option->getValue())]
                    = $attributeOptions[$option->getValue()];
            }
            if (isset($swatches[$option->getValue()])) {
                $newSwatches[$this->slugify->execute($option->getValue())]
                    = $swatches[$option->getValue()];
            }
        }

        $data['options'] = $newAttributeOptions;
        $data['swatches'] = $newSwatches;

        return $data;
    }

    /**
     * Build filter option url.
     *
     * @param string $attributeCode
     * @param string $optionId
     * @return string
     */
    public function buildUrl($attributeCode, $optionId)
    {
        if ($this->config->isModuleEnabled()) {
            return $this->filterItemUrlBuilder->toggleFilterUrl($attributeCode, (string) $optionId);
        }
        return parent::buildUrl($attributeCode, $optionId);
    }

    /**
     * @inheritDoc
     */
    protected function getOptionViewData(FilterItem $filterItem, Option $swatchOption)
    {
        if (! $this->config->isModuleEnabled()) {
            return parent::getOptionViewData($filterItem, $swatchOption);
        }

        if ($this->isOptionDisabled($filterItem)) {
            $link = 'javascript:void();';
            $style = 'disabled';
        } else {
            $style = '';
            $link = $this->buildUrl($this->eavAttribute->getAttributeCode(), $filterItem->getValueString());
        }
        return [
            'link' => $link,
            'custom_style' => $style,
            'label' => $swatchOption->getLabel()
        ];
    }
}
