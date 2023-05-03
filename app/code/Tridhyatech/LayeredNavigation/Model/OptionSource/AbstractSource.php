<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Allow creating short system config sources
 *
 * @since 2.4.1
 */
abstract class AbstractSource implements OptionSourceInterface
{
    /**
     * Convert ['<value>' => '<label>'] to ['value' => '<value>', 'label' => '<label>']
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->toOptionHash() as $key => $value) {
            $result[] = ['value' => $key, 'label' => $value];
        }
        return $result;
    }

    /**
     * Return array of options
     *
     * @return array Format: array(array('<value>' => '<label>'), ...)
     */
    abstract public function toOptionHash(): array;
}
