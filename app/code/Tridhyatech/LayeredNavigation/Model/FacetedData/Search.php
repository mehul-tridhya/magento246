<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FacetedData;

use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\Search\SearchResultFactory;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Search\Request\EmptyRequestDataException;
use Magento\Search\Api\SearchInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\Search\Request\NonExistingRequestNameException;

class Search
{

    /**
     * @var SearchResultFactory
     */
    private $searchResultFactory;

    /**
     * @var SearchInterface
     */
    private $search;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchBuilderFactory;


    /**
     * @param SearchCriteriaBuilderFactory $searchBuilder
     * @param SearchInterface              $search
     * @param SearchResultFactory          $searchResultFactory
     */
    public function __construct(
        SearchCriteriaBuilderFactory $searchBuilder,
        SearchInterface $search,
        SearchResultFactory $searchResultFactory
    ) {
        $this->searchBuilderFactory = $searchBuilder;
        $this->search = $search;
        $this->searchResultFactory = $searchResultFactory;
    }

    /**
     * Search by filters.
     *
     * @param  Filter[] $filters
     * @return SearchResultInterface
     * @throws LocalizedException
     */
    public function searchProductsByFilter(array $filters): SearchResultInterface
    {
        $searchBuilder = $this->searchBuilderFactory->create();

        $isSearch = false;
        foreach ($filters as $filter) {
            if ($filter->getField() === 'search_term') {
                $isSearch = true;
            }
            $searchBuilder->addFilter($filter);
        }

        $searchCriteria = $searchBuilder->create();
        $searchCriteria->setRequestName($isSearch ? 'quick_search_container' : 'catalog_view_container');
        $searchCriteria->setSortOrders([]);

        try {
            return $this->search->search($searchCriteria);
        } catch (EmptyRequestDataException $e) {
            return $this->searchResultFactory->create()->setItems([]);
        } catch (NonExistingRequestNameException $e) {
            throw new LocalizedException(__('An error occurred. For details, see the error log.'));
        }
    }

    /**
     * Extract faced data from search results.
     *
     * @param  string                $field
     * @param  SearchResultInterface $searchResult
     * @return array
     * @throws StateException
     */
    private function extractFacedData(string $field, SearchResultInterface $searchResult): array
    {
        $result = [];
        $aggregations = $searchResult->getAggregations();

        // This behavior is for case with empty object when we got EmptyRequestDataException
        if (null !== $aggregations) {
            $bucket = $aggregations->getBucket($field . RequestGenerator::BUCKET_SUFFIX);
            if ($bucket) {
                foreach ($bucket->getValues() as $value) {
                    $metrics = $value->getMetrics();
                    $result[$metrics['value']] = $metrics;
                }
            } else if(!$bucket) {
                throw new StateException(__("The bucket doesn't exist."));
            }
        }
        return $result;
    }

    /**
     * Search faced data by filters.
     *
     * @param  string   $field
     * @param  Filter[] $filters
     * @return array
     * @throws LocalizedException
     * @throws StateException
     */
    public function search(string $field, array $filters): array
    {
        $searchResult = $this->searchProductsByFilter($filters);
        return $this->extractFacedData($field, $searchResult);
    }
}
