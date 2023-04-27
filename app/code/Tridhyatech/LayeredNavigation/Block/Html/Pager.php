<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Tridhyatech\LayeredNavigation\Block\Html;

use Magento\Framework\View\Element\Template\Context;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\Variable\Registry;
use Tridhyatech\LayeredNavigation\Model\Variable\Renderer;

/**
 * Extended pager for category and search results pages.
 *
 * @since 1.0.0
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Renderer
     */
    private $variableRenderer;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Registry
     */
    private $variableRegistry;

    /**
     * @param \Magento\Framework\View\Element\Template\Context          $context
     * @param \Tridhyatech\LayeredNavigation\Helper\Config           $config
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Renderer $variableRenderer
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Registry $variableRegistry
     * @param array                                                     $data
     */
    public function __construct(
        Context $context,
        Config $config,
        Renderer $variableRenderer,
        Registry $variableRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->variableRenderer = $variableRenderer;
        $this->variableRegistry = $variableRegistry;
    }

    /**
     * @inheritdoc
     */
    public function getPageUrl($page)
    {
        if ($this->config->isModuleEnabled()) {
            return $this->variableRenderer->render($this->variableRegistry->get(), ['p' => $page > 1 ? $page : null]);
        }

        return parent::getPageUrl($page);
    }
}
