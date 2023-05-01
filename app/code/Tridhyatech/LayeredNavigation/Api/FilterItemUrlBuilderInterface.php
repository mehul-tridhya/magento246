<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Api;

/**
 * @since 1.0.0
 */
interface FilterItemUrlBuilderInterface
{

    /**
     * Create url to add filter option.
     *
     * @param string $requestVar
     * @param string $itemValue
     * @param bool   $removeCurrentValue
     * @return string
     */
    public function getAddFilterUrl(string $requestVar, string $itemValue, bool $removeCurrentValue = false): string;

    /**
     * Create url to remove filter option.
     *
     * @param string $requestVar
     * @param string $itemValue
     * @return string
     */
    public function getRemoveFilterUrl(string $requestVar, string $itemValue): string;

    /**
     * Create url to toggle filter option.
     *
     * @param string $requestVar
     * @param string $itemValue
     * @param bool   $removeCurrentValue
     * @return string
     */
    public function toggleFilterUrl(string $requestVar, string $itemValue, bool $removeCurrentValue = false): string;
}
