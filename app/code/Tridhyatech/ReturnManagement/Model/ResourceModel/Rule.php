<?php

namespace Tridhyatech\ReturnManagement\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Rule extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('tt_rma_return_rule', 'rule_id');
    }
}