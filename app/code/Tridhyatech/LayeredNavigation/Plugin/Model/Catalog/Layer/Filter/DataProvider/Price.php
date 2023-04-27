<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Model\Catalog\Layer\Filter\DataProvider;

use Tridhyatech\LayeredNavigation\Helper\Config;

class Price
{
    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @param \Tridhyatech\LayeredNavigation\Helper\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Skip max price modification
     *
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\Price $subject
     * @param mixed                                                  $result
     * @return mixed
     */
    public function afterValidateFilter(\Magento\Catalog\Model\Layer\Filter\DataProvider\Price $subject, $result)
    {
        if (false === $result || ! $this->config->isModuleEnabled()) {
            return $result;
        }

        $count = is_array($result) ? count($result) : count(explode('-', (string) $result));

        if ($count !== 2) {
            return $result;
        }

        if (! defined('Magento\CatalogSearch\Model\Layer\Filter\Price::PRICE_DELTA')) {
            return $result;
        }

        if (! empty($result[1])) {
            $result[1] += \Magento\CatalogSearch\Model\Layer\Filter\Price::PRICE_DELTA + .01;
        }

        return $result;
    }
}
