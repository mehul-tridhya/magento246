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

        $filters = $result($layer);

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

        $sortedFilters = [];
        foreach ($filters as $filter) {
            if ($filter instanceof \Magento\Catalog\Model\Layer\Filter\Category
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

            $sortedFilters[] = $filter->setPfAttributeCode($_code);
        }

        ksort($sortedFilters);
        return $sortedFilters;
    }
}
