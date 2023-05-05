<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterItem;

use Magento\Catalog\Model\Layer\Filter\Item;

class Status
{
    /**
     * Active Filter List.
     *
     * @var null|array
     */
    private $activeFilters;

    /**
     * Is filter active.
     *
     * @param  Item $item
     * @return bool
     */
    private function isActive(Item $item): bool
    {
        $value = (string) $item->getValue();
        $additionalValue = str_replace(' ', '_', strtolower($value));
        if ($value == '0') {
            $value = 'no';
        }

        $filterObject = $item->getData('filter');
        $attributeCode = $filterObject->getData('pf_attribute_code');
        $activeFilters = $this->getActiveFilters($filterObject->getLayer());

        return $item->getIsActive()
            || (isset($activeFilters[$attributeCode])
                && (in_array($value, $activeFilters[$attributeCode])
                    || in_array($additionalValue, $activeFilters[$attributeCode]))
            );
    }

    /**
     * Mark active filter items.
     *
     * @param  array $items
     * @return void
     */
    public function markActiveItems(array $items): void
    {
        foreach ($items as $item) {
            $item->setData(
                'is_active',
                $this->isActive($item)
            );
        }
    }

    /**
     * Retrieve active filter.
     *
     * @param  object $layer
     * @return array
     */
    protected function getActiveFilters($layer): array
    {
        if (null === $this->activeFilters) {
            $this->activeFilters = [];

            if (!empty($layer->getState()->getFilters())) {
                foreach ($layer->getState()->getFilters() as $filter) {
                    $attributeCode = $filter->getData('filter')->getData('pf_attribute_code');
                    $value = $filter->getValue();

                    if (!is_array($value)) {
                        $value = strtolower((string) $value);
                    }

                    $this->activeFilters[$attributeCode][] = $value;
                }
            }
        }

        return $this->activeFilters;
    }
}
