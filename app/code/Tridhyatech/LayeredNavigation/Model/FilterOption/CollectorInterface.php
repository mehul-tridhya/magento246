<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterOption;

/**
 * @since 1.0.0
 */
interface CollectorInterface
{

    /**
     * Collect options codes and labels.
     *
     * @param array $options
     * @return array
     */
    public function collect(array $options): array;
}
