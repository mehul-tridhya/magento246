/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

define([
    'jquery',
    'Tridhyatech_LayeredNavigation/js/model/real-variables',
    'tridhyatech/product-filter/action',
    'Tridhyatech_LayeredNavigation/js/model/swatch',
    'Tridhyatech_LayeredNavigation/js/view/apply-button',
    'domReady!'
], function ($, realVariables, processor, swatch, applyButton) {
    "use strict";

   return {

        auto: true,

        change: function (from, to, notPrsInt, element) {
            if (!notPrsInt) {
                from = parseInt(from);
                to = parseInt(to);
            }

            from = Math.floor(from);
            to = Math.ceil(to);

            var $ranges = $('#narrow-by-list .item a[data-request="price"]');
            if ($ranges.length) {
                $ranges.removeClass('selected');
            }

            realVariables.removeAllValues('price');
            realVariables.add('price', from + '_' + to);

            if (this.auto) {
                processor.applyChanges();
            } else {
                processor.preloadChanges();
                applyButton.showFilterButton(element, '#layered-filter-block');
            }
        },

        changeRange: function (event) {
            var $item = $(event.currentTarget);

            realVariables.removeAllValues('price');
            //Remove selected from all price ranges
            $('#narrow-by-list .item a[data-request="price"]').removeClass('selected');

            //Add current price to selected filters
            $item.addClass('selected');
            realVariables.add('price', $item.data('value'));

            var values = $item.data('value').split('_');
            values = [parseInt(values[0]), parseInt(values[1])];

            if (!values[1]) {
                values[1] = $('#product-filter-init').data('max-price');
            }

            if (typeof jQuery.tridhyatech.priceslider != 'undefined') {
                $('#slider-range').slider("option", "values", values);
                $('#filter-price-amount-from').html(values[0]);
                $('#filter-price-amount-to').html(values[1]);
            }

            if (typeof jQuery.tridhyatech.priceinput != 'undefined') {
                $('#filter-input-price-from').val(values[0]);
                $('#filter-input-price-to').val(values[1]);
            }
        }
    };
});
