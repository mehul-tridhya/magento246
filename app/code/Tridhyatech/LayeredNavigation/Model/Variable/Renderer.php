<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Variable;

use Magento\Framework\UrlInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Helper\Config\Seo;
use Tridhyatech\LayeredNavigation\Model\CatalogSearch\IsSearchResultsPage;
use Tridhyatech\LayeredNavigation\Model\OptionSource\InsertFiltersIn;
use Tridhyatech\LayeredNavigation\Model\Variable\Path\Processor;
use Tridhyatech\LayeredNavigation\Model\Variable\Value\UrlInterface as modelUrlInterface;
use Tridhyatech\LayeredNavigation\Model\Variable\Registry;

class Renderer
{

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var Processor
     */
    private $pathProcessor;

    /**
     * @var Registry
     */
    private $variableRegistry;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var modelUrlInterface
     */
    private $valueUrlEncoder;

    /**
     * @var Seo
     */
    private $seoConfig;

    /**
     * @var IsSearchResultsPage
     */
    private $isSearchResultsPage;

    /**
     * @param UrlInterface        $urlBuilder
     * @param Processor           $pathProcessor
     * @param Registry            $variableRegistry
     * @param Config              $config
     * @param modelUrlInterface   $valueUrlEncoder
     * @param Seo                 $seoConfig
     * @param IsSearchResultsPage $isSearchResultsPage
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Processor $pathProcessor,
        Registry $variableRegistry,
        Config $config,
        modelUrlInterface $valueUrlEncoder,
        Seo $seoConfig,
        IsSearchResultsPage $isSearchResultsPage
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->pathProcessor = $pathProcessor;
        $this->variableRegistry = $variableRegistry;
        $this->config = $config;
        $this->valueUrlEncoder = $valueUrlEncoder;
        $this->seoConfig = $seoConfig;
        $this->isSearchResultsPage = $isSearchResultsPage;
    }

    /**
     * Render variables in current url.
     *
     * @param  array $variables
     * @param  array $additionalGetParams
     * @return string
     */
    public function render(array $variables, array $additionalGetParams = []): string
    {
        $currentUrl = $this->urlBuilder->getCurrentUrl();
        $baseUrl = $this->urlBuilder->getBaseUrl();

        $relatedUrl = str_replace($baseUrl, '', $currentUrl);

        $path = parse_url($relatedUrl, PHP_URL_PATH);
        $query = parse_url($relatedUrl, PHP_URL_QUERY);
        $fragment = parse_url($relatedUrl, PHP_URL_FRAGMENT);

        if ($query) {
            parse_str($query, $getParams);
        } else {
            $getParams = [];
        }

        $getParams = array_merge($getParams, $additionalGetParams);
        $getParams = array_filter(
            $getParams,
            static function ($paramValue) {
                return null !== $paramValue;
            }
        );

        $nextPath = $this->pathProcessor->getPathWithoutVariables($path, $this->variableRegistry->get());
        if (InsertFiltersIn::GET_PARAMS === $this->seoConfig->getInsertFiltersIn()) {
            $resultUrl = "{$baseUrl}{$nextPath}";
            $getParams = $this->addVariablesToParams($getParams, $variables);
        } else {
            $resultUrl = $this->addVariablesToUrl("{$baseUrl}{$nextPath}", $variables);
        }

        if ($getParams) {
            $resultUrl .= '?' . http_build_query($getParams);
        }
        if ($fragment) {
            $resultUrl .= "#$fragment";
        }
        return $resultUrl;
    }

    /**
     * Add variables to the url.
     *
     * @param  string $url
     * @param  array  $variables
     * @return string
     */
    public function addVariablesToUrl(string $url, array $variables): string
    {
        if ($variables) {
            $url = str_replace($this->config->getCategoryUrlSuffix(), '', $url);
            $url = rtrim($url, '/');
            $url .= '/' . $this->inlineVariables($variables);
            $url .= $this->isSearchResultsPage->execute($url) ? '' : $this->config->getCategoryUrlSuffix();
        }
        return $url;
    }

    /**
     * Inline variables into string.
     *
     * Format "color-red/size-m-l"
     *
     * @param  array $variables
     * @return string
     */
    public function inlineVariables(array $variables): string
    {
        ksort($variables);

        $parts = [];
        foreach ($variables as $variable => $values) {
            $preparedValues = [];
            foreach ($values as $value) {
                $preparedValues[] = $this->valueUrlEncoder->encode($variable, $value);
            }
            sort($preparedValues);
            $parts[] = $variable . '-' . implode('-', $preparedValues);
        }

        return implode('/', $parts);
    }

    /**
     * Add variables to get params.
     *
     * @param  string[] $getParams
     * @param  array[]  $variables
     * @return string[]
     */
    public function addVariablesToParams(array $getParams, array $variables): array
    {
        ksort($variables);
        foreach ($variables as $variable => $values) {
            $preparedValues = [];
            foreach ($values as $value) {
                $preparedValues[] = $this->valueUrlEncoder->encode($variable, $value);
            }
            sort($preparedValues);
            $getParams[$variable] = implode('-', $preparedValues);
        }
        return $getParams;
    }
}
