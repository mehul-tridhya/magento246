<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Asset\Repository;
use Tridhyatech\LayeredNavigation\Block\Adminhtml\System\Config\Form\Element\ImageRadioButtons;

/**
 * Frontend model for render customizations.
 *
 * See README.md for instruction
 *
 * @since 1.0.4
 */
class CustomisableField extends Field
{

    /**
     * @var Repository
     */
    private $viewAssetRepository;

    /**
     * @param Context    $context
     * @param Repository $viewAssetRepository
     * @param array      $data
     */
    public function __construct(
        Context $context,
        Repository $viewAssetRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->viewAssetRepository = $viewAssetRepository;
    }

    /**
     * Render element value
     *
     * @param  ImageRadioButtons $element
     * @return string
     */
    protected function _renderValue(AbstractElement $element): string
    {
        $valueCssClass = $element->getPrValueCustomCssClass();

        if ($element->getTooltip()) {
            $html = '<td class="value with-tooltip ' . $valueCssClass . '">';
            $html .= $this->_getElementHtml($element);
            $html .= '<div class="tooltip"><span class="help"><span></span></span>';
            $html .= '<div class="tooltip-content">' . $element->getTooltip() . '</div></div>';
        } else {
            $html = '<td class="value ' . $valueCssClass . '">';
            $html .= $this->_getElementHtml($element);
        }
        $comment = (string) $element->getComment();
        if ($comment) {
            $html .= '<p class="note"><span>' . $this->filterTooltipMediaVariables($element, $comment) . '</span></p>';
        }
        $html .= '</td>';
        return $html;
    }

    /**
     * Disable checkbox if needed.
     *
     * @param  AbstractElement $element
     * @return bool
     */
    protected function _isInheritCheckboxRequired(AbstractElement $element): bool
    {
        if ($element->getData('field_config/pr_disable_inherit_checkbox')
            || $element->getPrDisableInheritCheckbox()
        ) {
            return false;
        }
        return parent::_isInheritCheckboxRequired($element);
    }

    /**
     * Disable scope label if needed.
     *
     * @param  AbstractElement $element
     * @return string
     */
    protected function _renderScopeLabel(AbstractElement $element): string
    {
        if ($element->getData('field_config/pr_disable_scope_label')) {
            return '';
        }
        return parent::_renderScopeLabel($element);
    }

    /**
     * Replace media directives by urls.
     *
     * @param  AbstractElement $element
     * @param  string          $comment
     * @return string
     */
    private function filterTooltipMediaVariables(AbstractElement $element, string $comment): string
    {
        if (! $element->getData('field_config/pr_allow_variables')) {
            return $comment;
        }

        if (preg_match_all('/{{media url="(.*?)".*?}}/', $comment, $media)) {
            $urls = [];
            foreach ($media[1] as $fieldId) {
                $urls[] = $this->viewAssetRepository->getUrl($fieldId);
            }
            $comment = str_replace($media[0], $urls, $comment);
        }

        return $comment;
    }
}
