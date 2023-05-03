<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Model\Catalog\Layer;

use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Helper\Config\Attribute;
use Magento\Catalog\Model\Layer\FilterList as modelFilterList;

class FilterList
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Attribute
     */
    private $attributeConfig;

    /**
     * @param Config    $config
     * @param Attribute $attributeConfig
     */
    public function __construct(
        Config $config,
        Attribute $attributeConfig
    ) {
        $this->config = $config;
        $this->attributeConfig = $attributeConfig;
    }

    /**
     * Around get filter
     *
     * @param  modelFilterList              $filterList
     * @param  \Closure                     $result
     * @param  \Magento\Catalog\Model\Layer $layer
     * @return array
     */
    public function aroundGetFilters(
        modelFilterList $filterList,
        $result,
        \Magento\Catalog\Model\Layer $layer
    ) {
        if (!$this->config->isModuleEnabled()) {
            return $result($layer);
        }

        $filters = $result($layer);

        //Remove first element from array, because category filter is first
        if (!$this->attributeConfig->isCategoryFilterEnabled()) {
            unset($filters[0]);
        }

        return $this->_sortFilters($filters);
    }

    /**
     * Sort filters
     *
     * @param  array $filters
     * @return array
     */
    protected function _sortFilters(array $filters): array
    {
        $selectedAttributeCodes = $this->attributeConfig->getSelectedAttributeCodes();

        $sortedFilters = [];
        foreach ($filters as $filter) {
            if (
                $filter instanceof \Magento\Catalog\Model\Layer\Filter\Category
                || $filter instanceof \Magento\CatalogSearch\Model\Layer\Filter\Category
            ) {
                $_code = 'category';
            } elseif ($filter->getData('attribute_model')) {
                $_code = $filter->getAttributeModel()->getAttributeCode();
            } elseif ($filter->getLayer()->getCode()) {
                $_code = $filter->getLayer()->getCode();
            } else {
                $_code = $filter->getRequestVar();
            }

            if (in_array($_code, $selectedAttributeCodes, true)) {
                $position =  array_search($_code, $selectedAttributeCodes, true);
                $sortedFilters[$position] = $filter->setPfAttributeCode($_code);
            }
        }

        ksort($sortedFilters);
        return $sortedFilters;
    }
}
