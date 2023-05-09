<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Plugin\Elasticsearch;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Helper\SearchEngine;
use Tridhyatech\LayeredNavigation\Model\CatalogSearch\Model\ResourceModel\Fulltext\Collection\CurrentLoading;
use Magento\Catalog\Model\ResourceModel\Product\Collection as productCollection;
use ReflectionObject;

/**
 * Temporary fix for
 *
 * @link https://github.com/magento/magento2/issues/28919
 * TODO: remove after left support magento version with bug
 *
 */
class FixMaxAndMinPrices
{
    /**
     * @var CurrentLoading
     */
    private $currentLoadingCollection;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var SearchEngine
     */
    private $searchEngine;

    /**
     * @param CurrentLoading $currentLoadingCollection
     * @param Config         $config
     * @param SearchEngine   $searchEngine
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
     * @param Collection $subject
     * @param bool       $printQuery
     * @param bool       $logQuery
     */
    public function beforeLoad(
        Collection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        if (!$this->needFix()) {
            return;
        }

        $this->currentLoadingCollection->set($subject);
    }

    /**
     * Fix min and max prices.
     *
     * @param  Collection        $subject
     * @param  productCollection $result
     * @param  bool              $printQuery
     * @param  bool              $logQuery
     * @return mixed
     */
    public function afterLoad(
        Collection $subject,
        $result,
        $printQuery = false,
        $logQuery = false
    ) {
        if ($this->currentLoadingCollection->getPriceData()) {
            $priceData = $this->currentLoadingCollection->getPriceData();
            $this->setPropertyValue($subject, '_maxPrice', (float) ($priceData['max'] ?? 0));
            $this->setPropertyValue($subject, '_minPrice', (float) ($priceData['min'] ?? 0));
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
        return $this->searchEngine->isElasticSearch();
    }

    /**
     * Set private property.
     *
     * @param Collection $subject
     * @param string     $propertyName
     * @param float      $value
     */
    private function setPropertyValue(Collection $subject, string $propertyName, float $value)
    {
        try {
            $reflectionSubject = new ReflectionObject($subject);
            $reflectionProperty = $reflectionSubject->getProperty($propertyName);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($subject, $value);
            $reflectionProperty->setAccessible(false);
        } catch (\ReflectionException $e) {
            return;
        }
    }
}
