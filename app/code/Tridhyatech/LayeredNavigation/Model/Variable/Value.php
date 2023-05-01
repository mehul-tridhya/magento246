<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Variable;

use Magento\Framework\Exception\NoSuchEntityException;
use Tridhyatech\LayeredNavigation\Api\FilterMetaRepositoryInterface;
use Tridhyatech\LayeredNavigation\Model\Variable\Value\Slugify;
use Tridhyatech\LayeredNavigation\Model\Variable\Value\UrlInterface;

/**
 * @since 1.0.0
 */
class Value
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Api\FilterMetaRepositoryInterface
     */
    private $filterMetaRepository;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Value\UrlInterface
     */
    private $urlValue;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Value\Slugify
     */
    private $slugify;

    /**
     * @param \Tridhyatech\LayeredNavigation\Api\FilterMetaRepositoryInterface $filterMetaRepository
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Value\UrlInterface $urlValue
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Value\Slugify      $slugify
     */
    public function __construct(
        FilterMetaRepositoryInterface $filterMetaRepository,
        UrlInterface $urlValue,
        Slugify $slugify
    ) {
        $this->filterMetaRepository = $filterMetaRepository;
        $this->urlValue = $urlValue;
        $this->slugify = $slugify;
    }

    /**
     * Prepare all values for array of variables.
     *
     * @param array $variables
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
     * @param string $code
     * @param array  $values
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
     * @param string $code
     * @param string $value
     * @return string|null
     */
    private function prepareValue(string $code, string $value): ?string
    {
        $value = html_entity_decode($value);

        try {
            $filterMeta = $this->filterMetaRepository->get($code);
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
     * @param array $variables
     * @return array
     */
    public function preparePriceValues(array $variables): array
    {
        if (isset($variables['price'])) {
            $variables['price'] = array_map(static function ($value) {
                return str_replace('_', '-', $value);
            }, $variables['price']);
        }

        return $variables;
    }
}
