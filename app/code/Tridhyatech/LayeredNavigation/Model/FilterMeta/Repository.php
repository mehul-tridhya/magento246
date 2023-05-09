<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterMeta;

use Magento\Framework\Exception\NoSuchEntityException;
use Tridhyatech\LayeredNavigation\Api\Data\FilterInterface;
use Magento\Catalog\Model\Product\ProductList\Toolbar;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Tridhyatech\LayeredNavigation\Model\FilterMeta\Factory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface;

class Repository implements FilterRepositoryInterface
{

    /**
     * Toolbar variables
     *
     * @var array
     */
    private $toolbarVars = [
        Toolbar::ORDER_PARAM_NAME,
        Toolbar::DIRECTION_PARAM_NAME,
        Toolbar::MODE_PARAM_NAME,
        Toolbar::LIMIT_PARAM_NAME
    ];

    /**
     * @var CollectionFactory
     */
    protected $productAttributeCollectionFactory;

    /**
     * @var array|null
     */
    protected $variables;

    /**
     * @var Attribute
     */
    protected $attributeConfig;

    /**
     * @var Factory
     */
    protected $filterMetaFactory;

    /**
     * @param Factory           $filterMetaFactory
     * @param Attribute         $attributeConfig
     * @param CollectionFactory $productAttributeCollectionFactory
     * @param array             $toolbarVars
     */
    public function __construct(
        Factory $filterMetaFactory,
        Attribute $attributeConfig,
        CollectionFactory $productAttributeCollectionFactory,
        array $toolbarVars = []
    ) {
        $this->toolbarVars = array_merge($this->toolbarVars, $toolbarVars);
        $this->filterMetaFactory = $filterMetaFactory;
        $this->attributeConfig = $attributeConfig;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
    }

    /**
     * Get list of filters meta.
     *
     * @return array
     */
    public function getList(): array
    {
        if (null === $this->variables) {
            foreach ($this->toolbarVars as $toolbarVar) {
                $this->variables[$toolbarVar] = $this->filterMetaFactory->create(
                    $toolbarVar,
                    FilterInterface::TYPE_TOOLBAR_VAR
                );
            }

            $getAttributes = $this->getFilterableAttributes();
            foreach ($getAttributes as $attribute) {
                $this->variables[$attribute->getAttributeCode()] = $this->filterMetaFactory->create(
                    $attribute->getAttributeCode(),
                    FilterInterface::TYPE_ATTRIBUTE
                );
            }

            $this->variables['cat'] = $this->filterMetaFactory->create(
                'cat',
                FilterInterface::TYPE_CATEGORY
            );
        }

        return $this->variables;
    }

    /**
     * Get filter meta.
     *
     * @param  string $requestVar
     * @return FilterInterface
     * @throws NoSuchEntityException
     */
    public function get(string $requestVar): FilterInterface
    {
        if (!isset($this->getList()[$requestVar])) {
            throw NoSuchEntityException::singleField('requestVar', $requestVar);
        }
        return $this->getList()[$requestVar];
    }

    /**
     * Return Filterable Attribute
     *
     * @return CollectionFactory
     */
    public function getFilterableAttributes()
    {
        $productAttributes = $this->productAttributeCollectionFactory->create();
        $productAttributes->setItemObjectClass($this->attributeConfig::class)
            ->setOrder('position', 'ASC');
        $productAttributes->addIsFilterableFilter();
        return $productAttributes;
    }
}
