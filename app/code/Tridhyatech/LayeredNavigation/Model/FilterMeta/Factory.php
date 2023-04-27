<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterMeta;

use Magento\Framework\ObjectManagerInterface;
use Tridhyatech\LayeredNavigation\Api\Data\FilterMetaInterface;
use Tridhyatech\LayeredNavigation\Model\FilterMeta;

/**
 * @since 1.0.0
 */
class Factory
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Custom filter meta factory.
     *
     * @param string $requestVar
     * @param string $type
     * @return \Tridhyatech\LayeredNavigation\Api\Data\FilterMetaInterface
     */
    public function create(string $requestVar, string $type): FilterMetaInterface
    {
        return $this->objectManager->create(FilterMeta::class, ['requestVar' => $requestVar, 'type' => $type]);
    }
}
