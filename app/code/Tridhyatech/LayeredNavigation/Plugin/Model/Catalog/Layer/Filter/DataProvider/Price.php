<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Model\Catalog\Layer\Filter\DataProvider;

use Tridhyatech\LayeredNavigation\Helper\Config;
use Magento\Catalog\Model\Layer\Filter\DataProvider\Price as modelPrice;

class Price
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Skip max price modification
     *
     * @param  modelPrice $subject
     * @param  mixed      $result
     * @return mixed
     */
    public function afterValidateFilter(modelPrice $subject, $result)
    {
        if (false === $result || !$this->config->isModuleEnabled()) {
            return $result;
        }

        $count = is_array($result) ? count($result) : count(explode('-', (string) $result));

        if ($count !== 2) {
            return $result;
        }

        if (!defined('Magento\CatalogSearch\Model\Layer\Filter\Price::PRICE_DELTA')) {
            return $result;
        }

        if (!empty($result[1])) {
            $result[1] += \Magento\CatalogSearch\Model\Layer\Filter\Price::PRICE_DELTA + .01;
        }

        return $result;
    }
}
