<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\OptionSource;

use Tridhyatech\LayeredNavigation\Model\OptionSource\AbstractSource;

/**
 * @since 1.0.0
 */
abstract class AbstractTitlePosition extends AbstractSource
{
    public const POSITION_BEFORE = 'before';
    public const POSITION_AFTER = 'after';
    public const POSITION_NONE = 'none';
}
