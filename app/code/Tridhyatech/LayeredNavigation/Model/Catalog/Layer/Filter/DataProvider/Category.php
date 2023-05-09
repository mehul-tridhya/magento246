<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Catalog\Layer\Filter\DataProvider;

use Magento\Framework\Registry;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\Layer;

class Category extends \Magento\Catalog\Model\Layer\Filter\DataProvider\Category
{

    /**
     * @var array
     */
    protected $categoryIds;

    /**
     * @var Collection|null
     */
    protected $categories;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @var Layer
     */
    protected $layer;
    
    /**
     * @var int
     */
    protected $categoryId;

    /**
     * Can procced filter logic
     *
     * @var boolean
     */
    protected $canProceed;

    /**
     * @param Registry        $coreRegistry
     * @param CategoryFactory $categoryFactory
     * @param Layer           $layer
     */
    public function __construct(
        Registry $coreRegistry,
        CategoryFactory $categoryFactory,
        Layer $layer
    ) {
        parent::__construct($coreRegistry, $categoryFactory, $layer);
        $this->coreRegistry = $coreRegistry;
        $this->layer = $layer;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Retrieve categories
     *
     * @return Collection
     * @throws LocalizedException
     */
    public function getCategories()
    {
        if ($this->categories === null) {
            if ($this->categoryIds === null) {
                if ($this->categoryId) {
                    $this->categoryIds = [$this->categoryId];
                } elseif ($this->getLayer()->getCurrentCategory()) {
                    $this->categoryIds = [$this->getLayer()->getCurrentCategory()->getId()];
                }
            }

            if (!is_array($this->categoryIds)) {
                throw new LocalizedException(__('Category Ids must be array'));
            }

            /**
             * @var Collection $categories
            */
            $categories = $this->categoryFactory->create()
                ->setStoreId(
                    $this->getLayer()
                        ->getCurrentStore()
                        ->getId()
                )
                ->getCollection()
                ->addAttributeToSelect(['name', 'url_key'])
                ->addFieldToFilter('entity_id', ['in' => $this->categoryIds]);

            $this->coreRegistry->register('current_category_filter', $categories, true);
            $this->categories = $categories;
        }

        return $this->categories;
    }

    /**
     * @inheritdoc
     */
    public function getCategory()
    {
        if ($this->canProceed) {
            return $this->getCategories();
        }

        return parent::getCategory();
    }

    /**
     * @inheritDoc
     */
    public function setCategoryId($categoryId)
    {
        parent::setCategoryId($categoryId);
        $this->categoryId = $categoryId;
        return $this;
    }

    /**
     * Can proceed
     *
     * @param bool $val
     */
    public function setCanProceed($val)
    {
        $this->canProceed = (bool) $val;
        return $this;
    }

    /**
     * Get layer
     *
     * @return \Magento\Catalog\Model\Layer
     */
    protected function getLayer()
    {
        return $this->layer;
    }

    /**
     * Category
     *
     * @param array $ids
     */
    public function setCategoryIds($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        $this->categoryIds = $ids;
        return $this;
    }
}
