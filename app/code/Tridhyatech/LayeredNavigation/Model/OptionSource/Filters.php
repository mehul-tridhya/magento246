<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\OptionSource;

use Magento\Framework\Exception\NoSuchEntityException;
use Tridhyatech\LayeredNavigation\Model\OptionSource\AbstractSource;
use Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface;
use Tridhyatech\LayeredNavigation\Model\FilterList;

class Filters extends AbstractSource
{

    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @var FilterList
     */
    private $filterList;

    /**
     * @param FilterList                $filterList
     * @param FilterRepositoryInterface $filterRepository
     */
    public function __construct(
        FilterList $filterList,
        FilterRepositoryInterface $filterRepository
    ) {
        $this->filterList = $filterList;
        $this->filterRepository = $filterRepository;
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
                if ($this->filterRepository->get($code)->isAttribute()
                    || $this->filterRepository->get($code)->isCategory()
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
