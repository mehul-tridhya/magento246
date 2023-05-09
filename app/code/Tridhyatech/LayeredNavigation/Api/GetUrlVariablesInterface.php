<?php
/**
 * @author Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Api;

interface GetUrlVariablesInterface
{
    /**
     * Get variables from special GET param as array of variable values.
     *
     * @param array $params
     * @return array
     * [
     *     'requestVar' => ['value', 'value', ...],
     *     ...
     * ]
     */
    public function getFromAjaxParams(array $params): array;

    /**
     * Get variables from GET params as array of variable values.
     *
     * @param array $params
     * @return array
     * [
     *     'requestVar' => ['value', 'value', ...],
     *     ...
     * ]
     */
    public function getFromParams(array $params): array;

    /**
     * Get variables from url path as array of variable values.
     *
     * @param string $urlPath
     * @return array
     * [
     *     'requestVar' => ['value', 'value', ...],
     *     ...
     * ]
     */
    public function get(string $urlPath): array;
}
