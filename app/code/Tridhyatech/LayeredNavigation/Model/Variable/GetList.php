<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Variable;

use Magento\Framework\Exception\NoSuchEntityException;
use Tridhyatech\LayeredNavigation\Api\FilterMetaRepositoryInterface;
use Tridhyatech\LayeredNavigation\Api\GetUrlVariablesInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;

/**
 * @since 1.0.0
 */
class GetList implements GetUrlVariablesInterface
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @var \Tridhyatech\LayeredNavigation\Api\FilterMetaRepositoryInterface
     */
    private $filterMetaRepository;

    /**
     * @param \Tridhyatech\LayeredNavigation\Helper\Config                     $config
     * @param \Tridhyatech\LayeredNavigation\Api\FilterMetaRepositoryInterface $filterMetaRepository
     */
    public function __construct(
        Config $config,
        FilterMetaRepositoryInterface $filterMetaRepository
    ) {
        $this->config = $config;
        $this->filterMetaRepository = $filterMetaRepository;
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
     * Clean path.
     *
     * @param string $urlPath
     * @return string
     */
    private function cleanUrlPath(string $urlPath): string
    {
        $urlPath = trim($urlPath, '/');
        return urldecode(str_replace($this->config->getCategoryUrlSuffix(), '', $urlPath));
    }

    /**
     * Parse variables and they value.
     *
     * @param array $urlPathParts
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
            } else {
                $variables[$requestVar] = $values;
            }
        }
        return $variables;
    }

    /**
     * Remove variables witch we don't know.
     *
     * @param array $variables
     * @return array
     */
    private function filterVariables(array $variables): array
    {
        $result = [];
        foreach ($variables as $requestVar => $values) {
            try {
                $this->filterMetaRepository->get((string) $requestVar);
                $result[$requestVar] = $values;
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }
        return $result;
    }
}
