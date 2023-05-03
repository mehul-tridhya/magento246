<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Seo;

use Magento\Framework\View\Page\Config as PageConfig;
use Tridhyatech\LayeredNavigation\Helper\Config\Seo;
use Tridhyatech\LayeredNavigation\Model\Seo\AddFilterTitles;

/**
 * @since 1.0.0
 */
class MetaTitleResolver
{

    /**
     * @var Seo
     */
    private $seoConfig;

    /**
     * @var AddFilterTitles
     */
    private $addFilterTitles;

    /**
     * @param Seo             $seoConfig
     * @param AddFilterTitles $addFilterTitles
     */
    public function __construct(
        Seo $seoConfig,
        AddFilterTitles $addFilterTitles
    ) {
        $this->seoConfig = $seoConfig;
        $this->addFilterTitles = $addFilterTitles;
    }

    /**
     * Get pae titles with active filter titles.
     *
     * @param  PageConfig $pageConfig
     * @return string
     */
    public function resolve(PageConfig $pageConfig): string
    {
        return $this->addFilterTitles->execute(
            $pageConfig->getTitle()->get(),
            $this->seoConfig->getFilterMetaTitlePosition(),
            $this->seoConfig->getMetaTitleFilters(),
            $this->seoConfig->getFilterMetaTitleSeparator()
        );
    }
}
