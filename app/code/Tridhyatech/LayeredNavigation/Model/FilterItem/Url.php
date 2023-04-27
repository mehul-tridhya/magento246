<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterItem;

use Tridhyatech\LayeredNavigation\Api\FilterItemUrlBuilderInterface;
use Tridhyatech\LayeredNavigation\Model\Variable\Registry;
use Tridhyatech\LayeredNavigation\Model\Variable\Renderer;

/**
 * @since 1.0.0
 */
class Url implements FilterItemUrlBuilderInterface
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Registry
     */
    private $variableRegistry;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Renderer
     */
    private $variablesRenderer;

    /**
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Registry $variableRegistry
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Renderer $variablesRenderer
     */
    public function __construct(
        Registry $variableRegistry,
        Renderer $variablesRenderer
    ) {
        $this->variableRegistry = $variableRegistry;
        $this->variablesRenderer = $variablesRenderer;
    }

    /**
     * Create url to add filter option.
     *
     * @param string $requestVar
     * @param string $itemValue
     * @param bool   $removeCurrentValue
     * @return string
     */
    public function getAddFilterUrl(string $requestVar, string $itemValue, bool $removeCurrentValue = false): string
    {
        $variables = $this->variableRegistry->get();
        if (isset($variables[$requestVar]) && ! $removeCurrentValue) {
            $values = $variables[$requestVar];
            $values[] = $itemValue;
        } else {
            $values = [$itemValue];
        }
        $variables[$requestVar] = $values;

        return $this->variablesRenderer->render($variables);
    }

    /**
     * Create url to remove filter option.
     *
     * @param string $requestVar
     * @param string $itemValue
     * @return string
     */
    public function getRemoveFilterUrl(string $requestVar, string $itemValue): string
    {
        $variables = $this->variableRegistry->get();
        if (isset($variables[$requestVar])) {
            $index = array_search($itemValue, $variables[$requestVar], false);
            if (false !== $index) {
                unset($variables[$requestVar][$index]);
            }
            if (! $variables[$requestVar]) {
                unset($variables[$requestVar]);
            }
        }

        return $this->variablesRenderer->render($variables);
    }

    /**
     * @inheritDoc
     */
    public function toggleFilterUrl(string $requestVar, string $itemValue, bool $removeCurrentValue = false): string
    {
        $variables = $this->variableRegistry->get();
        if (isset($variables[$requestVar]) && in_array($itemValue, $variables[$requestVar], false)) {
            return $this->getRemoveFilterUrl($requestVar, $itemValue);
        }
        return $this->getAddFilterUrl($requestVar, $itemValue, $removeCurrentValue);
    }
}
