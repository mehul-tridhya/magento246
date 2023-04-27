/**
* @author Tridhya Tech Team
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_CouponsList
*/

define([
    'jquery',
    'ko',
    'Magento_SalesRule/js/model/coupon',
    'mage/url',
    'Tridhyatech_CouponsList/js/model/couponcodes',
    'domReady',
    'Magento_Ui/js/modal/modal'
], function ($, ko, coupon, url, couponcodesmodel, domReady, modal) {
    'use strict';
    domReady(function () {
        couponcodesmodel.getCouponCode();
    });
    var couponCode = coupon.getCouponCode();
    var couponCodes = couponcodesmodel.getCouponCodes();
    var isModuleEnable = couponcodesmodel.getIsModuleEnable();
    var couponListType = couponcodesmodel.getCouponListType();
    var isCouponChanged = couponcodesmodel.getIsCouponChanged();
    var title = couponcodesmodel.title;
    var buttonTitle = couponcodesmodel.buttonTitle;
    var availableCouponTitle = couponcodesmodel.availableCouponTitle;
    var unavailableCouponTitle = couponcodesmodel.unavailableCouponTitle;
    isCouponChanged.subscribe(function (newValue) {
        if (newValue) {
            couponcodesmodel.getCouponCode();
            couponcodesmodel.setIsCouponChanged(false);
        };
    });
    return function (targetModule) {
        return targetModule.extend({
            defaults: {
                template: 'Tridhyatech_CouponsList/payment/discount'
            },
            couponcodes: couponCodes,
            isModuleEnable: isModuleEnable,
            couponListType: couponListType,
            title: title,
            buttonTitle: buttonTitle,
            availableCouponTitle: availableCouponTitle,
            unavailableCouponTitle: unavailableCouponTitle,
            initialize: function () {
                var self = this;
                this._super();
            },
            applyCuponCode: function (couponvalue, elementId) {
                couponCode(couponvalue);
                this.apply();
                $(elementId).modal("closeModal");
            },
            openCouponModal: function (elementId) {
                var title = this.title();
                var options = {
                    type: 'slide',
                    responsive: true,
                    innerScroll: true,
                    title: title,
                    modalClass: 'coupon-list-modal',
                    buttons: []
                };
                var popup = modal(options, $(elementId));
                $(elementId).modal("openModal");
            }
        })
    }
});
