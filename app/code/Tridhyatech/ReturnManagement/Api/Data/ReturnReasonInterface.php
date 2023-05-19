<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */
namespace Tridhyatech\ReturnManagement\Api\Data;
 
interface ReturnReasonInterface
{
    const ENTITY_ID = 'entity_id';
    const TITLE = 'title';
    const POSITION = 'position';
    const CREATED_DATE = 'created_at';
    const SHIPPING_PAYER = 'shipping_payer';
    const STATUS = 'status';
    
    public function getEntityId();
    public function setEntityId($entityId);
    
    public function getTitle();
    public function setTitle($title);

    public function getCreatedDate();
    public function setCreatedDate($createdDate);

    public function getStatus();
    public function setStatus($status);

    public function getShippingPayer();
    public function setShippingPayer($shippingPayer);

    public function getPosition();
    public function setPosition($position);
}
