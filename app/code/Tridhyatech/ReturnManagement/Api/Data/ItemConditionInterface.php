<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */
namespace Tridhyatech\ReturnManagement\Api\Data;

interface ItemConditionInterface
{
    const ENTITY_ID = 'entity_id';
    const TITLE = 'title';
    const POSITION = 'position';
    const STATUS = 'status';
    
    public function getEntityId();
    public function setEntityId($entityId);
    
    public function getTitle();
    public function setTitle($title);

    public function getStatus();
    public function setStatus($status);

    public function getPosition();
    public function setPosition($position);
}
