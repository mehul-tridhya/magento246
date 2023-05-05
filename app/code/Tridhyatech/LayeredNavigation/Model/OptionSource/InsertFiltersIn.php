<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\OptionSource;

use Tridhyatech\LayeredNavigation\Model\OptionSource\AbstractSource;

/**
 * @since 1.3.0
 */
class InsertFiltersIn extends AbstractSource
{
    public const GET_PARAMS = 1;
    public const URL_PATH = 0;

    /**
     * Get filter mode.
     *
     * @return array
     */
    public function toOptionHash(): array
    {
        return [
            self::URL_PATH   => __('URL path'),
            self::GET_PARAMS => __('GET parameters'),
        ];
    }
}
