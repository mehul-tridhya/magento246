<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Catalog\Layer\Filter;

use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager;
use Tridhyatech\LayeredNavigation\Api\ItemUrlBuilderInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Magento\Framework\Exception\LocalizedException;

/**
 * @since 1.0.0
 */
class Item extends \Magento\Catalog\Model\Layer\Filter\Item
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ItemUrlBuilderInterface
     */
    private $itemUrl;

    /**
     * @param UrlInterface            $url
     * @param Pager                   $htmlPagerBlock
     * @param Config                  $config
     * @param ItemUrlBuilderInterface $itemUrl
     * @param array                   $data
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
     * @throws     LocalizedException
     */
    public function getUrl()
    {

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
     * @throws LocalizedException
     */
    public function getRemoveUrl()
    {
        return $this->itemUrl->getRemoveFilterUrl($this->getFilter()->getRequestVar(), $this->getValueString());
    }

    /**
     * Get item value as string.
     *
     * Price value should be imploded by underscore (_) instead of comma ','.
     *
     * @return string
     * @throws LocalizedException
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
