/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

define([], function () {
        'use strict';

        var isDebugMode = false;

        return {
            log: function () {
                if (isDebugMode) {
                    console.log('Product filter debug log:');
                    console.log(arguments);
                }
            },
        };
    }
);
