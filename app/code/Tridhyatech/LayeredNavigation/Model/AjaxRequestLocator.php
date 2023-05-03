<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model;

/**
 * @since 1.0.0
 */
class AjaxRequestLocator
{

    /**
     * @var bool
     */
    private $active = false;

    /**
     * Check whether current request is filter ajax request.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Set that current request is filter ajax request.
     *
     * @param mixed $active
     */
    public function setActive($active): void
    {
        $this->active = $active;
    }
}
