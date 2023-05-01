/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

define([
    'jquery'
], function ($) {
        'use strict';

        return {
            data: {},
            created: false,

            init: function (options) {
                if (options.itemSelector) {
                    this.created = true;
                    varienGlobalEvents.attachEventHandler('formSubmit', function (event) {
                        this.generate(options);
                    }.bind(this));
                    return this.options;
                }
            },

            generate: function (options) {
                var $items = $(options.attributeEnabledListSelector).find(options.itemSelector);
                var _data = {};

                $items.each(function () {
                    _data[$(this).data('value')] = $(this).data('label');
                });

                this.data = _data;

                $(options.elementId).val(JSON.stringify(_data));
            }
        };
    }
);
