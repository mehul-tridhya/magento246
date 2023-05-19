<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */
namespace Tridhyatech\ReturnManagement\Model;

use Tridhyatech\ReturnManagement\Api\Data\ReturnReasonInterface;

class Reason extends \Magento\Framework\Model\AbstractModel implements ReturnReasonInterface
{
    
    const CACHE_TAG = 'tridhyatech_ttrma_status';
    
    protected $_cacheTag = 'tridhyatech_ttrma_status';
    
    protected $_eventPrefix = 'tridhyatech_ttrma_status';
    
    protected function _construct()
    {
        $this->_init(\Tridhyatech\ReturnManagement\Model\ResourceModel\Reason::class);
    }
    
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }
    
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }
    
    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    public function getCreatedDate()
    {
        return $this->getData(self::CREATED_DATE);
    }
    
    public function setCreatedDate($createdDate)
    {
        return $this->setData(self::CREATED_DATE, $createdDate);
    }

    public function getStatus(){
        return $this->getData(self::STATUS);
    }

    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }

    public function getShippingPayer(){
        return $this->getData(self::SHIPPING_PAYER);
    }

    public function setShippingPayer($shippingPayer){
        return $this->setData(self::SHIPPING_PAYER, $shippingPayer);
    }

    public function getPosition(){
        return $this->getData(self::POSITION);
    }

    public function setPosition($position){
        return $this->setData(self::POSITION, $position);
    }
}
