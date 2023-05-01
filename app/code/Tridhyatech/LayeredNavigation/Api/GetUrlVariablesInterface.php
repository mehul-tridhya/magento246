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
interface GetUrlVariablesInterface
{

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

    /**
     * Get variables from GET params as array of variable values.
     *
     * @param array $params
     * @return array
     * [
     *     'requestVar' => ['value', 'value', ...],
     *     ...
     * ]
     * @since 1.3.0
     */
    public function getFromParams(array $params): array;

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
}
