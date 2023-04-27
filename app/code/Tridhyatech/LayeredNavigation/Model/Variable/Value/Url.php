<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Variable\Value;

/**
 * @since 1.0.0
 */
class Url implements UrlInterface
{

    /**
     * @inheritDoc
     */
    public function encode(string $variable, $value): string
    {
        if ('price' === $variable) {
            return str_replace('-', '_', $value);
        }
        return (string) $value;
    }

    /**
     * @inheritDoc
     */
    public function decode(string $variable, string $value): string
    {
        if ('price' === $variable) {
            return str_replace('_', '-', $value);
        }
        return $value;
    }
}
