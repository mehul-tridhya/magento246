<?php
/**
 * @author Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Api\Data;

/**
 * @since 1.0.0
 */
interface FilterMetaInterface
{
    public const TYPE_ATTRIBUTE = 'attribute';
    public const TYPE_CUSTOM_OPTION = 'custom_option';
    public const TYPE_TOOLBAR_VAR = 'toolbar_var';
    public const TYPE_SPECIAL = 'special';
    public const TYPE_CATEGORY = 'category';

    /**
     * Get request variable or attribute code.
     *
     * @return string
     */
    public function getRequestVariable(): string;

    /**
     * Check if filter based on attribute.
     *
     * @return bool
     */
    public function isAttribute(): bool;

    /**
     * Check if filter based on custom option.
     *
     * @return bool
     */
    public function isCustomOption(): bool;

    /**
     * Check if filter based on special filters.
     *
     * @return bool
     */
    public function isSpecial(): bool;

    /**
     * Check if filter based on special filters.
     *
     * @return bool
     */
    public function isCategory(): bool;

    /**
     * Check if filter based on toolbar.
     *
     * @return bool
     */
    public function isToolbarVariable(): bool;
}
