<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Catalog\Layer\Filter\Price;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\Flat\Collection as FlatCategoryCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;
use Tridhyatech\LayeredNavigation\Helper\Config;

class Range extends \Magento\Catalog\Model\Layer\Filter\Price\Range
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @param Config               $config
     * @param Registry             $registry
     * @param ScopeConfigInterface $scopeConfig
     * @param Resolver             $layerResolver
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
