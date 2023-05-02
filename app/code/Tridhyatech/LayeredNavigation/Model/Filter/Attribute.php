<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Filter;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\StripTags;
use Magento\Store\Model\StoreManagerInterface;
use Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface;
use Tridhyatech\LayeredNavigation\Model\CollectionFilterApplier;
use Tridhyatech\LayeredNavigation\Model\FacetedData\AttributeResolver;

/**
 * @since 1.0.0
 */
class Attribute extends \Magento\CatalogSearch\Model\Layer\Filter\Attribute
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\FacetedData\AttributeResolver
     */
    private $facetedDataResolver;

    /**
     * @var \Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface
     */
    private $filterMetaRepository;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\CollectionFilterApplier
     */
    private $filterApplier;

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory                       $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface                            $storeManager
     * @param \Magento\Catalog\Model\Layer                                          $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder                  $itemDataBuilder
     * @param \Magento\Framework\Filter\StripTags                                   $tagFilter
     * @param \Tridhyatech\LayeredNavigation\Model\FacetedData\AttributeResolver $facetedDataResolver
     * @param \Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface   $filterMetaRepository
     * @param \Tridhyatech\LayeredNavigation\Model\CollectionFilterApplier       $filterApplier
     * @param array                                                                 $data
     */
    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        StripTags $tagFilter,
        AttributeResolver $facetedDataResolver,
        FilterRepositoryInterface $filterMetaRepository,
        CollectionFilterApplier $filterApplier,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $tagFilter, $data);
        $this->facetedDataResolver = $facetedDataResolver;
        $this->filterMetaRepository = $filterMetaRepository;
        $this->filterApplier = $filterApplier;
    }

    /**
     * @inheritdoc
     */
    public function apply(RequestInterface $request)
    {
        $attributeValue = $request->getParam($this->_requestVar);
        if ('' === $attributeValue || null === $attributeValue) {
            return $this;
        }

        $attributeValue = explode(',', (string) $attributeValue);
        if (! \count($attributeValue)) {
            return $this;
        }

        $attribute = $this->getAttributeModel();
        $productCollection = $this->getLayer()
            ->getProductCollection();

        foreach ($attributeValue as $index => $value) {
            $attributeValue[$index] = $this->convertAttributeValue($attribute, $value);
        }

        $this->filterApplier->applyInCondition($productCollection, $attribute->getAttributeCode(), $attributeValue);

        foreach ($attributeValue as $attributeVal) {

            $label = $this->getOptionText($attributeVal);
            $this->getLayer()
                ->getState()
                ->addFilter($this->_createItem($label, $attributeVal)->setIsActive(true));

        }
        return $this;
    }

    /**
     * Convert attribute value according to its backend type.
     *
     * @param ProductAttributeInterface $attribute
     * @param mixed $value
     * @return int|string
     */
    private function convertAttributeValue(ProductAttributeInterface $attribute, $value)
    {
        if ($attribute->getBackendType() === 'int') {
            return (int) $value;
        }

        return $value;
    }

    /**
     * @inheritdoc
     */
    protected function isOptionReducesResults($optionCount, $totalSize)
    {
        return true;
    }

    /**
     * Get data array for building attribute filter items.
     *
     * We customize logic for ability to choose multiple attribute options.
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getItemsData(): array
    {
        try {
            if (! $this->filterMetaRepository->get($this->getRequestVar())->isAttribute()) {
                return parent::_getItemsData();
            }

            $attribute = $this->getAttributeModel();
            $isAttributeFilterable =
                $this->getAttributeIsFilterable($attribute) === static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS;

            return $this->facetedDataResolver->resolve(
                $this->getRequestVar(),
                $attribute,
                $this->getLayer(),
                $isAttributeFilterable
            );
        } catch (LocalizedException $exception) {
            return parent::_getItemsData();
        }
    }
}
