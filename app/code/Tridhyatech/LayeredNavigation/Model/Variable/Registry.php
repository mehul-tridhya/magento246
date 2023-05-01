<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Variable;

/**
 * @since 1.0.0
 */
class Registry
{

    /**
     * @var array
     */
    private $variables = [];

    /**
     * Save current variables and their values.
     *
     * @param array $variables
     */
    public function set(array $variables): void
    {
        $this->variables = $variables;
    }

    /**
     * Get current variables and their values.
     *
     * @return array
     */
    public function get(): array
    {
        return $this->variables;
    }
}
