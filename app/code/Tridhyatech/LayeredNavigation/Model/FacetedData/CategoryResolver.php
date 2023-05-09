<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FacetedData;

use Magento\Framework\Api\Filter;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer;
use Magento\Framework\Escaper;
use Magento\Framework\Filter\StripTags;
use Tridhyatech\LayeredNavigation\Model\FacetedData\Search;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\StateException;
use Tridhyatech\LayeredNavigation\Model\Catalog\Layer\Filter\Item;
use Tridhyatech\LayeredNavigation\Model\FacetedData\GetLayerFilters;
use Magento\Framework\Exception\LocalizedException;

class CategoryResolver
{

    /**
     * @var CollectionFactory
     */
    private $categoryFactory;
    
    /**
     * @var DataBuilder
     */
    private $itemDataBuilder;

    /**
     * @var StripTags
     */
    private $tagFilter;

    /**
     * @var GetLayerFilters
     */
    private $getLayerFilters;

    /**
     * @var Search
     */
    private $search;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param DataBuilder       $itemDataBuilder
     * @param StripTags         $tagFilter
     * @param CollectionFactory $categoryFactory
     * @param Escaper           $escaper
     * @param GetLayerFilters   $getLayerFilters
     * @param Search            $search
     */
    public function __construct(
        DataBuilder $itemDataBuilder,
        StripTags $tagFilter,
        CollectionFactory $categoryFactory,
        Escaper $escaper,
        GetLayerFilters $getLayerFilters,
        Search $search
    ) {
        $this->itemDataBuilder = $itemDataBuilder;
        $this->tagFilter = $tagFilter;
        $this->categoryFactory = $categoryFactory;
        $this->escaper = $escaper;
        $this->getLayerFilters = $getLayerFilters;
        $this->search = $search;
    }

    /**
     * Return field faceted data from faceted search result
     *
     * @param  string $field
     * @param  Layer  $layer
     * @return array
     * @throws LocalizedException
     * @throws StateException
     */
    public function getFacetedData(string $field, Layer $layer): array
    {
        $filters = $this->getLayerFilters->execute($layer);
        $otherFilters = array_filter(
            $filters,
            static function (Filter $filter) {
                return ! ($filter->getField() === 'category_ids' && is_array($filter->getValue()));
            }
        );
        return $this->search->search($field, $otherFilters);
    }

    /**
     * Resolve faced data for attribute with ability to choose other attribute values.
     *
     * @param  string   $requestVar
     * @param  Category $category
     * @param  Layer    $layer
     * @return array
     * @throws LocalizedException
     * @throws StateException
     */
    public function resolve(
        string $requestVar,
        Category $category,
        Layer $layer
    ): array {
        $filters = $layer->getState()->getFilters();
        $categoryFilterItems = [];
        foreach ($filters as $filterItem) {
            if ($requestVar === $filterItem->getFilter()->getRequestVar()) {
                $categoryFilterItems[] = $filterItem;
            }
        }

        return $this->getItemsData($category, $layer, $categoryFilterItems);
    }

    /**
     * Get data array for building attribute filter items
     *
     * @param  Category $category
     * @param  Layer    $layer
     * @param  Item[]   $attrFilterItems
     * @return array
     * @throws LocalizedException
     * @throws StateException
     */
    protected function getItemsData(
        Category $category,
        Layer $layer,
        array $attrFilterItems
    ): array {
        $options = $this->getChildrenCategoriesOptions($category);
        $optionsFacetedData = $this->getFacetedData('category', $layer);

        foreach ($options as $option) {
            $this->buildOptionData($option, $optionsFacetedData, $attrFilterItems);
        }

        return $this->itemDataBuilder->build();
    }

    /**
     * Build option data
     *
     * @param  array  $option
     * @param  array  $optionsFacetedData
     * @param  Item[] $attrFilterItems
     * @return void
     */
    protected function buildOptionData(
        array $option,
        array $optionsFacetedData,
        array $attrFilterItems
    ): void {
        // todo: do not remove all categories from filters, only children
        $value = $this->getOptionValue($option);
        if ($value === false) {
            return;
        }
        $count = $this->getOptionCount($value, $optionsFacetedData);

        if ($count === 0 && !$this->isActiveFilter($value, $attrFilterItems)) {
            return;
        }

        $this->itemDataBuilder->addItemData(
            $this->tagFilter->filter($option['label']),
            $value,
            $count
        );
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
     * Return child categories
     *
     * @param  Category $category
     * @return array[]
     * @throws LocalizedException
     */
    protected function getChildrenCategoriesOptions(Category $category): array
    {
        /* @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->categoryFactory->create();

        $collection->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('all_children')
            ->addAttributeToSelect('is_anchor')
            ->addAttributeToFilter('is_active', 1)
            ->addIdFilter($category->getChildren())
            ->setOrder(
                'position',
                \Magento\Framework\DB\Select::SQL_ASC
            );

        $options = [];
        /**
         * @var Category $childCategory
        */
        foreach ($collection->getItems() as $childCategory) {
            if (! $childCategory->getIsActive()) {
                continue;
            }

            $options[] = [
                'value' => $childCategory->getId(),
                'label' => $this->escaper->escapeHtml($childCategory->getName()),
            ];
        }

        return $options;
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

    /**
     * Check if option is selected.
     *
     * @param  int|string $value
     * @param  Item[]     $attrFilterItems
     * @return bool
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
}
