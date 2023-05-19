<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */
namespace Tridhyatech\ReturnManagement\Model;

use Tridhyatech\ReturnManagement\Api\Data\ReturnResolutionInterface;

class Resolution extends \Magento\Framework\Model\AbstractModel implements ReturnResolutionInterface
{
    
    const CACHE_TAG = 'tridhyatech_ttrma_resolution';
    
    protected $_cacheTag = 'tridhyatech_ttrma_resolution';
    
    protected $_eventPrefix = 'tridhyatech_ttrma_resolution';
    
    protected function _construct()
    {
        $this->_init(\Tridhyatech\ReturnManagement\Model\ResourceModel\Resolution::class);
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

    public function getStatus(){
        return $this->getData(self::STATUS);
    }

    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }

    public function getPosition(){
        return $this->getData(self::POSITION);
    }

    public function setPosition($position){
        return $this->setData(self::POSITION, $position);
    }
}
