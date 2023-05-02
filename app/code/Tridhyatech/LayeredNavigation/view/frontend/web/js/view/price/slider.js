/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

define([
    'jquery',
    'Tridhyatech_LayeredNavigation/js/model/price',
    'tridhyatech/noUiSlider',
    'domReady!'
], function ($, price, noUiSlider) {
    "use strict";

    $.widget('tridhyatech.priceslider', {

        initedElement: $('#product-filter-init'),

        currentRanges : [],

        _create: function () {
            var self = this,
                emptyPriceRequest = Object.keys(self.options.request).length === 0;

            if (!this.initedElement.data('max-price') || emptyPriceRequest) {
                this.initedElement.data('max-price', self.options.max);
                this.initedElement.data('min-price', self.options.min);
            }

            var values = [];
            if (this.options.request.min) {
                self.currentRanges[0] = Math.floor(this.options.request.min);
                values.push(this.options.request.min);
            } else {
                self.currentRanges[0] = self.options.min;
                values.push(self.options.min);
            }

            if (this.options.request.max) {
                self.currentRanges[1] = Math.ceil(this.options.request.max);
                values.push(this.options.request.max);
            } else {
                self.currentRanges[1] = self.options.max;
                values.push(self.options.max);
            }

            try {
                var slider = this.element[0];
                noUiSlider.create(slider, {
                    start: values,
                    connect: true,
                    step: 1,
                    range: {
                        'min': this.initedElement.data('min-price'),
                        'max': this.initedElement.data('max-price')
                    },
                    format: {
                        // 'to' the formatted value. Receives a number.
                        to: function (value) {
                            return value.toFixed(0);
                        },
                        // 'from' the formatted value.
                        // Receives a string, should return a number.
                        from: function (value) {
                            return Number(value).toFixed(0);
                        }
                    }
                });

                slider.noUiSlider.on('update', function (values, handle) {
                    self.currentRanges = values;
                    self.changeInputValue(self.currentRanges[0], self.currentRanges[1]);
                });

                slider.noUiSlider.on('set', function () {
                    price.change(
                        self.currentRanges[0]/self.options.rate,
                        self.currentRanges[1]/self.options.rate,
                        true,
                        this.target
                    );
                });
            } catch (err) {
            }
        },

        changeRangeHtml: function (from, to) {
            $(this.options.amountFrom).html(from);
            $(this.options.amountTo).html(to);
        },

        changeInputValue: function (from, to) {
            this.changeRangeHtml(from, to);
            if ($('div[data-filter-type="input"]').length) {
                var inputFrom = document.querySelector(this.options.inputFrom);
                var inputTo = document.querySelector(this.options.inputTo);

                if (inputFrom && inputFrom.value !== from) {
                    inputFrom.value = from;
                }
                if (inputTo && inputTo.value !== to) {
                    inputTo.value = to;
                }
            }
        }
    });

    return $.tridhyatech.priceslider;
});