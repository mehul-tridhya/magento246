<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\ObjectManagerInterface;
use Tridhyatech\LayeredNavigation\Helper\SearchEngine;

class CollectionFilterApplier
{

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var SearchEngine
     */
    private $searchEngine;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param SearchEngine           $searchEngine
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        SearchEngine $searchEngine
    ) {
        $this->objectManager = $objectManager;
        $this->searchEngine = $searchEngine;
    }

    /**
     * Apply filter to collection.
     *
     * @param  Collection $collection
     * @param  string     $code
     * @param  array      $values
     * @return void
     */
    public function applyInCondition(Collection $collection, string $code, array $values): void
    {
        if (!$values) {
            return;
        }

        if ($this->searchEngine->isLiveSearch()) {
            $this->fixForLiveSearch($code, $values);
            return;
        }

        $collection->addFieldToFilter($code, ['in' => $values]);
    }

    /**
     * Fix for Magento_LiveSearch
     *
     * Live Search cannot parse default collection 'in' condition,
     * so we add it to the search criteria with proper format.
     *
     * @param  string $code
     * @param  array  $values
     * @return void
     */
    private function fixForLiveSearch(string $code, array $values): void
    {
        // Use object manager because it doesn't work if we add them to the __construct.
        $searchBuilder = $this->objectManager->get(SearchCriteriaBuilder::class);
        $filterBuilder = $this->objectManager->get(FilterBuilder::class);

        $filterBuilder->setField($code);
        if (count($values) > 1) {
            $filterBuilder->setValue(array_values($values));
            $filterBuilder->setConditionType('in');
        } elseif (count($values) <= 1) {
            $filterBuilder->setValue(array_values($values)[0]);
        }
        $searchBuilder->addFilter($filterBuilder->create());
    }
}
