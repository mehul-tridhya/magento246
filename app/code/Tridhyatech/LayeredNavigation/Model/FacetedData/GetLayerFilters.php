<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FacetedData;

use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\Layer;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Filter;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use ReflectionObject;
use ReflectionException;

class GetLayerFilters
{
    /**
     * Get Search Criteria Builder from product collection.
     *
     * @param  Layer $layer
     * @return Filter[]
     * @throws LocalizedException
     */
    public function execute(Layer $layer): array
    {
        try {
            /**
             * @var Collection $productCollection 
            */
            $productCollection = $layer->getProductCollection();
            $reflectionSubject = new ReflectionObject($productCollection);
            $reflectionProperty = $reflectionSubject->getParentClass()->getProperty('searchCriteriaBuilder');
            $reflectionProperty->setAccessible(true);
            /**
             * @var SearchCriteriaBuilder $searchCriteriaBuilder 
            */
            $searchCriteriaBuilder = clone $reflectionProperty->getValue($productCollection);
            $reflectionProperty->setAccessible(false);

            $reflectionSubject = new ReflectionObject($searchCriteriaBuilder);
            $reflectionProperty = $reflectionSubject->getProperty('filters');
            $reflectionProperty->setAccessible(true);
            $filters = $reflectionProperty->getValue($searchCriteriaBuilder);
            $reflectionProperty->setAccessible(false);
        } catch (ReflectionException $e) {
            throw new LocalizedException(__('Cannot get existing filters'));
        }
        return $filters;
    }
}
