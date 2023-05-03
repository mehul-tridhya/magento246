<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Block\LayeredNavigation\Navigation;

use Magento\Framework\View\LayoutInterface;
use Magento\Framework\Module\Manager;
use Magento\Framework\ObjectManagerInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\FilterItem\Status;
use Magento\LayeredNavigation\Block\Navigation\FilterRenderer as blockFilterRenderer;
use Closure;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\Exception\LocalizedException;
use Tridhyatech\LayeredNavigation\Model\Catalog\Layer\Filter\Item;

class FilterRenderer
{

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @var array
     */
    protected $optionValues = [];

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Status
     */
    private $itemStatus;

    /**
     * @var array
     */
    private $filterRenderers;

    /**
     * @param LayoutInterface        $layout
     * @param Manager                $moduleManager
     * @param ObjectManagerInterface $objectManager
     * @param Config                 $config
     * @param Status                 $itemStatus
     * @param array                  $filterRenderers
     */
    public function __construct(
        LayoutInterface $layout,
        Manager $moduleManager,
        ObjectManagerInterface $objectManager,
        Config $config,
        Status $itemStatus,
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
     * @param                                         blockFilterRenderer $subject
     * @param                                         Closure             $proceed
     * @param                                         FilterInterface     $filter
     * @return                                        mixed
     * @throws                                        LocalizedException
     */
    public function aroundRender(
        blockFilterRenderer $subject,
        Closure $proceed,
        FilterInterface $filter
    ) {
        if (!$this->config->isModuleEnabled()) {
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
     * @param Item $option
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
