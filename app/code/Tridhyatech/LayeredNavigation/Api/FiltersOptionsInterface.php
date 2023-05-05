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
interface FiltersOptionsInterface
{

    /**
     * Get attribute option label by its id.
     *
     * @param string     $requestVar
     * @param int|string $optionValue
     * @return string
     */
    public function toOptionLabel(string $requestVar, $optionValue): string;

    /**
     * Get attribute option id by its escaped label.
     *
     * @param string $requestVar
     * @param string $optionCode
     * @return int|string
     */
    public function toOptionValue(string $requestVar, string $optionCode);

    /**
     * Get category id by url key.
     *
     * @param string $urlKey
     * @return int
     */
    public function getCategoryId(string $urlKey): int;

    /**
     * Get attribute option code by its id.
     *
     * @param string     $requestVar
     * @param int|string $optionValue
     * @return string
     */
    public function toOptionCode(string $requestVar, $optionValue): string;
}
