/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

define([
    'underscore',
    'domReady!'
], function ($) {
    "use strict";

    return {
        updateDataLayer: function (data) {
            if (data.productlist && typeof dataLayer != 'undefined' && data.googleTagManager.length > 0) {
                $('.products-grid').append(data.googleTagManager);

                var impressions = [];
                var impressionsData;

                if (typeof staticImpressions['search_result_list'] != 'undefined') {
                    impressionsData = staticImpressions['search_result_list'];
                } else {
                    impressionsData = staticImpressions['category.products.list'];
                }

                for (var i = 0; i < impressionsData.length; i++) {
                    impressions.push({
                        'id': impressionsData[i].id,
                        'name': impressionsData[i].name,
                        'category': impressionsData[i].category,
                        'list': impressionsData[i].list,
                        'position': impressionsData[i].position
                    });
                }

                dataLayer.push({
                    'event': 'productImpression',
                    'ecommerce': {
                        'impressions': impressions
                    }
                });
            }
        }
    };
});
