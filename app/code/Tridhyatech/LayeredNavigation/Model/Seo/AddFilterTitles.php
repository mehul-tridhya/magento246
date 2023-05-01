<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Seo;

use Magento\Framework\Exception\NoSuchEntityException;
use Tridhyatech\LayeredNavigation\Api\FilterMetaRepositoryInterface;
use Tridhyatech\LayeredNavigation\Api\FiltersOptionsInterface;
use Tridhyatech\LayeredNavigation\Model\OptionSource\AbstractTitlePosition;
use Tridhyatech\LayeredNavigation\Model\Variable\Registry;

/**
 * @since 1.0.0
 */
class AddFilterTitles
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\Variable\Registry
     */
    private $variableRegistry;

    /**
     * @var \Tridhyatech\LayeredNavigation\Api\FiltersOptionsInterface
     */
    private $filtersOptions;

    /**
     * @var \Tridhyatech\LayeredNavigation\Api\FilterMetaRepositoryInterface
     */
    private $filterMetaRepository;

    /**
     * @param \Tridhyatech\LayeredNavigation\Model\Variable\Registry           $variableRegistry
     * @param \Tridhyatech\LayeredNavigation\Api\FiltersOptionsInterface       $filtersOptions
     * @param \Tridhyatech\LayeredNavigation\Api\FilterMetaRepositoryInterface $filterMetaRepository
     */
    public function __construct(
        Registry $variableRegistry,
        FiltersOptionsInterface $filtersOptions,
        FilterMetaRepositoryInterface $filterMetaRepository
    ) {
        $this->variableRegistry = $variableRegistry;
        $this->filtersOptions = $filtersOptions;
        $this->filterMetaRepository = $filterMetaRepository;
    }

    /**
     * Add active filter titles to title.
     *
     * @param string $title
     * @param string $position
     * @param array  $allowList
     * @param string $separator
     * @return string
     */
    public function execute(
        string $title,
        string $position,
        array $allowList,
        string $separator
    ): string {
        if (AbstractTitlePosition::POSITION_NONE === $position) {
            return $title;
        }

        $titles = $this->toTitles($this->variableRegistry->get(), $allowList);
        if (! $titles) {
            return $title;
        }

        if (AbstractTitlePosition::POSITION_BEFORE === $position) {
            $titles[] = $title;
        } else {
            array_unshift($titles, $title);
        }

        return implode($separator, $titles);
    }

    /**
     * Make titles from active filters.
     *
     * @param array $variables
     * @param array $allowList
     * @return string[]
     */
    private function toTitles(array $variables, array $allowList): array
    {
        $allowAll = in_array('all', $allowList, true);
        if (! $allowAll && in_array('category', $allowList, true)) {
            $allowList[] = 'cat';
        }

        $titles = [];
        foreach ($variables as $variable => $values) {
            try {
                if (! $this->filterMetaRepository->get($variable)->isAttribute()
                    && ! $this->filterMetaRepository->get($variable)->isCategory()
                ) {
                    continue;
                }
            } catch (NoSuchEntityException $e) {
                continue;
            }

            if (! $allowAll && ! in_array($variable, $allowList, true)) {
                continue;
            }
            foreach ($values as $value) {
                $titles[] = $this->filtersOptions->toOptionLabel($variable, $value);
            }
        }
        return array_filter($titles);
    }
}
