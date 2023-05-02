<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Helper\Config;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Tridhyatech\LayeredNavigation\Model\Utils\Config;

/**
 * @since 1.0.0
 */
class Seo extends AbstractHelper
{

    public const XML_PATH_INSERT_FILTERS_IN = 'ttlayerednavigation/seo/url/insert_in';
    public const XML_PATH_PAGE_HEADING_POSITION = 'ttlayerednavigation/seo/page_heading/position';
    public const XML_PATH_PAGE_HEADING_FILTERS = 'ttlayerednavigation/seo/page_heading/filters';
    public const XML_PATH_PAGE_HEADING_SEPARATOR = 'ttlayerednavigation/seo/page_heading/separator';
    public const XML_PATH_META_TITLE_POSITION = 'ttlayerednavigation/seo/meta_title/position';
    public const XML_PATH_META_TITLE_FILTERS = 'ttlayerednavigation/seo/meta_title/filters';
    public const XML_PATH_META_TITLE_SEPARATOR = 'ttlayerednavigation/seo/meta_title/separator';

    /**
     * @var Config
     */
    protected $configUtils;

    /**
     * @param Context $context
     * @param Config  $configUtils
     */
    public function __construct(
        Context $context,
        Config $configUtils
    ) {
        parent::__construct($context);
        $this->configUtils = $configUtils;
    }

    /**
     * Check if filters should be stored in get params.
     *
     * @param  int|null $storeId
     * @return int
     * @since  1.3.0
     */
    public function getInsertFiltersIn(int $storeId = null): int
    {
        return (int) $this->configUtils->getStoreConfig(self::XML_PATH_INSERT_FILTERS_IN, $storeId);
    }

    /**
     * Get position of active filter titles.
     *
     * @return string
     */
    public function getPageFilterTitlePosition(): string
    {
        return (string) $this->configUtils->getStoreConfig(self::XML_PATH_PAGE_HEADING_POSITION);
    }

    /**
     * Get filters to add to title.
     *
     * @return array
     */
    public function getPageTitleFilters(): array
    {
        $config = (string) $this->configUtils->getStoreConfig(self::XML_PATH_PAGE_HEADING_FILTERS);
        return $this->configUtils->prepareMultiselectValue($config);
    }

    /**
     * Get separator.
     *
     * @return string
     */
    public function getPageFilterTitleSeparator(): string
    {
        return (string) $this->configUtils->getStoreConfig(self::XML_PATH_PAGE_HEADING_SEPARATOR);
    }

    /**
     * Get position of active filter titles.
     *
     * @return string
     */
    public function getFilterMetaTitlePosition(): string
    {
        return (string) $this->configUtils->getStoreConfig(self::XML_PATH_META_TITLE_POSITION);
    }

    /**
     * Get filters to add to title.
     *
     * @return array
     */
    public function getMetaTitleFilters(): array
    {
        $config = (string) $this->configUtils->getStoreConfig(self::XML_PATH_META_TITLE_FILTERS);
        return $this->configUtils->prepareMultiselectValue($config);
    }

    /**
     * Get meta title separator.
     *
     * @return string
     */
    public function getFilterMetaTitleSeparator(): string
    {
        return (string) $this->configUtils->getStoreConfig(self::XML_PATH_META_TITLE_SEPARATOR);
    }
}
