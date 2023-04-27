<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterOption;

use Tridhyatech\LayeredNavigation\Model\FilterList;

/**
 * @since 1.0.0
 */
class AttributeCollector implements CollectorInterface
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\FilterList
     */
    private $filterList;

    /**
     * @param \Tridhyatech\LayeredNavigation\Model\FilterList $filterList
     */
    public function __construct(FilterList $filterList)
    {
        $this->filterList = $filterList;
    }

    /**
     * Collect options codes and labels.
     *
     * @param array $options
     * @return array
     */
    public function collect(array $options): array
    {
        foreach ($this->filterList->getFilters() as $attributeCode => $attribute) {
            $options[$attributeCode] = $options[$attributeCode] ?? [];

            if ($attribute->getOptions()) {
                foreach ($attribute->getOptions() as $option) {
                    if (is_array($option->getValue())) {
                        continue;
                    }

                    $value = (string) $option->getValue();
                    $options[$attributeCode][$value] = [
                        'code' => $value,
                        'label' => strip_tags((string) $option->getLabel()),
                    ];
                }
            }
        }

        return $options;
    }
}
