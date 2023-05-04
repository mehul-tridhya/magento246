<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Block\Html;

use Magento\Framework\View\Element\Template\Context;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\Variable\Registry;
use Tridhyatech\LayeredNavigation\Model\Variable\Renderer;

/**
 * Extended pager for category and search results pages.
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Renderer
     */
    private $variableRenderer;

    /**
     * @var Registry
     */
    private $variableRegistry;

    /**
     * @param Context  $context
     * @param Config   $config
     * @param Renderer $variableRenderer
     * @param Registry $variableRegistry
     * @param array    $data
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
        return $this->variableRenderer->render($this->variableRegistry->get(), ['p' => $page > 1 ? $page : null]);
    }
}
