<?php

/**
 *
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\Catalog\ResourceModel\Layer\Filter;

use Zend_Db_Expr;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;

/**
 * Attribute Class To get Attribute Code and Count
 */
class Attribute extends \Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute
{
    /**
     * Which attributes will use reseted category collection
     *
     * @var array
     */
    protected $removeFilterAttributes = ["color"];

    /**
     * Function to get count
     *
     * @param FilterInterface $filter
     * @return array
     */
    public function getCount(FilterInterface $filter)
    {
        $attribute = $filter->getAttributeModel();
        $attributeCode = $attribute->getAttributeCode();
        $storeId = $filter->getStoreId();
        $connection = $this->getConnection();
        $tableAlias = sprintf('%s_idx', $attributeCode);

        $select = clone $filter->getLayer()->getProductCollection()->getSelect();

        if (in_array($attributeCode, $this->removeFilterAttributes)) {

            $from = $select->getPart(\Magento\Framework\DB\Select::FROM);

            unset($from["search_result"]);

            $select->reset(\Magento\Framework\DB\Select::FROM);
            $select->setPart(\Magento\Framework\DB\Select::FROM, $from);
        }

        $select->reset(\Magento\Framework\DB\Select::COLUMNS);
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $select->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);

        $conditions = [
            "$tableAlias.entity_id = e.entity_id",
            $connection->quoteInto("$tableAlias.attribute_id = ?", $attribute->getAttributeId()),
            $connection->quoteInto("$tableAlias.store_id = ?", $storeId),
        ];

        $select->join(
            [$tableAlias => $this->getMainTable()],
            implode(' AND ', $conditions),
            ['value', 'count' => new Zend_Db_Expr("COUNT($tableAlias.entity_id)")]
        )->group(
            "$tableAlias.value"
        );

        return $connection->fetchPairs($select);
    }
}
