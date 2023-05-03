<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterOption;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

/**
 * @since 1.0.0
 */
class CategoryCollector implements CollectorInterface
{

    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    /**
     * @param CollectionFactory $categoryCollectionFactory
     */
    public function __construct(CollectionFactory $categoryCollectionFactory)
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Collect options codes and labels.
     *
     * @param  array $options
     * @return array
     */
    public function collect(array $options): array
    {
        $categories = $this->categoryCollectionFactory
            ->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('url_key')
            ->addFieldToFilter('is_active', 1);

        foreach ($categories as $category) {
            $categoryId = $category->getId();
            $options['cat'][$categoryId] = [
                'code' => (string) $categoryId,
                'label' => strip_tags((string) $category->getName()),
            ];
        }
        return $options;
    }
}
