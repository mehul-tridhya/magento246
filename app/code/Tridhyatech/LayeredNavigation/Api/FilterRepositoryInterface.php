<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Api;

use Tridhyatech\LayeredNavigation\Api\Data\FilterInterface;

/**
 * @since 1.0.0
 */
interface FilterRepositoryInterface
{

    /**
     * Get about all active filters.
     *
     * @return FilterInterface[]
     */
    public function getList(): array;

    /**
     * Get filter by request variable or attribute code
     *
     * @param string $requestVar
     * @return FilterInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $requestVar): FilterInterface;
}
