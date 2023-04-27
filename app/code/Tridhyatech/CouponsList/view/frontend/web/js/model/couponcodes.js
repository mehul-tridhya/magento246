/**
* @author Tridhya Tech Team
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_CouponsList
*/
/**
 * Coupon Code model.
 */
define([
    'jquery',
    'ko',
    'domReady!',
    'mage/url',
    
], function ($,ko, domReady, url) {
    'use strict';

    var couponCodes = ko.observableArray(null),
        isModuleEnable = ko.observable(false),
        couponListType = ko.observable(1),
        isCouponChanged = ko.observable(false),
        title = ko.observable(''),
        buttonTitle = ko.observable(''),
        availableCouponTitle = ko.observable(''),
        unavailableCouponTitle = ko.observable('');
    return {
        couponCodes: couponCodes,
        title: title,
        buttonTitle: buttonTitle,
        availableCouponTitle: availableCouponTitle,
        unavailableCouponTitle: unavailableCouponTitle,

        /**
         * @return {*}
         */
        getCouponCodes: function () {
            return couponCodes;
        },

        getIsModuleEnable: function () {
            return isModuleEnable;
        },

        setIsModuleEnable: function (isModuleEnableValue) {
            isModuleEnable(isModuleEnableValue);
        },

        getIsCouponChanged: function () {
            return isCouponChanged;
        },

        setIsCouponChanged: function (isCouponChangedValue) {
            isCouponChanged(isCouponChangedValue);
        },

        getCouponListType: function () {
            return couponListType;
        },

        setCouponListType: function (couponListTypeValue) {
            couponListType(couponListTypeValue);
        },

        setCouponCodes: function (coupons) {
            couponCodes(coupons);
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
                    self.setCouponCodes([]);
                    if (response.is_module_enable) {
                        var isEnable = response.is_module_enable;
                        self.setIsModuleEnable(isEnable);
                    }
                    if (response.coupon_list_type) {
                        self.setCouponListType(parseInt(response.coupon_list_type));
                    }
                    if (response.title) {
                        self.title(response.title);
                    }
                    if (response.button_title) {
                        self.buttonTitle(response.button_title);
                    }
                    if (response.available_coupon_title) {
                        self.availableCouponTitle(response.available_coupon_title);
                    }
                    if (response.unavailable_coupon_title) {
                        self.unavailableCouponTitle(response.unavailable_coupon_title);
                    }
                    if (response.couponcodes) {
                        self.setCouponCodes(codes);
                        var codes = response.couponcodes;
                        self.setCouponCodes(codes);
                    }
                }
            });
        }
    };
});
