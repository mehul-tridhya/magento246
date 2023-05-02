<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Block\LayeredNavigation\Navigation;

/**
 * @since 1.0.0
 */
class FilterRenderer
{

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $layout;

    /**
     * @var array
     */
    protected $optionValues = [];

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\FilterItem\Status
     */
    private $itemStatus;

    /**
     * @var array
     */
    private $filterRenderers;

    /**
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Tridhyatech\LayeredNavigation\Helper\Config $config
     * @param \Tridhyatech\LayeredNavigation\Model\FilterItem\Status $itemStatus
     * @param array $filterRenderers
     */
    public function __construct(
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Tridhyatech\LayeredNavigation\Helper\Config $config,
        \Tridhyatech\LayeredNavigation\Model\FilterItem\Status $itemStatus,
        array $filterRenderers = []
    ) {
        $this->layout = $layout;
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
        $this->config = $config;
        $this->itemStatus = $itemStatus;
        $this->filterRenderers = $filterRenderers;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject
     * @param \Closure $proceed
     * @param \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundRender(
        \Magento\LayeredNavigation\Block\Navigation\FilterRenderer $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Layer\Filter\FilterInterface $filter
    ) {
        if (! $this->config->isModuleEnabled()) {
            return $proceed($filter);
        }

        foreach ($filter->getItems() as $item) {
            $this->refactorRewritedValue($item);
        }
        $this->itemStatus->markActiveItems($filter->getItems());
        $this->optionValues = [];

        $block = null;
        foreach ($this->filterRenderers as $filterClass => $filterRenderer) {
            if ($filter instanceof $filterClass) {
                $block = $filterRenderer;
            }
        }
        if ($block) {
            return $this->layout->createBlock($block)->setFilter($filter)->toHtml();
        }

        return $proceed($filter);
    }

    /**
     * Set rewritten value.
     *
     * @param \Tridhyatech\LayeredNavigation\Model\Catalog\Layer\Filter\Item $option
     */
    private function refactorRewritedValue($option): void
    {
        $value = $option->getRewritedValue();

        if (isset($this->optionValues[$value])) {
            $value .= '_' . $option->getValue();
            $option->setRewritedValue($value);
        }

        $this->optionValues[$value] = $value;
    }
}
