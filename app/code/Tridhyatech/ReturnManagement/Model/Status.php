<?php

/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */

namespace Tridhyatech\ReturnManagement\Model;

use Tridhyatech\ReturnManagement\Api\Data\ReturnStatusInterface;

class Status extends \Magento\Framework\Model\AbstractModel implements ReturnStatusInterface
{

    const CACHE_TAG = 'tridhyatech_ttrma_status';

    protected $_cacheTag = 'tridhyatech_ttrma_status';

    protected $_eventPrefix = 'tridhyatech_ttrma_status';

    protected function _construct()
    {
        $this->_init(\Tridhyatech\ReturnManagement\Model\ResourceModel\Status::class);
    }

    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }
    public function setEntityId($entityId)
    {
        return $this->getData(self::ENTITY_ID, $entityId);
    }


    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }
    public function setTitle($title)
    {
        return $this->getData(self::TITLE, $title);
    }


    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }
    public function setPosition($position)
    {
        return $this->getData(self::POSITION, $position);
    }


    public function getState()
    {
        return $this->getData(self::STATE);
    }
    public function setState($state)
    {
        return $this->getData(self::STATE, $state);
    }


    public function getIsInitial()
    {
        return $this->getData(self::IS_INITIAL);
    }
    public function setIsInitial($isInitial)
    {
        return $this->getData(self::IS_INITIAL, $isInitial);
    }


    public function getColor()
    {
        return $this->getData(self::COLOR);
    }
    public function setColor($color)
    {
        return $this->getData(self::COLOR, $color);
    }


    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }
    public function setStatus($status)
    {
        return $this->getData(self::STATUS, $status);
    }


    public function getRelatedEvent()
    {
        return $this->getData(self::RELATED_EVENT);
    }
    public function setRelatedEvent($relatedEvent)
    {
        return $this->getData(self::RELATED_EVENT, $relatedEvent);
    }


    public function getAdminGrid()
    {
        return $this->getData(self::ADMIN_GRID);
    }
    public function setAdminGrid($adminGrid)
    {
        return $this->getData(self::ADMIN_GRID, $adminGrid);
    }
}
