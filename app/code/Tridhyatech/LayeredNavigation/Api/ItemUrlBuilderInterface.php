<?php
/**
 * @author Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Api;

interface ItemUrlBuilderInterface
{
    /**
     * Create url to remove filter option.
     *
     * @param string $requestVar
     * @param string $itemValue
     * @return string
     */
    public function getRemoveUrl(string $requestVar, string $itemValue): string;

    /**
     * Create url to toggle filter option.
     *
     * @param string $requestVar
     * @param string $itemValue
     * @return string
     */
    public function toggleUrl(string $requestVar, string $itemValue): string;

    /**
     * Create url to add filter option.
     *
     * @param string $requestVar
     * @param string $itemValue
     * @param bool   $removeCurrentValue
     * @return string
     */
    public function getAddUrl(string $requestVar, string $itemValue, bool $removeCurrentValue): string;
}
