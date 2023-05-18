/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_CouponsList
*/
var config = {
    map: {
        '*': {
            couponCode: 'Tridhyatech_CouponsList/js/coupon-codes',
        }
    },
    config: {
        mixins: {
            'Magento_SalesRule/js/view/payment/discount': {
                'Tridhyatech_CouponsList/js/view/payment/discount-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Tridhyatech_CouponsList/js/view/shipping-mixin': true
            }
        }
    }
};