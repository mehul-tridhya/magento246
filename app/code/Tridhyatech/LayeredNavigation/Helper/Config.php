<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Helper;

use Magento\Catalog\Model\Product\ProductList\Toolbar;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Tridhyatech\LayeredNavigation\Model\Utils\Config as modelConfig;

class Config extends AbstractHelper
{

    public const XML_PATH_SCROLL_UP = 'ttlayerednavigation/general/scroll_up';
    public const FILTER_PARAM_SEPARATOR = '-';

    /**
     * Toolbar variables
     *
     * @var array
     */
    protected $toolbarVars = [
        Toolbar::PAGE_PARM_NAME,
        Toolbar::ORDER_PARAM_NAME,
        Toolbar::DIRECTION_PARAM_NAME,
        Toolbar::MODE_PARAM_NAME,
        Toolbar::LIMIT_PARAM_NAME
    ];

    /**
     * Array of allowed handles
     *
     * @var array
     */
    protected $allowedHandles = [
        'catalog_category_view',
        'catalog_category_view_type_layered',
        'catalogsearch_result_index',
    ];

    /**
     * @var modelConfig
     */
    private $configUtils;

    /**
     * @param \Magento\Framework\App\Helper\Context             $context
     * @param modelConfig $configUtils
     */
    public function __construct(
        Context $context,
        modelConfig $configUtils
    ) {
        parent::__construct($context);
        $this->configUtils = $configUtils;
    }

    /**
     * Check if we should scroll to top after filtering or other actions.
     *
     * @param  int|string|null $store
     * @return bool
     */
    public function canScrollUpAfterUpdate($store = null): bool
    {
        return $this->configUtils->isSetFlag(self::XML_PATH_SCROLL_UP, $store);
    }

    /**
     * Retrieve category suffix.
     *
     * @return string
     */
    public function getCategoryUrlPathSuffix(): string
    {
        return (string) $this->configUtils->getConfig(CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX);
    }

    /**
     * Get element to witch we should scroll up.
     *
     * @return string
     */
    public function getScrollUpClass(): string
    {
        return '.toolbar-products';
    }

    /**
     * Retrieve canonical url
     *
     * @return string
     */
    public function getCanonicalUrlFromUrl(): string
    {
        $currentUrl = $this->_urlBuilder->getCurrentUrl();
        $parts = explode('?', $currentUrl);
        return $parts[0];
    }

    /**
     * Retrieve toolbar vars
     *
     * @return array
     */
    public function getToolbarVars(): array
    {
        return $this->toolbarVars;
    }

    /**
     * Retrieve array of allowed handles
     *
     * @return array
     */
    public function getAllowedHandles(): array
    {
        return $this->allowedHandles;
    }
}
