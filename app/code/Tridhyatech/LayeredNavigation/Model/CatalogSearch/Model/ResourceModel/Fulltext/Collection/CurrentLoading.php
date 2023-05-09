<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\CatalogSearch\Model\ResourceModel\Fulltext\Collection;

use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;

/**
 * Temporary fix for
 *
 * @link https://github.com/magento/magento2/issues/28919
 * TODO: remove after left support magento version with bug
 */
class CurrentLoading
{
    /**
     * @var array
     */
    private $priceData = [];

    /**
     * @var Collection|null
     */
    private $collection;

    /**
     * Set Collection
     *
     * @param  Collection $collection
     * @return $this
     */
    public function set(Collection $collection): CurrentLoading
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     * Get Collection
     *
     * @return Collection|null
     */
    public function get()
    {
        return $this->collection;
    }

    /**
     * Get Price Data
     *
     * @return array
     */
    public function getPriceData(): array
    {
        return $this->priceData;
    }

    /**
     * Set Price Data
     *
     * @param  array $priceData
     * @return $this
     */
    public function setPriceData(array $priceData): CurrentLoading
    {
        $this->priceData = $priceData;
        return $this;
    }

    /**
     * Reset Collection and Price Data
     *
     * @return $this
     */
    public function reset(): CurrentLoading
    {
        $this->collection = null;
        $this->priceData = [];
        return $this;
    }
}
