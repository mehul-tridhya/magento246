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
use Magento\Store\Model\ScopeInterface;
use Tridhyatech\LayeredNavigation\Model\Utils\Config;

class Attribute extends AbstractHelper
{

    /**
     * @var Config
     */
    private $configUtils;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @param Context                     $context
     * @param Config                      $configUtils
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        Config $configUtils,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->configUtils = $configUtils;
        $this->registry = $registry;
    }

    /**
     * Fix category code.
     *
     * @param  array $codes
     * @return string[]
     */
    protected function fixCodes(array $codes): array
    {
        return array_replace(
            $codes,
            array_fill_keys(
                array_keys($codes, 'categorie'),
                'category'
            )
        );
    }

    /**
     * Get config by current store/website.
     *
     * @param  string $path
     * @return string
     */
    public function getRelatedConfig(string $path): string
    {
        $scopeCode = null;
        $scopeType = null;

        if ('ttlayerednavigation' === $this->_request->getParam('section')) {
            if ($scopeCode = $this->_request->getParam('website')) {
                $scopeType = ScopeInterface::SCOPE_WEBSITE;
            } elseif ($scopeCode = $this->_request->getParam('store')) {
                $scopeType = ScopeInterface::SCOPE_STORE;
            } else {
                $scopeCode = 0;
            }
        } elseif ($category = $this->registry->registry('current_category')) {
            $scopeCode = $category->getStoreId();
        }
        return (string) $this->configUtils->getConfig($path, $scopeCode, $scopeType);
    }

}
