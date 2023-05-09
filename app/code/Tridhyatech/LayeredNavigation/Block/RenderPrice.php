<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Block;

use Magento\Framework\Json\Helper\Data;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Registry;

class RenderPrice extends Template
{

    public const FILTER_PRICE_REQUEST_VAR = 'price';
    public const FILTER_PRICE_SLIDER_TEMPLATE = 'Tridhyatech_LayeredNavigation::layer/renderer/price/slider.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var Collection $originalCollection
     */
    protected $originalCollection = null;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * @var CollectionFactory
     */
    protected $productFactory;

    /**
     * @param Context           $context
     * @param Data              $jsonHelper
     * @param Registry          $registry
     * @param CollectionFactory $productFactory
     * @param array             $data
     */
    public function __construct(
        Context $context,
        Data $jsonHelper,
        Registry $registry,
        CollectionFactory $productFactory,
        array $data = []
    ) {
        $this->_jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->productFactory = $productFactory;
    }

    /**
     * Render price template.
     *
     * @return string
     */
    protected function renderPriceSlider(): string
    {
        $this->setTemplate(self::FILTER_PRICE_SLIDER_TEMPLATE);
        return parent::_toHtml();
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $this->assign('filterItems', $this->getItems());
        $html = $this->renderPriceSlider();
        $this->assign('filterItems', []);
        return $html;
    }

    /**
     * Get Items
     *
     * @return array
     */
    public function getItems()
    {
        return $this->getFilter()->getItems();
    }

    /**
     * Retrieve "to'" value
     *
     * @return string
     */
    public function getToValue()
    {
        $fromRequest = $this->_getRequestedPrice();
        return $fromRequest['max'] ?? $this->getMaxValue();
    }

    /**
     * Retrieve from value
     *
     * @return string
     */
    public function getFromValue()
    {
        $fromRequest = $this->_getRequestedPrice();
        return $fromRequest['min'] ?? $this->getMinValue();
    }

    /**
     * Get original collection min price value
     *
     * @return float
     */
    private function getOriginalMinValue()
    {
        /**
         * @var Collection $collection 
        */
        $collection = $this->getOriginalCollection();

        return $collection->getMinPrice();
    }

    /**
     * Get min value
     *
     * @return string
     */
    public function getMinValue()
    {
        if (null !== $this->_request->getParam(self::FILTER_PRICE_REQUEST_VAR)
            && !$this->_request->isXmlHttpRequest()
        ) {
            return $this->getOriginalMinValue();
        }

        return $this->getFilter()->getLayer()
            ->getProductCollection()
            ->getMinPrice();
    }

    /**
     * Get max value
     *
     * @return string
     */
    public function getMaxValue()
    {
        if (null !== $this->_request->getParam(self::FILTER_PRICE_REQUEST_VAR)
            && !$this->_request->isXmlHttpRequest()
        ) {
            return $this->getOriginalMaxValue();
        }

        return $this->getFilter()->getLayer()
            ->getProductCollection()
            ->getMaxPrice();
    }

    /**
     * Retrieve not filtered collection with price data
     *
     * @return Collection\
     */
    private function getOriginalCollection()
    {
        if (null === $this->originalCollection) {
            $category = $this->registry->registry('current_category');
            $this->originalCollection = $this->productFactory->create()
                ->addPriceData();

            if ($category) {
                $this->originalCollection->addCategoryFilter($category);
            }
        }

        return $this->originalCollection;
    }

    /**
     * Retrieve prices from request
     *
     * @return string
     */
    public function getRequestedPrice()
    {
        $result = $this->_getRequestedPrice();
        return $this->_jsonHelper->jsonEncode($result);
    }

    /**
     * Get original collection max price value
     *
     * @return float
     */
    private function getOriginalMaxValue()
    {
        /**
         * @var Collection $collection 
        */
        $collection = $this->getOriginalCollection();

        return $collection->getMaxPrice();
    }

    /**
     * Retrieve currency symbol
     *
     * @return string
     */
    public function getCurrencySymbol()
    {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCurrencySymbol();
    }

    /**
     * Get current currency rate
     *
     * @return float
     */
    public function getCurrentCurrencyRate()
    {
        $currencyRate = (float)$this->_storeManager->getStore()->getCurrentCurrencyRate();
        return $currencyRate ?: 1;
    }

    /**
     * Retrieve requested price
     *
     * @return array
     */
    private function _getRequestedPrice()
    {
        $result = [];
        if ($this->_request->getParam(self::FILTER_PRICE_REQUEST_VAR)) {
            $prices = array_map(
                'floatval',
                explode('-', $this->_request->getParam(self::FILTER_PRICE_REQUEST_VAR))
            );

            if (!empty($prices[0])) {
                $result['min'] = round($prices[0] * $this->getCurrentCurrencyRate(), 2);
            }

            if (!empty($prices[1])) {
                $result['max'] = round($prices[1] * $this->getCurrentCurrencyRate(), 2);
            }
        }

        return $result;
    }

}
