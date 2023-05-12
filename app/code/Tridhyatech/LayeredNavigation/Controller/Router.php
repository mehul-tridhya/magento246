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
    private $getUrlAttribute;

    /**
     * @var Value
     */
    private $attributeValue;

    /**
     * @var PathProcessor
     */
    private $pathProcessor;

    /**
     * @var Registry
     */
    private $attributeRegistry;

    /**
     * @var ParamsProcessor
     */
    private $variableProcessor;

    /**
     * @var AjaxRequestLocator
     */
    private $ajaxLocator;

    /**
     * @var Seo
     */
    private $seoConfig;

    /**
     * @param Config                   $config
     * @param GetUrlVariablesInterface $getUrlAttribute
     * @param Value                    $attributeValue
     * @param Processor                $pathProcessor
     * @param Registry                 $attributeRegistry
     * @param Processor                $variableProcessor
     * @param AjaxRequestLocator       $ajaxLocator
     * @param Seo                      $seoConfig
     */
    public function __construct(
        Config $config,
        GetUrlVariablesInterface $getUrlAttribute,
        Value $attributeValue,
        PathProcessor $pathProcessor,
        Registry $attributeRegistry,
        ParamsProcessor $variableProcessor,
        AjaxRequestLocator $ajaxLocator,
        Seo $seoConfig
    ) {
        $this->config = $config;
        $this->getUrlAttribute = $getUrlAttribute;
        $this->attributeValue = $attributeValue;
        $this->pathProcessor = $pathProcessor;
        $this->attributeRegistry = $attributeRegistry;
        $this->variableProcessor = $variableProcessor;
        $this->ajaxLocator = $ajaxLocator;
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
            || $this->ajaxLocator->isActive()
        ) {
            return;
        }

        if ($request->getParam('prfilter_ajax')) {
            $this->manageAjaxRequest($request);
            return;
        }

        $this->managePageRequest($request);
    }

    /**
     * Process product filter regular request.
     *
     * @param Request $request
     */
    private function managePageRequest(Request $request): void
    {
        if (InsertFiltersIn::GET_PARAMS === $this->seoConfig->getInsertFiltersIn()) {
            $variables = $this->getUrlAttribute->getFromParams($this->variableProcessor->parseGetParams($request));
        } elseif (InsertFiltersIn::GET_PARAMS !== $this->seoConfig->getInsertFiltersIn()) {
            $variables = $this->getUrlAttribute->get($request->getPathInfo());
        }

        if (! $variables) {
            return;
        }
        $variables = $this->attributeValue->prepareVariableValues($variables);
        $this->attributeRegistry->set($variables);
        $this->pathProcessor->moveToParams($request, $variables);
    }

    /**
     * Process product filter ajax request.
     *
     * @param Request $request
     */
    private function manageAjaxRequest(Request $request): void
    {
        $this->ajaxLocator->setActive(true);
        $variables = $this->getUrlAttribute->getFromAjaxParams($request->getParam('prfilter_variables', []));
        $variables = $this->attributeValue->preparePriceValues($variables);
        $this->attributeRegistry->set($variables);
        $this->variableProcessor->moveToParams($request, $variables);
    }
}
