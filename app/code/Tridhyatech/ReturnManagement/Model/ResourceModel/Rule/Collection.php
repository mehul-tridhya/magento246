<?php

namespace Tridhyatech\ReturnManagement\Model\ResourceModel\Rule;

use Tridhyatech\ReturnManagement\Model\Rule as RuleModel;
use Tridhyatech\ReturnManagement\Model\ResourceModel\Rule as RuleResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            RuleModel::class,
            RuleResourceModel::class
        );
    }
}