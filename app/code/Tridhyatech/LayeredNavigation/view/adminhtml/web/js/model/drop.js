/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    return {
        init: function (options, item) {
            var droppableParams = {
                accept: options.itemSelector,
                drop: function (event, ui) {
                    event.toElement['dropTo'] = event.target;
                }
            };

            if (! item) {
                $(options.attributeEnabledListSelector)
                    .find('.attr_item')
                    .droppable(droppableParams);
            } else {
                item.droppable(droppableParams);
            }
        }
    };
});
