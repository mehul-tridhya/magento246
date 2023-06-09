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
    private $attributesRenderer;

    /**
     * @var Registry
     */
    private $variableRegistry;

    /**
     * @param Registry $variableRegistry
     * @param Renderer $attributesRenderer
     */
    public function __construct(
        Registry $variableRegistry,
        Renderer $attributesRenderer
    ) {
        $this->variableRegistry = $variableRegistry;
        $this->attributesRenderer = $attributesRenderer;
    }

    /**
     * @inheritDoc
     */
    public function toggleUrl(string $requestVar, string $itemValue): string
    {
        $variables = $this->variableRegistry->get();
        if (isset($variables[$requestVar]) && in_array($itemValue, $variables[$requestVar], false)) {
            return $this->getRemoveUrl($requestVar, $itemValue);
        }
        $removeCurrentValue = false;
        return $this->getAddUrl($requestVar, $itemValue, $removeCurrentValue);
    }

    /**
     * Create url to remove filter option.
     *
     * @param  string $requestVar
     * @param  string $itemValue
     * @return string
     */
    public function getRemoveUrl(string $requestVar, string $itemValue): string
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

        return $this->attributesRenderer->render($variables);
    }

    /**
     * Create url to add filter option.
     *
     * @param  string $requestVar
     * @param  string $itemValue
     * @param  bool   $removeCurrentValue
     * @return string
     */
    public function getAddUrl(string $requestVar, string $itemValue, bool $removeCurrentValue): string
    {
        $variables = $this->variableRegistry->get();
        if (isset($variables[$requestVar]) && !$removeCurrentValue) {
            $values = $variables[$requestVar];
            $values[] = $itemValue;
        } else {
            $values = [$itemValue];
        }
        $variables[$requestVar] = $values;

        return $this->attributesRenderer->render($variables);
    }
}
