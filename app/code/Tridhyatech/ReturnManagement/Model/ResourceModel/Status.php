<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */
namespace Tridhyatech\ReturnManagement\Model\ResourceModel;

class Status extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init('tt_rma_status', 'entity_id');
    }
}
