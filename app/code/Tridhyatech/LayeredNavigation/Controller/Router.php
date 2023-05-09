<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
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

class Router implements RouterInterface
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\GetList
     */
    private $getUrlVariables;

    /**
     * @var Value
     */
    private $variableValue;

    /**
     * @var PathProcessor
     */
    private $pathProcessor;

    /**
     * @var Registry
     */
    private $variableRegistry;

    /**
     * @var ParamsProcessor
     */
    private $paramsProcessor;

    /**
     * @var AjaxRequestLocator
     */
    private $ajaxRequestLocator;

    /**
     * @var Seo
     */
    private $seoConfig;

    /**
     * @param Config                   $config
     * @param GetUrlVariablesInterface $getUrlVariables
     * @param Value                    $variableValue
     * @param Processor                $pathProcessor
     * @param Registry                 $variableRegistry
     * @param Processor                $paramsProcessor
     * @param AjaxRequestLocator       $ajaxRequestLocator
     * @param Seo                      $seoConfig
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
     * @param  RequestInterface $request
     * @return void
     */
    public function match(RequestInterface $request): void
    {
        if (! $request instanceof Request
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
     * Process product filter regular request.
     *
     * @param Request $request
     */
    private function handlePageRequest(Request $request): void
    {
        if (InsertFiltersIn::GET_PARAMS === $this->seoConfig->getInsertFiltersIn()) {
            $variables = $this->getUrlVariables->getFromParams($this->paramsProcessor->parseGetParams($request));
        } else if(InsertFiltersIn::GET_PARAMS !== $this->seoConfig->getInsertFiltersIn()) {
            $variables = $this->getUrlVariables->get($request->getPathInfo());
        }

        if (! $variables) {
            return;
        }
        $variables = $this->variableValue->prepareVariableValues($variables);
        $this->variableRegistry->set($variables);
        $this->pathProcessor->moveToParams($request, $variables);
    }

    /**
     * Process product filter ajax request.
     *
     * @param Request $request
     */
    private function handleAjaxRequest(Request $request): void
    {
        $this->ajaxRequestLocator->setActive(true);
        $variables = $this->getUrlVariables->getFromAjaxParams($request->getParam('prfilter_variables', []));
        $variables = $this->variableValue->preparePriceValues($variables);
        $this->variableRegistry->set($variables);
        $this->paramsProcessor->moveToParams($request, $variables);
    }

}
