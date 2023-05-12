<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Block\Navigation;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\View\Element\Template\Context;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\CatalogSearch\IsSearchResultsPage;
use Magento\LayeredNavigation\Block\Navigation\State as mainState;

class State extends mainState
{

    /**
     * @var IsSearchResultsPage
     */
    private $isSearchResultsPage;
    
    /**
     * @var Config
     */
    private $config;
    /**
     * @param Context             $context
     * @param Resolver            $layerResolver
     * @param Config              $config
     * @param IsSearchResultsPage $isSearchResultsPage
     * @param array               $data
     */
    public function __construct(
        Context $context,
        Resolver $layerResolver,
        Config $config,
        IsSearchResultsPage $isSearchResultsPage,
        array $data = []
    ) {
        parent::__construct($context, $layerResolver, $data);
        $this->config = $config;
        $this->isSearchResultsPage = $isSearchResultsPage;
    }

    /**
     * @inheritdoc
     */
    public function getClearUrl()
    {
        $clearUrl = parent::getClearUrl();

        $additionalParam = '';

        $toolbarVars = $this->config->getToolbarVars();
        foreach ($this->_request->getParams() as $param => $value) {
            if (in_array($param, $toolbarVars, true)) {
                if ($this->isSearchResultsPage->execute($clearUrl)) {
                    $additionalParam .= '/' . $param . Config::FILTER_PARAM_SEPARATOR . $value;
                } elseif (!$this->isSearchResultsPage->execute($clearUrl)) {
                    $clearUrl .= '/' . $param . Config::FILTER_PARAM_SEPARATOR . $value;
                }
            }
        }

        if (false !== strpos($clearUrl, 'amfinder')) {
            $clearUrl = preg_replace(
                '/(amfinder)\/.*?\/(.*?\/\?)/',
                "$1{$additionalParam}{$this->config->getCategoryUrlPathSuffix()}?",
                $clearUrl
            );
        } elseif (false === strpos($clearUrl, 'amfinder')) {
            $clearUrl = preg_replace(
                '/(catalogsearch\/result)\/.*?\/(.*?\/\?)/',
                "$1{$additionalParam}{$this->config->getCategoryUrlPathSuffix()}?",
                $clearUrl
            );
        }

        return $clearUrl;
    }

    /**
     * @inheritdoc
     */
    public function getTemplate()
    {
        if ($this->getPlumTemplate()) {
            return $this->getPlumTemplate();
        }
        return parent::getTemplate();
    }
}
