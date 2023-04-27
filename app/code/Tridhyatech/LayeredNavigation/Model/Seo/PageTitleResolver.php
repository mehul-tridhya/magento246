<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Seo;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Tridhyatech\LayeredNavigation\Helper\Config\Seo;
use Tridhyatech\LayeredNavigation\Model\AjaxResponse;

/**
 * @since 1.0.0
 */
class PageTitleResolver
{

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config\Seo
     */
    private $seoConfig;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Seo\AddFilterTitles
     */
    private $addFilterTitles;

    /**
     * @param \Magento\Framework\Registry                                 $coreRegistry
     * @param \Tridhyatech\LayeredNavigation\Helper\Config\Seo         $seoConfig
     * @param \Tridhyatech\LayeredNavigation\Model\Seo\AddFilterTitles $addFilterTitles
     */
    public function __construct(
        Registry $coreRegistry,
        Seo $seoConfig,
        AddFilterTitles $addFilterTitles
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->seoConfig = $seoConfig;
        $this->addFilterTitles = $addFilterTitles;
    }

    /**
     * Get pae titles with active filter titles.
     *
     * @param \Magento\Framework\App\Request\Http $request
     * @return string
     */
    public function resolve(Http $request): string
    {
        if (AjaxResponse::CATEGORY_VIEW_ACTION_NAME === $request->getFullActionName()) {
            $category = $this->coreRegistry->registry('current_category');
            if (! $category || ! $category->getName()) {
                return '';
            }
            return $this->addFilterTitles->execute(
                $category->getName(),
                $this->seoConfig->getPageFilterTitlePosition(),
                $this->seoConfig->getPageTitleFilters(),
                $this->seoConfig->getPageFilterTitleSeparator()
            );
        }
        return '';
    }
}
