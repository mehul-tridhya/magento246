/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

requirejs([
    'jquery',
    'Tridhyatech_LayeredNavigation/js/chosen',
    'domReady!'
], function ($, chosen) {
    'use strict';

    /* Enable chosen */
    setTimeout(function () {
        $('#prproductfilter_seo_page_heading_filters_inherit:checked').click().click();
        $('#prproductfilter_seo_meta_title_filters_inherit:checked').click().click();
        chosen.initializeChosen('.pr-chosen', {enabled: true, allIndex: 'all'});
    }, 2000);

    /* Fix for chosen in not expanded section */
    $('#prproductfilter_seo-head').on('click', function () {
        chosen.reinitializeSelect(document.getElementById('prproductfilter_seo_page_heading_filters'));
    });
    $('#prproductfilter_seo_page_heading-head').on('click', function () {
        chosen.reinitializeSelect(document.getElementById('prproductfilter_seo_page_heading_filters'));
    });
    $('#prproductfilter_seo_meta_title-head').on('click', function () {
        chosen.reinitializeSelect(document.getElementById('prproductfilter_seo_meta_title_filters'));
    });

    $('#prproductfilter_seo_page_heading_filters_inherit').on('click', function () {
        chosen.reinitializeSelect(document.getElementById('prproductfilter_seo_page_heading_filters'));
    });
    $('#prproductfilter_seo_meta_title_filters_inherit').on('click', function () {
        chosen.reinitializeSelect(document.getElementById('prproductfilter_seo_meta_title_filters'));
    });
});
