<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Setup\Patch\Data;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * @since 1.0.0
 */
class EnableDefaultFilters implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var CollectionFactory
     */
    private $filterableAttributes;

    /**
     * @var Config
     */
    private $resourceConfig;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param CollectionFactory        $filterableAttributes
     * @param Config                   $resourceConfig
     * @param SerializerInterface      $serializer
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CollectionFactory $filterableAttributes,
        Config $resourceConfig,
        SerializerInterface $serializer
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->filterableAttributes = $filterableAttributes;
        $this->resourceConfig = $resourceConfig;
        $this->serializer = $serializer;
    }

    /**
     * Apply patch
     *
     * @return void
     */
    public function apply(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $collection = $this->filterableAttributes->create();
        $collection->setItemObjectClass(Attribute::class)
            ->setOrder('position', 'ASC');

        $collection->addIsFilterableFilter();

        $attributeCodes = [];
        foreach ($collection as $attribute) {
            if ($attribute->getFrontendLabel()) {
                $attributeCodes[$attribute->getAttributeCode()] = $attribute->getFrontendLabel();
            }
        }

        $value = $this->serializer->serialize($attributeCodes);
        $this->resourceConfig->saveConfig(
            \Tridhyatech\LayeredNavigation\Helper\Config\Attribute::XML_PATH_ATTRS,
            $value,
            'default'
        );

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
