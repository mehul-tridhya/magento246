<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Plugin\Elasticsearch;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Helper\SearchEngine;
use Tridhyatech\LayeredNavigation\Model\CatalogSearch\Model\ResourceModel\Fulltext\Collection\CurrentLoading;

/**
 * Temporary fix for
 * @link https://github.com/magento/magento2/issues/28919
 * TODO: remove after left support magento version with bug
 *
 * @since 1.0.0
 */
class FixMaxAndMinPrices
{
    /**
     * @var \Tridhyatech\LayeredNavigation\Model\CatalogSearch\Model\ResourceModel\Fulltext\Collection\CurrentLoading
     */
    private $currentLoadingCollection;

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\SearchEngine
     */
    private $searchEngine;

    /**
     * @param CurrentLoading                                        $currentLoadingCollection
     * @param \Tridhyatech\LayeredNavigation\Helper\Config       $config
     * @param \Tridhyatech\LayeredNavigation\Helper\SearchEngine $searchEngine
     */
    public function __construct(
        CurrentLoading $currentLoadingCollection,
        Config $config,
        SearchEngine $searchEngine
    ) {
        $this->currentLoadingCollection = $currentLoadingCollection;
        $this->config = $config;
        $this->searchEngine = $searchEngine;
    }

    /**
     * Fix min and max prices.
     *
     * @param \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $subject
     * @param bool                                                           $printQuery
     * @param bool                                                           $logQuery
     */
    public function beforeLoad(
        Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        if (! $this->needFix()) {
            return;
        }

        $this->currentLoadingCollection->set($subject);
    }

    /**
     * Fix min and max prices.
     *
     * @param \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection        $result
     * @param bool                                                           $printQuery
     * @param bool                                                           $logQuery
     * @return mixed
     */
    public function afterLoad(
        Collection $subject,
        $result,
        $printQuery = false,
        $logQuery = false
    ) {
        if ($priceData = $this->currentLoadingCollection->getPriceData()) {
            $this->setPropertyValue($subject, '_maxPrice', (double) ($priceData['max'] ?? 0));
            $this->setPropertyValue($subject, '_minPrice', (double) ($priceData['min'] ?? 0));
        }

        $this->currentLoadingCollection->reset();
        return $result;
    }

    /**
     * Check if we need to apply fix.
     *
     * @return bool
     */
    public function needFix(): bool
    {
        return $this->config->isModuleEnabled() && $this->searchEngine->isElasticSearch();
    }

    /**
     * Set private property.
     *
     * @param \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $subject
     * @param string                                                         $propertyName
     * @param float                                                          $value
     */
    private function setPropertyValue(Collection $subject, string $propertyName, float $value)
    {
        try {
            $reflectionSubject = new \ReflectionObject($subject);
            $reflectionProperty = $reflectionSubject->getProperty($propertyName);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($subject, $value);
            $reflectionProperty->setAccessible(false);
        } catch (\ReflectionException $e) {
            return;
        }
    }
}
