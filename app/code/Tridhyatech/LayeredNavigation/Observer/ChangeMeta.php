<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Observer;

use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Theme\Block\Html\Title;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\AjaxResponse;
use Tridhyatech\LayeredNavigation\Model\Seo\MetaTitleResolver;
use Tridhyatech\LayeredNavigation\Model\Seo\PageTitleResolver;

/**
 * @since 1.0.0
 */
class ChangeMeta implements ObserverInterface
{

    /**
     * @var PageConfig
     */
    private $_pageConfig;

    /**
     * @var Resolver
     */
    private $_catalogLayer;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var Http
     */
    private $request;

    /**
     * @var PageTitleResolver
     */
    private $pageTitleResolver;

    /**
     * @var MetaTitleResolver
     */
    private $metaTitleResolver;

    /**
     * @param PageConfig        $pageConfig
     * @param Resolver          $layerResolver
     * @param Config            $config
     * @param Http              $request
     * @param PageTitleResolver $pageTitleResolver
     * @param MetaTitleResolver $metaTitleResolver
     */
    public function __construct(
        PageConfig $pageConfig,
        Resolver $layerResolver,
        Config $config,
        Http $request,
        PageTitleResolver $pageTitleResolver,
        MetaTitleResolver $metaTitleResolver
    ) {
        $this->_catalogLayer = $layerResolver->get();
        $this->_pageConfig = $pageConfig;
        $this->config = $config;
        $this->request = $request;
        $this->pageTitleResolver = $pageTitleResolver;
        $this->metaTitleResolver = $metaTitleResolver;
    }

    /**
     * Changing attribute values
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isModuleEnabled()
            && AjaxResponse::CATEGORY_VIEW_ACTION_NAME === $observer->getFullActionName()
        ) {
            /**
             * @var LayoutInterface $layout 
             */
            $layout = $observer->getLayout();

            $this->setCategoryPageH1($layout);
            $this->setPageMetaTitle($this->_pageConfig);
            $this->setRobots();
            $this->setCanonical();
        }
    }

    /**
     * Set robots to page with active filters.
     *
     * @return void
     */
    public function setRobots(): void
    {
        if (!$this->_catalogLayer->getState()->getFilters()) {
            return;
        }
        $this->_pageConfig->setRobots('NOINDEX,FOLLOW');
    }

    /**
     * Set H1 for category pages.
     *
     * @param  LayoutInterface $layout
     * @return void
     */
    private function setCategoryPageH1(LayoutInterface $layout): void
    {
        $h1 = $this->pageTitleResolver->resolve($this->request);
        $titleBlock = $layout->getBlock('page.main.title');
        if ($h1 && $titleBlock instanceof Title) {
            $titleBlock->setPageTitle($h1);
        }
    }

    /**
     * Set meta title with active filters.
     *
     * @param  PageConfig $pageConfig
     * @return void
     */
    private function setPageMetaTitle(PageConfig $pageConfig): void
    {
        $metaTitle = $this->metaTitleResolver->resolve($pageConfig);
        if ($metaTitle) {
            $pageConfig->getTitle()->set($metaTitle);
        }
    }

    /**
     * Set canonical url.
     *
     * @return void
     */
    private function setCanonical(): void
    {
        if ($this->_catalogLayer->getState()->getFilters()
            && $this->_pageConfig->getAssetCollection()->getGroupByContentType('canonical')
        ) {
            //Remove current canonical url and add new
            $canonicals = $this->_pageConfig->getAssetCollection()
                ->getGroupByContentType('canonical')
                ->getAll();
            $canonical = array_shift($canonicals);
            $this->_pageConfig->getAssetCollection()->remove($canonical->getUrl());

            $this->_pageConfig->addRemotePageAsset(
                $this->config->getCanonicalUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }
    }
}
