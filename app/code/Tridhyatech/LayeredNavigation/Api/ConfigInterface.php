<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Api;

/**
 * @deprecated since 2.10.0 - using interface can cause "Cannot instantiate interface"
 * because <preference> applies only after cache cleaning.
 * To avoid helpdesk tickets it's better to use model instead of interface.
 * @see \Tridhyatech\LayeredNavigation\Model\ConfigUtils
 */
interface ConfigInterface
{

    /**
     * Receive magento config value by store or by other scope type
     *
     * @param string      $path      full path, eg: "tt_base/general/enabled"
     * @param string|int  $scopeCode store view code or website code
     * @param string|null $scopeType
     * @return mixed
     */
    public function getConfig(string $path, $scopeCode = null, $scopeType = null);

    /**
     * Receive magento config value by store
     *
     * @param string     $path  full path, eg: "tt_base/general/enabled"
     * @param string|int $store store view code or id
     * @return mixed
     */
    public function getConfigStore(string $path, $store = null);

    /**
     * Is flag set.
     *
     * @param string $path
     * @param null   $scopeCode
     * @param null   $scopeType
     * @return bool
     */
    public function isSetFlag(string $path, $scopeCode = null, $scopeType = null): bool;
}
