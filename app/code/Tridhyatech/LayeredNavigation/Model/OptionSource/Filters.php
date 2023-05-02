<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\OptionSource;

use Magento\Framework\Exception\NoSuchEntityException;
use Tridhyatech\LayeredNavigation\Model\OptionSource\AbstractSource;
use Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface;
use Tridhyatech\LayeredNavigation\Model\FilterList;

/**
 * @since 1.0.0
 */
class Filters extends AbstractSource
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\FilterList
     */
    private $filterList;

    /**
     * @var \Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface
     */
    private $filterMetaRepository;

    /**
     * @param \Tridhyatech\LayeredNavigation\Model\FilterList                  $filterList
     * @param \Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface $filterMetaRepository
     */
    public function __construct(
        FilterList $filterList,
        FilterRepositoryInterface $filterMetaRepository
    ) {
        $this->filterList = $filterList;
        $this->filterMetaRepository = $filterMetaRepository;
    }

    /**
     * Get filter title positions.
     *
     * @return array
     */
    public function toOptionHash(): array
    {
        $options = ['all' => 'All Attributes'];
        foreach ($this->filterList->getAttributeList() as $code => $label) {
            try {
                if ($this->filterMetaRepository->get($code)->isAttribute()
                    || $this->filterMetaRepository->get($code)->isCategory()
                ) {
                    $options[$code] = $label;
                }
            } catch (NoSuchEntityException $e) {
                continue;
            }
        }
        return $options;
    }
}
