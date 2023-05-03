<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterOption;

use Tridhyatech\LayeredNavigation\Model\FilterList;

class AttributeCollector implements CollectorInterface
{

    /**
     * @var FilterList
     */
    private $filterList;

    /**
     * @param FilterList $filterList
     */
    public function __construct(FilterList $filterList)
    {
        $this->filterList = $filterList;
    }

    /**
     * Collect options codes and labels.
     *
     * @param  array $options
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
