<?php
/**
 * @package     Plumrocket_ProductFilter
 * @copyright   Copyright (c) 2016 Tridhyatech Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Tridhyatech\LayeredNavigation\Model\Catalog\Layer\Filter\Price;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\Flat\Collection as FlatCategoryCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Tridhyatech\LayeredNavigation\Helper\Config;

/**
 * @since 1.0.0
 */
class Range extends \Magento\Catalog\Model\Layer\Filter\Price\Range
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @param \Tridhyatech\LayeredNavigation\Helper\Config    $config
     * @param \Magento\Framework\Registry                        $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Catalog\Model\Layer\Resolver              $layerResolver
     */
    public function __construct(
        Config $config,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        Resolver $layerResolver
    ) {
        $this->config = $config;
        $this->registry = $registry;
        parent::__construct($registry, $scopeConfig, $layerResolver);
    }

    /**
     * @inheritDoc
     */
    public function getPriceRange()
    {
        if (! $this->config->isModuleEnabled()) {
            return parent::getPriceRange();
        }

        $categories = $this->registry->registry('current_category_filter');
        if ($categories instanceof CategoryCollection || $categories instanceof FlatCategoryCollection) {
            return $categories->getFirstItem()->getFilterPriceRange();
        }

        return parent::getPriceRange();
    }
}
