<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Tridhyatech\LayeredNavigation\Controller;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Tridhyatech\LayeredNavigation\Api\GetUrlVariablesInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Helper\Config\Seo;
use Tridhyatech\LayeredNavigation\Model\AjaxRequestLocator;
use Tridhyatech\LayeredNavigation\Model\OptionSource\InsertFiltersIn;
use Tridhyatech\LayeredNavigation\Model\Variable\Params\Processor as ParamsProcessor;
use Tridhyatech\LayeredNavigation\Model\Variable\Path\Processor as PathProcessor;
use Tridhyatech\LayeredNavigation\Model\Variable\Registry;
use Tridhyatech\LayeredNavigation\Model\Variable\Value;

/**
 * @since 1.0.0
 */
class Router implements RouterInterface
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\GetList
     */
    private $getUrlVariables;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Value
     */
    private $variableValue;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Path\Processor
     */
    private $pathProcessor;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Registry
     */
    private $variableRegistry;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Params\Processor
     */
    private $paramsProcessor;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\AjaxRequestLocator
     */
    private $ajaxRequestLocator;

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config\Seo
     */
    private $seoConfig;

    /**
     * @param \Tridhyatech\LayeredNavigation\Helper\Config                   $config
     * @param \Tridhyatech\LayeredNavigation\Api\GetUrlVariablesInterface    $getUrlVariables
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Value            $variableValue
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Path\Processor   $pathProcessor
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Registry         $variableRegistry
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Params\Processor $paramsProcessor
     * @param \Tridhyatech\LayeredNavigation\Model\AjaxRequestLocator        $ajaxRequestLocator
     * @param \Tridhyatech\LayeredNavigation\Helper\Config\Seo               $seoConfig
     */
    public function __construct(
        Config $config,
        GetUrlVariablesInterface $getUrlVariables,
        Value $variableValue,
        PathProcessor $pathProcessor,
        Registry $variableRegistry,
        ParamsProcessor $paramsProcessor,
        AjaxRequestLocator $ajaxRequestLocator,
        Seo $seoConfig
    ) {
        $this->config = $config;
        $this->getUrlVariables = $getUrlVariables;
        $this->variableValue = $variableValue;
        $this->pathProcessor = $pathProcessor;
        $this->variableRegistry = $variableRegistry;
        $this->paramsProcessor = $paramsProcessor;
        $this->ajaxRequestLocator = $ajaxRequestLocator;
        $this->seoConfig = $seoConfig;
    }

    /**
     * Parse, convert and move filters variables.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return void
     */
    public function match(RequestInterface $request): void
    {
        if (! $request instanceof Request
            || ! $this->config->isModuleEnabled()
            || $this->ajaxRequestLocator->isActive()
        ) {
            return;
        }

        if ($request->getParam('prfilter_ajax')) {
            $this->handleAjaxRequest($request);
            return;
        }

        $this->handlePageRequest($request);
    }

    /**
     * Process product filter ajax request.
     *
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $request
     */
    private function handleAjaxRequest(Request $request): void
    {
        $this->ajaxRequestLocator->setActive(true);
        $variables = $this->getUrlVariables->getFromAjaxParams($request->getParam('prfilter_variables', []));
        $variables = $this->variableValue->preparePriceValues($variables);
        $this->variableRegistry->set($variables);
        $this->paramsProcessor->moveToParams($request, $variables);
    }

    /**
     * Process product filter regular request.
     *
     * @param \Magento\Framework\HTTP\PhpEnvironment\Request $request
     */
    private function handlePageRequest(Request $request): void
    {
        if (InsertFiltersIn::GET_PARAMS === $this->seoConfig->getInsertFiltersIn()) {
            $variables = $this->getUrlVariables->getFromParams($this->paramsProcessor->parseGetParams($request));
        } else {
            $variables = $this->getUrlVariables->get($request->getPathInfo());
        }

        if (! $variables) {
            return;
        }
        $variables = $this->variableValue->prepareVariableValues($variables);
        $this->variableRegistry->set($variables);
        $this->pathProcessor->moveToParams($request, $variables);
    }
}
