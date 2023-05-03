<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Filter;

use Magento\Catalog\Model\Layer\Filter\Item;

class Price extends \Magento\CatalogSearch\Model\Layer\Filter\Price
{
    /**
     * Retrieve is radio type
     *
     * @return boolean
     */
    public function getIsRadio()
    {
        return true;
    }

    /**
     * Fix price value for frontend.
     *
     * Change price format from '50-59.011' to '50_59'
     *
     * @param  string $label
     * @param  mixed  $value
     * @param  int    $count
     * @return Item
     */
    protected function _createItem($label, $value, $count = 0)
    {
        if (is_array($value) && \count($value) === 2) {
            $value[1] = str_replace(['.011', '-'], ['', '_'], $value[1]);
        } elseif (is_string($value)) {
            $value = str_replace(['.011', '-'], ['', '_'], $value);
        }
        return parent::_createItem($label, $value, $count);
    }
}
