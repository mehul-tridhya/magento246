<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Variable\Value;

class Url implements UrlInterface
{

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
}
