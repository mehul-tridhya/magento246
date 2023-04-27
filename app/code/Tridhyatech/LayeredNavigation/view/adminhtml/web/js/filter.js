/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
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
