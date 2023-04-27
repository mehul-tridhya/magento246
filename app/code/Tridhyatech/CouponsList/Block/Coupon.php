<?php

/**
 * @author    Tridhya Tech Team
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_CouponsList
 */

namespace Tridhyatech\CouponsList\Block;

use Tridhyatech\CouponsList\Model\ConfigProvider;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Coupon Provider
 */
class Coupon extends \Magento\Framework\View\Element\Template
{

    /**
     * Config Provider Variable
     *
     * @var ConfigProvider
     */
    protected $_configProvider;

    /**
     * Scope Config Provider
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     *
     * @param Context              $context
     * @param ConfigProvider       $configProvider
     * @param ScopeConfigInterface $scopeConfig
     * @param array                $data
     */
    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_configProvider = $configProvider;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Get coupon codes
     *
     * @return array
     */
    public function getCouponCodes()
    {
        return $this->_configProvider->getCouponCodesWithDetails();
    }

    /**
     * Get is module enable
     *
     * @return boolean
     */
    public function isModuleEnable()
    {
        return $this->_configProvider->isModuleEnable();
    }

    /**
     * Get coupon list type
     *
     * @return boolean
     */
    public function getCouponListType()
    {
        return $this->_configProvider->getCouponListType();
    }

    /**
     * Get title for modal based on coupon list type
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getCouponListType() == 1 ? $this->getAllCouponTitle() : $this->getCartWiseCouponTitle();
    }

    /**
     * Get Button Title
     *
     * @return string
     */
    public function getButtonTitle()
    {
        return $this->_configProvider->getButtonTitle();
    }

    /**
     * Get Title For Available coupons
     *
     * @return string
     */
    public function getAvailableCouponTitle()
    {
        return $this->_configProvider->getAvailableCouponTitle();
    }

    /**
     * Get Title For Unavailable coupons
     *
     * @return string
     */
    public function getUnavailableCouponTitle()
    {
        return $this->_configProvider->getUnavailableCouponTitle();
    }

    /**
     * Get Title For All Coupons
     *
     * @return string
     */
    public function getAllCouponTitle()
    {
        return $this->_configProvider->getAllCouponTitle();
    }

    /**
     * Get Title For Cart Wise Availabe Coupons
     *
     * @return string
     */
    public function getCartWiseCouponTitle()
    {
        return $this->_configProvider->getCartWiseCouponTitle();
    }
}
