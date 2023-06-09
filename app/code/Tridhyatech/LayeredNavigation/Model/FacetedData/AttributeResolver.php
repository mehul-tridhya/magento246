<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FacetedData;

use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Framework\Api\Filter;
use Magento\Catalog\Model\Layer;
use Magento\Framework\Filter\StripTags;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\StateException;
use Tridhyatech\LayeredNavigation\Model\Catalog\Layer\Filter\Item;
use Magento\Framework\Exception\NoSuchEntityException;

class AttributeResolver
{

    /**
     * @var StripTags
     */
    private $tagFilter;

    /**
     * @var DataBuilder
     */
    private $itemDataBuilder;

    /**
     * @var Search
     */
    private $search;

    /**
     * @var GetLayerFilters
     */
    private $getLayerFilters;

    /**
     * @param DataBuilder     $itemDataBuilder
     * @param StripTags       $tagFilter
     * @param GetLayerFilters $getLayerFilters
     * @param Search          $search
     */
    public function __construct(
        DataBuilder $itemDataBuilder,
        StripTags $tagFilter,
        GetLayerFilters $getLayerFilters,
        Search $search
    ) {
        $this->tagFilter = $tagFilter;
        $this->search = $search;
        $this->getLayerFilters = $getLayerFilters;
        $this->itemDataBuilder = $itemDataBuilder;
    }

    /**
     * Resolve faced data for attribute with ability to choose other attribute values.
     *
     * @param  string    $requestVar
     * @param  Attribute $attribute
     * @param  Layer     $layer
     * @param  bool      $isFilterableAttribute
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @throws StateException
     */
    public function resolve(
        string $requestVar,
        Attribute $attribute,
        Layer $layer,
        bool $isFilterableAttribute
    ): array {
        $filters = $layer->getState()->getFilters();
        $attrFilterItems = [];
        foreach ($filters as $filterItem) {
            if ($requestVar === $filterItem->getFilter()->getRequestVar()) {
                $attrFilterItems[] = $filterItem;
            }
        }

        if ($attrFilterItems) {
            return $this->getItemsData(
                $attribute,
                $layer,
                $isFilterableAttribute,
                $attrFilterItems
            );
        }

        throw new LocalizedException(__('Do not need custom faced data resolving.'));
    }

    /**
     * Return field faceted data from faceted search result
     *
     * @param  string $field
     * @param  Layer  $layer
     * @return array
     * @throws StateException
     * @throws LocalizedException
     */
    public function getFacetedData(string $field, Layer $layer): array
    {
        $filters = $this->getLayerFilters->execute($layer);
        $otherFilters = array_filter(
            $filters,
            static function (Filter $filter) use ($field) {
                return $field !== $filter->getField();
            }
        );

        return $this->search->search($field, $otherFilters);
    }

    /**
     * Get data array for building attribute filter items
     *
     * @param  Attribute $attribute
     * @param  Layer     $layer
     * @param  bool      $isFilterableAttribute
     * @param  Item[]    $attrFilterItems
     * @return array
     * @throws LocalizedException
     * @throws StateException
     */
    protected function getItemsData(
        Attribute $attribute,
        Layer $layer,
        bool $isFilterableAttribute,
        array $attrFilterItems
    ): array {
        $optionsFacetedData = $this->getFacetedData($attribute->getAttributeCode(), $layer);
        if (!$isFilterableAttribute && count($optionsFacetedData) === 0) {
            return $this->itemDataBuilder->build();
        }

        foreach ($attribute->getFrontend()->getSelectOptions() as $option) {
            $this->buildOptionData($option, $isFilterableAttribute, $optionsFacetedData, $attrFilterItems);
        }
        return $this->itemDataBuilder->build();
    }

    /**
     * Retrieve option value if it exists
     *
     * @param  array $option
     * @return bool|string
     */
    private function getOptionValue(array $option)
    {
        if (empty($option['value']) && !is_numeric($option['value'])) {
            return false;
        }
        return $option['value'];
    }

    /**
     * Build option data
     *
     * @param  array  $option
     * @param  bool   $isFilterableAttribute
     * @param  array  $optionsFacetedData
     * @param  Item[] $attrFilterItems
     * @return void
     * @throws LocalizedException
     */
    private function buildOptionData(
        array $option,
        bool $isFilterableAttribute,
        array $optionsFacetedData,
        array $attrFilterItems
    ): void {
        $value = $this->getOptionValue($option);
        if ($value === false) {
            return;
        }

        $count = $this->getOptionCount($value, $optionsFacetedData);
        if ($isFilterableAttribute && ($count === 0 && !$this->isActiveFilter($value, $attrFilterItems))) {
            return;
        }

        $this->itemDataBuilder->addItemData(
            $this->tagFilter->filter($option['label']),
            $value,
            $count
        );
    }

    /**
     * Check if option is selected.
     *
     * @param  int|string $value
     * @param  Item[]     $attrFilterItems
     * @return bool
     * @throws LocalizedException
     */
    private function isActiveFilter($value, array $attrFilterItems): bool
    {
        foreach ($attrFilterItems as $filterItem) {
            if ((string) $value === (string) $filterItem->getValueString()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve count of the options
     *
     * @param  int|string $value
     * @param  array      $optionsFacetedData
     * @return int
     */
    private function getOptionCount($value, array $optionsFacetedData): int
    {
        return isset($optionsFacetedData[$value]['count'])
            ? (int) $optionsFacetedData[$value]['count']
            : 0;
    }
}
