/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

define([
    'jquery',
    'productListToolbarForm',
    'tridhyatech/product-filter/action',
    'Tridhyatech_LayeredNavigation/js/model/real-variables',
], function ($, toolbar, processor, realVariables) {

    return {
        rewrite: function () {
            $.mage.productListToolbarForm.prototype.changeUrl = this.changeUrl;
        },
        changeUrl: function (pName, pValue, defValue) {
            realVariables.removeAllValues(pName);
            realVariables.add(pName, pValue);
            processor.applyChanges();
        }
    }
});
