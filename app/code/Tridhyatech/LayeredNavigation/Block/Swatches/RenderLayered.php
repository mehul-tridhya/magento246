<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Block\Swatches;

use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\Entity\Attribute\Option;
use Tridhyatech\LayeredNavigation\Api\ItemUrlBuilderInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\Variable\Value\Slugify;
use Magento\Framework\View\Element\Template\Context;
use Magento\Swatches\Helper\Data;
use Magento\Swatches\Helper\Media;

class RenderLayered extends \Magento\Swatches\Block\LayeredNavigation\RenderLayered
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ItemUrlBuilderInterface
     */
    private $filterItemUrl;

    /**
     * @var Slugify
     */
    private $slugify;

    /**
     * @param Context                 $context
     * @param Attribute               $eavAttribute
     * @param AttributeFactory        $layerAttribute
     * @param Data                    $swatchHelper
     * @param Media                   $mediaHelper
     * @param Config                  $config
     * @param ItemUrlBuilderInterface $filterItemUrl
     * @param Slugify                 $slugify
     * @param array                   $data
     */
    public function __construct(
        Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        Data $swatchHelper,
        Media $mediaHelper,
        Config $config,
        ItemUrlBuilderInterface $filterItemUrl,
        Slugify $slugify,
        array $data = []
    ) {
        $this->config = $config;
        $this->filterItemUrl = $filterItemUrl;
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
     * @inheritDoc
     */
    protected function getOptionViewData(FilterItem $filterItem, Option $swatchOption)
    {

        if ($this->isOptionDisabled($filterItem)) {
            $link = 'javascript:void();';
            $style = 'disabled';
        } elseif (!$this->isOptionDisabled($filterItem)) {
            $style = '';
            $link = $this->buildUrl($this->eavAttribute->getAttributeCode(), $filterItem->getValueString());
        }
        return [
            'link' => $link,
            'custom_style' => $style,
            'label' => $swatchOption->getLabel()
        ];
    }

    /**
     * Get attribute swatch data
     *
     * @return array
     */
    public function getSwatchData()
    {
        $data = parent::getSwatchData();

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
     * @param  string $attributeCode
     * @param  string $optionId
     * @return string
     */
    public function buildUrl($attributeCode, $optionId)
    {
        return $this->filterItemUrl->toggleUrl($attributeCode, (string) $optionId);
    }
}
