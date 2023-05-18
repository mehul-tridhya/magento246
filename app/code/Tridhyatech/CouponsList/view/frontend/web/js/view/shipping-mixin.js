/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_CouponsList
*/
define(
    [
        "jquery",
        'ko',
        'mage/url',
        'Tridhyatech_CouponsList/js/model/couponcodes'
    ],
    function (
        $,
        ko,
        url,
        couponcodesmodel
    ) {
        'use strict';
        var mixins = {
            couponcodes: couponcodesmodel.getCouponCodes(),
            isCouponChanged: couponcodesmodel.getIsCouponChanged(),
            setShippingInformation: function () {
                var _return = this._super(arguments);
                this.getCouponCode();
                return _return;
            },
            getCouponCode: function () {
                var self = this;
                var ajaxurl = url.build('couponcode/index/index');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    showLoader: true,
                    data: {},
                    success: function (response) {
                        couponcodesmodel.setCouponCodes([]);
                        if (response.is_module_enable) {
                            var isEnable = response.is_module_enable;
                            couponcodesmodel.setIsModuleEnable(isEnable);
                        }
                        if (response.coupon_list_type) {
                            couponcodesmodel.setCouponListType(parseInt(response.coupon_list_type));
                        }
                        if (response.couponcodes) {
                            var codes = response.couponcodes;
                            self.isCouponChanged(true);
                            self.couponcodes(codes);
                        }
                    }
                });
            }
        }
        return function (target) {
            //target = quote returned by Magento_Checkout/js/model/quote
            return target.extend(mixins); //Return quote again, it important if you don't return you can't use Magento_Checkout/js/model/quote by required.
        }
    });