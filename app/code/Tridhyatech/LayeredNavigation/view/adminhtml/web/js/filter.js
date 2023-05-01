/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

define([
    'jquery',
    "Tridhyatech_LayeredNavigation/js/model/drag",
    "Tridhyatech_LayeredNavigation/js/model/jsonGenerator",
    "domReady!"
], function ($, drag, jsonGenerator) {
    "use strict";

    $.widget('productfilter.filterattribute', {
        _create: function () {
            jsonGenerator.init(this.options);
            drag.init(this.options);
        }
    });

    return $.productfilter.filterattribute;
});
