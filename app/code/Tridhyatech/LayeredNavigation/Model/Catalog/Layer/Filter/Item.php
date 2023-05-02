<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Catalog\Layer\Filter;

use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager;
use Tridhyatech\LayeredNavigation\Api\ItemUrlBuilderInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;

/**
 * @since 1.0.0
 */
class Item extends \Magento\Catalog\Model\Layer\Filter\Item
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @var \Tridhyatech\LayeredNavigation\Api\ItemUrlBuilderInterface
     */
    private $itemUrl;

    /**
     * @param \Magento\Framework\UrlInterface                                     $url
     * @param \Magento\Theme\Block\Html\Pager                                     $htmlPagerBlock
     * @param \Tridhyatech\LayeredNavigation\Helper\Config                     $config
     * @param \Tridhyatech\LayeredNavigation\Api\ItemUrlBuilderInterface $itemUrl
     * @param array                                                               $data
     */
    public function __construct(
        UrlInterface $url,
        Pager $htmlPagerBlock,
        Config $config,
        ItemUrlBuilderInterface $itemUrl,
        array $data = []
    ) {
        $this->config = $config;
        $this->itemUrl = $itemUrl;
        parent::__construct($url, $htmlPagerBlock, $data);
    }

    /**
     * @inheritDoc
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUrl()
    {
        if (! $this->config->isModuleEnabled()) {
            return parent::getUrl();
        }

        if ($this->getIsActive()) {
            return $this->getRemoveUrl();
        }

        return $this->itemUrl->getAddFilterUrl(
            $this->getFilter()->getRequestVar(),
            $this->getValueString(),
            (bool) $this->getFilter()->getIsRadio()
        );
    }

    /**
     * Rewrite default remove url.
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRemoveUrl()
    {
        if (! $this->config->isModuleEnabled()) {
            return parent::getUrl();
        }
        return $this->itemUrl->getRemoveFilterUrl($this->getFilter()->getRequestVar(), $this->getValueString());
    }

    /**
     * Get item value as string.
     *
     * Price value should be imploded by underscore (_) instead of comma ','.
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getValueString()
    {
        $value = $this->getValue();
        if ('price' === $this->getFilter()->getRequestVar() && is_array($value)) {
            return implode('_', $value);
        }
        return parent::getValueString();
    }
}
