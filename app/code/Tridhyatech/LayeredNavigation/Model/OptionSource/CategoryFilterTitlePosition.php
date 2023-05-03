<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\OptionSource;

class CategoryFilterTitlePosition extends AbstractTitlePosition
{

    /**
     * Get filter title positions.
     *
     * @return array
     */
    public function toOptionHash(): array
    {
        return [
            self::POSITION_BEFORE   => __('Before Category Name'),
            self::POSITION_AFTER => __('After Category Name'),
            self::POSITION_NONE => __('No'),
        ];
    }
}
