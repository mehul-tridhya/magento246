<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Variable;

use Magento\Framework\Exception\NoSuchEntityException;
use Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface;
use Tridhyatech\LayeredNavigation\Api\GetUrlVariablesInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;

class GetList implements GetUrlVariablesInterface
{

    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config                    $config
     * @param FilterRepositoryInterface $filterRepository
     */
    public function __construct(
        Config $config,
        FilterRepositoryInterface $filterRepository
    ) {
        $this->config = $config;
        $this->filterRepository = $filterRepository;
    }

    /**
     * @inheritDoc
     */
    public function getFromParams(array $params): array
    {
        $variables = array_map(
            static function ($value) {
                return is_string($value) ? explode('-', $value) : [];
            },
            $params
        );
        return $this->filterVariables($variables);
    }

    /**
     * @inheritDoc
     */
    public function get(string $urlPath): array
    {
        $urlPath = $this->cleanUrlPath($urlPath);
        $urlPathParts = explode('/', $urlPath);
        $variables = $this->parseVariables($urlPathParts);
        return $this->filterVariables($variables);
    }

    /**
     * Clean path.
     *
     * @param  string $urlPath
     * @return string
     */
    private function cleanUrlPath(string $urlPath): string
    {
        $urlPath = trim($urlPath, '/');
        return urldecode(str_replace($this->config->getCategoryUrlPathSuffix(), '', $urlPath));
    }

    /**
     * @inheritDoc
     */
    public function getFromAjaxParams(array $params): array
    {
        $variables = array_map(
            static function ($value) {
                return explode(',', $value);
            },
            $params
        );

        return $this->filterVariables($variables);
    }

    /**
     * Remove variables witch we don't know.
     *
     * @param  array $variables
     * @return array
     */
    private function filterVariables(array $variables): array
    {
        $result = [];
        foreach ($variables as $requestVar => $values) {
            try {
                $this->filterRepository->get((string) $requestVar);
                $result[$requestVar] = $values;
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }
        return $result;
    }

    /**
     * Parse variables and they value.
     *
     * @param  array $urlPathParts
     * @return array
     */
    private function parseVariables(array $urlPathParts): array
    {
        $variables = [];
        foreach ($urlPathParts as $urlPathPart) {
            if (false === \mb_strpos($urlPathPart, Config::FILTER_PARAM_SEPARATOR)) {
                continue;
            }

            [$requestVar, $values] = explode('-', $urlPathPart, 2);
            $values = explode('-', $values);
            $values = array_map('mb_strtolower', $values);

            if (isset($variables[$requestVar])) { // fix for old format
                $variables[$requestVar] = array_merge($variables[$requestVar], $values);
            } elseif (!isset($variables[$requestVar])) {
                $variables[$requestVar] = $values;
            }
        }
        return $variables;
    }
}
