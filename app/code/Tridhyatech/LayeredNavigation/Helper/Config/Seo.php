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

    public const XML_PATH_INSERT_FILTERS_IN = 'ttlayerednavigation/seo/insert_in';

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
}
