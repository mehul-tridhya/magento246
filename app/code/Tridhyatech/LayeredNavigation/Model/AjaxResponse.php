<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Config\View;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Layout;
use Tridhyatech\LayeredNavigation\Model\Seo\PageTitleResolver;
use Tridhyatech\LayeredNavigation\Model\Variable\Registry;
use Tridhyatech\LayeredNavigation\Model\Variable\Renderer;

/**
 * @since 1.0.0
 */
class AjaxResponse
{

    public const CATEGORY_VIEW_ACTION_NAME = 'catalog_category_view';

    public const CATALOG_SEARCH_ACTION_NAME = 'catalogsearch_result_index';

    public const PRODUCT_FILTER_REMOVE_HANDLE = 'product_filter_ajax_request';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Magento\Framework\View\Layout
     */
    private $layout;

    /**
     * Variables from view.xml
     *
     * @var array
     */
    private $vars;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Registry
     */
    private $variableRegistry;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Renderer
     */
    private $variableRenderer;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Seo\PageTitleResolver
     */
    private $pageTitleResolver;

    /**
     * @var bool
     */
    private $navigationBlock = false;

    /**
     * @param \Magento\Framework\App\Request\Http                           $request
     * @param \Magento\Framework\View\Layout                                $layout
     * @param \Magento\Framework\Config\View                                $configView
     * @param \Magento\Framework\Module\Manager                             $moduleManager
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Registry     $variableRegistry
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Renderer     $variableRenderer
     * @param \Tridhyatech\LayeredNavigation\Model\Seo\PageTitleResolver $pageTitleResolver
     */
    public function __construct(
        Http $request,
        Layout $layout,
        View $configView,
        Manager $moduleManager,
        Registry $variableRegistry,
        Renderer $variableRenderer,
        PageTitleResolver $pageTitleResolver
    ) {
        $this->layout = $layout;
        $this->vars = $configView->getVars('Plumrocket_ProductFilter');
        $this->request = $request;
        $this->moduleManager = $moduleManager;
        $this->variableRegistry = $variableRegistry;
        $this->variableRenderer = $variableRenderer;
        $this->pageTitleResolver = $pageTitleResolver;
    }

    /**
     * Collect product filter data for response.
     *
     * @return array
     */
    public function collectFilterData(): array
    {
        $productListHtml = $this->getProductListHtml();
        $productsCount = $this->getProductsCount();
        $leftNavHtml = $this->getLeftNavHtml();
        $googleTagManagerData = $this->getGoogleTagManagerData();

        return [
            'productsCount' => $productsCount,
            'productlist' => $productListHtml,
            'leftnav' => $leftNavHtml,
            'googleTagManager' => $googleTagManagerData,
            'nextUrl' => $this->variableRenderer->render($this->variableRegistry->get()),
            'h1' => $this->pageTitleResolver->resolve($this->request),
        ];
    }

    /**
     * Retrieve left navigation html
     *
     * @return string
     */
    private function getLeftNavHtml(): string
    {
        if ($block = $this->getNavigationBlock()) {
            return $block->toHtml();
        }

        return '';
    }

    /**
     * Retrieve product list html
     *
     * @return string
     */
    private function getProductListHtml(): string
    {
        if ($this->request->getFullActionName() === self::CATEGORY_VIEW_ACTION_NAME) {
            return $this->layout->getBlock($this->vars['catalog_product_list_block'])->toHtml();
        }

        if ($this->request->getFullActionName() === self::CATALOG_SEARCH_ACTION_NAME) {
            return $this->layout->getBlock($this->vars['catalogsearch_product_list_block'])->toHtml();
        }

        return '';
    }

    /**
     * Retrieve Google Tag Manager data
     *
     * @return string
     */
    private function getGoogleTagManagerData(): string
    {
        if ($this->moduleManager->isEnabled('Magento_GoogleTagManager')) {
            if ($this->request->getFullActionName() === self::CATEGORY_VIEW_ACTION_NAME) {
                return $this->layout->getBlock('product_list_impression')->toHtml();
            }

            if ($this->request->getFullActionName() === self::CATALOG_SEARCH_ACTION_NAME) {
                return $this->layout->getBlock('search_result_impression')->toHtml();
            }
        }

        return '';
    }

    /**
     * Retrieve navigation object block
     *
     * @return AbstractBlock|bool
     */
    private function getNavigationBlock()
    {
        if (! $this->navigationBlock) {
            if ($this->request->getFullActionName() === self::CATEGORY_VIEW_ACTION_NAME) {
                $this->navigationBlock = $this->layout->getBlock($this->vars['catalog_left_navigation_block']);
            }

            if ($this->request->getFullActionName() === self::CATALOG_SEARCH_ACTION_NAME) {
                $this->navigationBlock = $this->layout->getBlock($this->vars['catalogsearch_left_navigation_block']);
            }
        }

        return $this->navigationBlock;
    }

    /**
     * Retrieve products count
     *
     * @return string
     */
    private function getProductsCount(): string
    {
        if ($block = $this->getNavigationBlock()) {
            $count = $block->getLayer()->getProductCollection()->getSize();
            $countPrefix = $count > 1 || $count === 0 ? __('Items') : __('Item');

            return "$count $countPrefix";
        }

        return '';
    }
}
