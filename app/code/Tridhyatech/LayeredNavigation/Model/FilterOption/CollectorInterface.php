<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterOption;

interface CollectorInterface
{

    /**
     * Collect options codes and labels.
     *
     * @param  array $options
     * @return array
     */
    public function collect(array $options): array;
}
