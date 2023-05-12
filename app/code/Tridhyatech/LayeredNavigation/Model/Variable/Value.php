<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Variable;

use Magento\Framework\Exception\NoSuchEntityException;
use Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface;
use Tridhyatech\LayeredNavigation\Model\Variable\Value\Slugify;
use Tridhyatech\LayeredNavigation\Model\Variable\Value\UrlInterface;

class Value
{

    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @var UrlInterface
     */
    private $urlValue;

    /**
     * @var Slugify
     */
    private $slugify;

    /**
     * @param FilterRepositoryInterface $filterRepository
     * @param UrlInterface              $urlValue
     * @param Slugify                   $slugify
     */
    public function __construct(
        FilterRepositoryInterface $filterRepository,
        UrlInterface $urlValue,
        Slugify $slugify
    ) {
        $this->filterRepository = $filterRepository;
        $this->urlValue = $urlValue;
        $this->slugify = $slugify;
    }

    /**
     * Prepare all values for array of variables.
     *
     * @param  array $variables
     * @return array
     */
    public function prepareVariableValues(array $variables): array
    {
        $result = [];
        foreach ($variables as $code => $values) {
            $result[$code] = $this->prepareValues($code, $values);
        }
        return $result;
    }

    /**
     * Prepare all values for one variable.
     *
     * @param  string $code
     * @param  array  $values
     * @return array
     */
    private function prepareValues(string $code, array $values): array
    {
        $preparedValues = [];
        foreach ($values as $value) {
            $preparedValues[] = $this->prepareValue($code, $value);
        }
        return $preparedValues;
    }

    /**
     * Prepare value to default magento format.
     *
     * Can use one of these methods:
     *  1. Convert attribute option label to its id
     *  2. Convert price format
     *  3. Convert category key to its id
     *  4. Some specific fixes for custom filters
     *
     * @param  string $code
     * @param  string $value
     * @return string|null
     */
    private function prepareValue(string $code, string $value): ?string
    {
        $value = html_entity_decode($value);

        try {
            $filterMeta = $this->filterRepository->get($code);
            if ($filterMeta->isAttribute() || $filterMeta->isCategory() || $filterMeta->isSpecial()) {
                $value = $this->slugify->execute($value);
                return $this->urlValue->decode($code, $value);
            }
            return $value;
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Prepare price values.
     *
     * @param  array $variables
     * @return array
     */
    public function preparePriceValues(array $variables): array
    {
        if (isset($variables['price'])) {
            $variables['price'] = array_map(
                static function ($value) {
                    return str_replace('_', '-', $value);
                },
                $variables['price']
            );
        }

        return $variables;
    }
}
