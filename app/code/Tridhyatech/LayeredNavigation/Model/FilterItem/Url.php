<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterItem;

use Tridhyatech\LayeredNavigation\Model\Variable\Renderer;
use Tridhyatech\LayeredNavigation\Model\Variable\Registry;
use Tridhyatech\LayeredNavigation\Api\ItemUrlBuilderInterface;

class Url implements ItemUrlBuilderInterface
{

    /**
     * @var Renderer
     */
    private $variablesRenderer;

    /**
     * @var Registry
     */
    private $variableRegistry;

    /**
     * @param Registry $variableRegistry
     * @param Renderer $variablesRenderer
     */
    public function __construct(
        Registry $variableRegistry,
        Renderer $variablesRenderer
    ) {
        $this->variableRegistry = $variableRegistry;
        $this->variablesRenderer = $variablesRenderer;
    }

    /**
     * @inheritDoc
     */
    public function toggleFilterUrl(string $requestVar, string $itemValue): string
    {
        $variables = $this->variableRegistry->get();
        if (isset($variables[$requestVar]) && in_array($itemValue, $variables[$requestVar], false)) {
            return $this->getRemoveFilterUrl($requestVar, $itemValue);
        }
        $removeCurrentValue = false;
        return $this->getAddFilterUrl($requestVar, $itemValue, $removeCurrentValue);
    }

    /**
     * Create url to remove filter option.
     *
     * @param  string $requestVar
     * @param  string $itemValue
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
            if (!$variables[$requestVar]) {
                unset($variables[$requestVar]);
            }
        }

        return $this->variablesRenderer->render($variables);
    }

    /**
     * Create url to add filter option.
     *
     * @param  string $requestVar
     * @param  string $itemValue
     * @param  bool   $removeCurrentValue
     * @return string
     */
    public function getAddFilterUrl(string $requestVar, string $itemValue, bool $removeCurrentValue): string
    {
        $variables = $this->variableRegistry->get();
        if (isset($variables[$requestVar]) && !$removeCurrentValue) {
            $values = $variables[$requestVar];
            $values[] = $itemValue;
        } else {
            $values = [$itemValue];
        }
        $variables[$requestVar] = $values;

        return $this->variablesRenderer->render($variables);
    }
}
