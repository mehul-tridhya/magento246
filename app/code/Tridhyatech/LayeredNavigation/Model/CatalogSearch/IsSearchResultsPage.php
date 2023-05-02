<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\CatalogSearch;

/**
 * @since 1.3.3
 */
class IsSearchResultsPage
{

    /**
     * Check if current url is search result page.
     *
     * @param  string $url absolute or relative url
     * @return bool
     */
    public function execute(string $url): bool
    {
        /**
         * Integration with Amasty Product Parts Finder 
        */
        if (false !== strpos($url, 'amfinder')) {
            return true;
        }

        return false !== strpos($url, 'catalogsearch/result')
            || false !== strpos($url, 'amfinder');
    }
}
