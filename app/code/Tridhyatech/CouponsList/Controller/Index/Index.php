<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_CouponsList
 */
namespace Tridhyatech\CouponsList\Controller\Index;

use Tridhyatech\CouponsList\Model\ConfigProvider;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Index Controller to get All coupons
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     *
     * @var ConfigProvider
     */
    protected $configProvider;

    /**
     *
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Undocumented function
     *
     * @param Context              $context
     * @param ConfigProvider       $configProvider
     * @param JsonFactory          $resultJsonFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ConfigProvider $configProvider,
        JsonFactory $resultJsonFactory,
        ScopeConfigInterface $scopeConfig,
    ) {
        $this->configProvider = $configProvider;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->scopeConfig = $scopeConfig;
        return parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $resultJson = $this->resultJsonFactory->create();
        $isModuleEnable = $this->isModuleEnable();
        $couponListType = $this->getCouponListType();
        $buttonTitle = $this->getButtonTitle();
        $availableCouponTitle = $this->getAvailableCouponTitle();
        $unCouponTitle = $this->getUnavailableCouponTitle();
        $title = $this->getTitle();
        $data = [
            'couponcodes' => $this->configProvider->getCouponCodesWithDetails(),
            'is_module_enable' => $isModuleEnable,
            'coupon_list_type' => $couponListType,
            'button_title' => $buttonTitle,
            'available_coupon_title' => $availableCouponTitle,
            'unavailable_coupon_title' => $unCouponTitle,
            'title' => $title
        ];
        return $resultJson->setData($data);
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
     * Get module is module enable
     *
     * @return boolean
     */
    public function isModuleEnable()
    {
        return $this->configProvider->isModuleEnable();
    }

    /**
     * Get coupon list type
     *
     * @return string
     */
    public function getCouponListType()
    {
        return $this->configProvider->getCouponListType();
    }
    
    /**
     * Get Button Title
     *
     * @return string
     */
    public function getButtonTitle()
    {
        return $this->configProvider->getButtonTitle();
    }

    /**
     * Get Title For Available coupons
     *
     * @return string
     */
    public function getAvailableCouponTitle()
    {
        return $this->configProvider->getAvailableCouponTitle();
    }

    /**
     * Get Title For Unavailable coupons
     *
     * @return string
     */
    public function getUnavailableCouponTitle()
    {
        return $this->configProvider->getUnavailableCouponTitle();
    }

    /**
     * Get Title For All Coupons
     *
     * @return string
     */
    public function getAllCouponTitle()
    {
        return $this->configProvider->getAllCouponTitle();
    }

    /**
     * Get Title For Cart Wise Availabe Coupons
     *
     * @return string
     */
    public function getCartWiseCouponTitle()
    {
        return $this->configProvider->getCartWiseCouponTitle();
    }
}
