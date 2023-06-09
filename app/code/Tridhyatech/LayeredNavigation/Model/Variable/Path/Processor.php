<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Variable\Path;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\CatalogSearch\IsSearchResultsPage;

class Processor
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @var IsSearchResultsPage
     */
    private $isSearchResultsPage;

    /**
     * @param Config              $config
     * @param IsSearchResultsPage $isSearchResultsPage
     */
    public function __construct(Config $config, IsSearchResultsPage $isSearchResultsPage)
    {
        $this->config = $config;
        $this->isSearchResultsPage = $isSearchResultsPage;
    }

    /**
     * Get path without variables.
     *
     * @param  string $path
     * @param  array  $variables
     * @return string
     */
    public function getPathWithoutVariables(string $path, array $variables): string
    {
        $path = str_replace($this->config->getCategoryUrlPathSuffix(), '', $path);

        $parts = explode('/', $path);
        $newParts = [];
        foreach ($parts as $part) {
            if (!$part) { // save empty parts to save right count of slashes after imploding.
                $newParts[] = $part;
                continue;
            }

            $maybeVariable = explode('-', $part, 2)[0];
            if (!isset($variables[$maybeVariable])) {
                $newParts[] = $part;
            }
        }

        if ($this->isSearchResultsPage->execute($path)) {
            return implode('/', $newParts);
        }
        return implode('/', $newParts) . $this->config->getCategoryUrlPathSuffix();
    }

    /**
     * Move variables from path to params.
     *
     * @param Request $request
     * @param array   $variables
     */
    public function moveToParams(Request $request, array $variables): void
    {
        if (!$variables) {
            return;
        }

        foreach ($variables as $variable => $values) {
            $request->setParam($variable, implode(',', $values));
        }

        $request->setPathInfo($this->getPathWithoutVariables($request->getPathInfo(), $variables));
    }
}
