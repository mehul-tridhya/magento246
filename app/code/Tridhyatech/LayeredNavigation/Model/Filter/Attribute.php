<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Filter;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Framework\Filter\StripTags;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Tridhyatech\LayeredNavigation\Api\FilterRepositoryInterface;
use Tridhyatech\LayeredNavigation\Model\CollectionFilterApplier;
use Tridhyatech\LayeredNavigation\Model\FacetedData\AttributeResolver;
use Magento\Store\Model\StoreManagerInterface;

class Attribute extends \Magento\CatalogSearch\Model\Layer\Filter\Attribute
{

    /**
     * @var CollectionFilterApplier
     */
    private $filterApplier;
    
    /**
     * @var FilterRepositoryInterface
     */
    private $filterMetaRepository;
    
    /**
     * @var AttributeResolver
     */
    private $facetedDataResolver;

    /**
     * @param ItemFactory               $filterItemFactory
     * @param StoreManagerInterface     $storeManager
     * @param Layer                     $layer
     * @param DataBuilder               $itemDataBuilder
     * @param StripTags                 $tagFilter
     * @param AttributeResolver         $facetedDataResolver
     * @param FilterRepositoryInterface $filterMetaRepository
     * @param CollectionFilterApplier   $filterApplier
     * @param array                     $data
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
        $this->filterMetaRepository = $filterMetaRepository;
        $this->filterApplier = $filterApplier;
        $this->facetedDataResolver = $facetedDataResolver;
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

    /**
     * Convert attribute value according to its backend type.
     *
     * @param  ProductAttributeInterface $attribute
     * @param  mixed                     $value
     * @return int|string
     */
    private function convertAttributeValue(ProductAttributeInterface $attribute, $value)
    {
        if ($attribute->getBackendType() === 'int') {
            return (int) $value;
        }

        return $value;
    }
}
