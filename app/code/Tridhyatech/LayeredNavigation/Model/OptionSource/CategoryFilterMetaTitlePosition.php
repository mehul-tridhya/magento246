<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\OptionSource;

/**
 * @since 1.0.0
 */
class CategoryFilterMetaTitlePosition extends AbstractTitlePosition
{

    /**
     * Get filter title positions.
     *
     * @return array
     */
    public function toOptionHash(): array
    {
        return [
            self::POSITION_BEFORE   => __('Before Meta Title'),
            self::POSITION_AFTER => __('After Meta Title'),
            self::POSITION_NONE => __('No'),
        ];
    }
}
