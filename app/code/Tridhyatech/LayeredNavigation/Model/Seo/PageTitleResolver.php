<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Seo;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Tridhyatech\LayeredNavigation\Helper\Config\Seo;
use Tridhyatech\LayeredNavigation\Model\AjaxResponse;
use Tridhyatech\LayeredNavigation\Model\Seo\AddFilterTitles;

/**
 * @since 1.0.0
 */
class PageTitleResolver
{

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var Seo
     */
    private $seoConfig;

    /**
     * @var AddFilterTitles
     */
    private $addFilterTitles;

    /**
     * @param Registry        $coreRegistry
     * @param Seo             $seoConfig
     * @param AddFilterTitles $addFilterTitles
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
     * @param  Http $request
     * @return string
     */
    public function resolve(Http $request): string
    {
        if (AjaxResponse::CATEGORY_VIEW_ACTION_NAME === $request->getFullActionName()) {
            $category = $this->coreRegistry->registry('current_category');
            if (!$category || !$category->getName()) {
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
