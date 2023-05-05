<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterMeta;

use Tridhyatech\LayeredNavigation\Api\Data\FilterInterface;
use Tridhyatech\LayeredNavigation\Model\FilterMeta;
use Magento\Framework\ObjectManagerInterface;

class Factory
{

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Custom filter meta factory.
     *
     * @param  string $requestVar
     * @param  string $type
     * @return FilterInterface
     */
    public function create(string $requestVar, string $type): FilterInterface
    {
        return $this->objectManager->create(FilterMeta::class, ['requestVar' => $requestVar, 'type' => $type]);
    }
}
