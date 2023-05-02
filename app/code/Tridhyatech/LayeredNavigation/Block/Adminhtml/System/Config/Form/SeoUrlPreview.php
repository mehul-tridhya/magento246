<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @since 1.3.0
 */
class SeoUrlPreview extends AbstractElement
{

    /**
     * Get Preview URL HTML.
     *
     * @return string
     */
    public function getElementHtml(): string
    {
        return '<span id="' . $this->getHtmlId() . '" ' .
            'style="display: block;padding: 6px;background: #aacbff;color: #000000;border-radius: 3px;" ' .
            'class="admin__control">https://example.com/jackets.html</span>';
    }
}
