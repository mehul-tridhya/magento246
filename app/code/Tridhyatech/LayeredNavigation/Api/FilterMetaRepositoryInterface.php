<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
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
