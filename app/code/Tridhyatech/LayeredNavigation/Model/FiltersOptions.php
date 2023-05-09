<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface;
use Tridhyatech\LayeredNavigation\Api\FiltersOptionsInterface;
use Tridhyatech\LayeredNavigation\Model\FilterOption\CollectorInterface;

class FiltersOptions implements FiltersOptionsInterface
{

    public const ATTRIBUTES_CACHE_IDENTIFIER = 'product_filter_attribute_cache';

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var array|null
     */
    private $options;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var FilterRepositoryInterface
     */
    private $filterRepository;

    /**
     * @var CollectorInterface[]
     */
    private $filterOptionCollectors;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param CacheInterface            $cache
     * @param SerializerInterface       $serializer
     * @param FilterRepositoryInterface $filterRepository
     * @param StoreManagerInterface     $storeManager
     * @param array                     $filterOptionCollectors
     */
    public function __construct(
        CacheInterface $cache,
        SerializerInterface $serializer,
        FilterRepositoryInterface $filterRepository,
        StoreManagerInterface $storeManager,
        array $filterOptionCollectors = []
    ) {
        $this->cache = $cache;
        $this->serializer = $serializer;
        $this->filterRepository = $filterRepository;
        $this->storeManager = $storeManager;
        $this->filterOptionCollectors = $filterOptionCollectors;
    }

    /**
     * Get attribute options.
     *
     * @param  string $requestVar
     * @return array
     * [
     *     'option_id' => [
     *         'code' => 'code',
     *         'label' => 'label',
     *     ],
     * ]
     */
    public function getOptions(string $requestVar): array
    {
        return $this->getAllOptions()[$requestVar] ?? [];
    }

    /**
     * Get attribute option id by its escaped label.
     *
     * @param  string $requestVar
     * @param  string $optionCode
     * @return int|string
     */
    public function toOptionValue(string $requestVar, string $optionCode)
    {
        $filterOptions = $this->getOptions($requestVar);
        foreach ($filterOptions as $optionId => $filterOption) {
            if ($optionCode === $filterOption['code']) {
                return $optionId;
            }
        }
        return 0;
    }

    /**
     * Get attribute option label by its id.
     *
     * @param  string     $requestVar
     * @param  int|string $optionValue
     * @return string
     */
    public function toOptionLabel(string $requestVar, $optionValue): string
    {
        try {
            $filterMeta = $this->filterRepository->get($requestVar);
            if ($filterMeta->isToolbarVariable()) {
                return (string) $optionValue;
            }
        } catch (NoSuchEntityException $e) {
            return (string) $optionValue;
        }

        $filterOptions = $this->getOptions($requestVar);
        foreach ($filterOptions as $optionId => $filterOption) {
            if ((string) $optionValue === (string) $optionId) {
                return $filterOption['label'];
            }
        }
        return '';
    }

    /**
     * Get category id by url key.
     *
     * @param  string $urlKey
     * @return int
     */
    public function getCategoryId(string $urlKey): int
    {
        return (int) $urlKey;
    }

    /**
     * Get options for all filters.
     *
     * @return array[]
     */
    public function getAllOptions(): array
    {
        $this->collectOptionDetail();
        return $this->options;
    }

    /**
     * Collect options.
     *
     * @return void
     */
    private function collectOptionDetail(): void
    {
        if (null === $this->options) {
            if ($options = $this->fromCache()) {
                $this->options = $options;
                return;
            }

            $options = [];
            foreach ($this->filterOptionCollectors as $filterOptionCollector) {
                $options = $filterOptionCollector->collect($options);
            }

            $this->options = $options;
            $this->toCache($options);
        }
    }

    /**
     * Get options from cache.
     *
     * @return array
     */
    private function fromCache(): array
    {
        try {
            $identifier = self::ATTRIBUTES_CACHE_IDENTIFIER . '|' . $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $identifier = self::ATTRIBUTES_CACHE_IDENTIFIER . '|' . 0;
        }

        $data = $this->cache->load($identifier);
        if (! $data) {
            return [];
        }
        return $this->serializer->unserialize($data);
    }

    /**
     * Save options to cache.
     *
     * @param array[] $options
     */
    private function toCache(array $options): void
    {
        try {
            $identifier = self::ATTRIBUTES_CACHE_IDENTIFIER . '|' . $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            $identifier = self::ATTRIBUTES_CACHE_IDENTIFIER . '|' . 0;
        }

        $this->cache->save(
            $this->serializer->serialize($options),
            $identifier,
            [Config::CACHE_TAG],
            3600
        );
    }
}
