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
    private $filterRepository;
    
    /**
     * @var AttributeResolver
     */
    private $facetedResolver;

    /**
     * @param ItemFactory               $filterItemFactory
     * @param StoreManagerInterface     $storeManager
     * @param Layer                     $layer
     * @param DataBuilder               $itemDataBuilder
     * @param StripTags                 $tagFilter
     * @param AttributeResolver         $facetedResolver
     * @param FilterRepositoryInterface $filterRepository
     * @param CollectionFilterApplier   $filterApplier
     * @param array                     $data
     */
    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        StripTags $tagFilter,
        AttributeResolver $facetedResolver,
        FilterRepositoryInterface $filterRepository,
        CollectionFilterApplier $filterApplier,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $tagFilter, $data);
        $this->filterRepository = $filterRepository;
        $this->filterApplier = $filterApplier;
        $this->facetedResolver = $facetedResolver;
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
            $attributeValue[$index] = $this->convertAttributeValueInInt($attribute, $value);
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
            if (! $this->filterRepository->get($this->getRequestVar())->isAttribute()) {
                return parent::_getItemsData();
            }

            $attribute = $this->getAttributeModel();
            $isAttributeFilterable =
                $this->getAttributeIsFilterable($attribute) === static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS;

            return $this->facetedResolver->resolve(
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
    private function convertAttributeValueInInt(ProductAttributeInterface $attribute, $value)
    {
        if ($attribute->getBackendType() === 'int') {
            return (int) $value;
        }

        return $value;
    }
}
