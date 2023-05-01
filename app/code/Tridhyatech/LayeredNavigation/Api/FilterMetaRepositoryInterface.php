<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Api;

use Tridhyatech\LayeredNavigation\Api\Data\FilterMetaInterface;

/**
 * @since 1.0.0
 */
interface FilterMetaRepositoryInterface
{

    /**
     * Get filter meta by request variable or attribute code
     *
     * @param string $requestVar
     * @return \Tridhyatech\LayeredNavigation\Api\Data\FilterMetaInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $requestVar): FilterMetaInterface;

    /**
     * Get meta about all active filters.
     *
     * @return \Tridhyatech\LayeredNavigation\Api\Data\FilterMetaInterface[]
     */
    public function getList(): array;
}
