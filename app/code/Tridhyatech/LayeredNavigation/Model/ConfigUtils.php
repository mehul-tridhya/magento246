<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Using interface can cause "Cannot instantiate interface"
 * because <preference> applies only after cache cleaning.
 * To avoid helpdesk tickets it's better to use model instead of interface.
 */
class ConfigUtils
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Receive magento config value by store
     *
     * @param  string     $path  full path, eg: "tt_base/general/enabled"
     * @param  string|int $store store view code or website code
     * @return mixed
     */
    public function getStoreConfig(string $path, $store = null)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Receive magento config value by store or by other scope type
     *
     * @param  string      $path      full path, eg: "tt_base/general/enabled"
     * @param  string|int  $scopeCode store view code or website code
     * @param  string|null $scopeType
     * @return mixed
     */
    public function getConfig(string $path, $scopeCode = null, $scopeType = null)
    {
        if ($scopeType === null) {
            $scopeType = ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * Is flag set.
     *
     * @param  string $path
     * @param  null   $scopeCode
     * @param  null   $scopeType
     * @return bool
     */
    public function isSetFlag(string $path, $scopeCode = null, $scopeType = null): bool
    {
        if ($scopeType === null) {
            $scopeType = ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->isSetFlag($path, $scopeType, $scopeCode);
    }
}
