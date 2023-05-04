<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Helper\Config;

use Magento\Catalog\Model\Layer\FilterList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Tridhyatech\LayeredNavigation\Model\Utils\Config;

/**
 * @since 1.0.0
 */
class Attribute extends AbstractHelper
{

    public const XML_PATH_SHOW_EMPTY = 'ttlayerednavigation/general/empty_option';

    /**
     * @var Config
     */
    private $configUtils;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @param Context                     $context
     * @param Config                      $configUtils
     * @param SerializerInterface         $serializer
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        Config $configUtils,
        SerializerInterface $serializer,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->configUtils = $configUtils;
        $this->serializer = $serializer;
        $this->registry = $registry;
    }

    /**
     * Show filters with empty results.
     *
     * @return bool
     */
    public function shouldShowEmpty(): bool
    {
        return $this->configUtils->isSetFlag(self::XML_PATH_SHOW_EMPTY);
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
}
