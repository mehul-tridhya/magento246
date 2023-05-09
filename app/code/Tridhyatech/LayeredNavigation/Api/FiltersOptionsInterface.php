<?php
/**
 * @author Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Api;

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
}
