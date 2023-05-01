/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

define([], function () {
    'use strict';

    return {
        dataCache: {},

        /**
         * Url to cache key mapping.
         * e.g. https://example.com/... -> CACHE_KEY
         *
         * Used for navigation backward and forward.
         */
        urlCacheMapping: {},

        has: function (cacheKey) {
            return this.dataCache.hasOwnProperty(cacheKey);
        },

        hasUrlCache: function (url) {
            return this.urlCacheMapping.hasOwnProperty(url);
        },

        save: function (cacheKey, data) {
            this.dataCache[cacheKey] = data;
            this.urlCacheMapping[data.nextUrl] = cacheKey;
        },

        get: function (cacheKey) {
            return this.dataCache[cacheKey];
        },

        getKeyByUrl: function (url) {
            return this.urlCacheMapping[url];
        },
    };
});
