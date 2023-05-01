<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Variable\Value;

/**
 * @since 1.0.0
 */
interface UrlInterface
{

    /**
     * Encode value to use it url.
     *
     * @param string     $variable
     * @param string|int $value
     * @return string
     */
    public function encode(string $variable, $value): string;

    /**
     * Decode url value.
     *
     * @param string       $variable
     * @param string|array $value
     * @return string
     */
    public function decode(string $variable, string $value): string;
}
