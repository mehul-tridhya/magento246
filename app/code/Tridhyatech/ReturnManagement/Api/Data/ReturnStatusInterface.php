<?php

/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */

namespace Tridhyatech\ReturnManagement\Api\Data;

interface ReturnStatusInterface
{
    const ENTITY_ID = 'entity_id';
    const TITLE = 'title';
    const POSITION = 'position';
    const STATE = 'state';
    const IS_INITIAL = 'is_initial';
    const COLOR = 'color';
    const STATUS = 'status';
    const RELATED_EVENT = 'related_event';
    const ADMIN_GRID = 'admin_grid';

    public function getEntityId();
    public function setEntityId($entityId);

    public function getTitle();
    public function setTitle($title);

    public function getPosition();
    public function setPosition($position);

    public function getState();
    public function setState($state);

    public function getIsInitial();
    public function setIsInitial($isInitial);

    public function getColor();
    public function setColor($color);

    public function getStatus();
    public function setStatus($status);

    public function getRelatedEvent();
    public function setRelatedEvent($relatedEvent);

    public function getAdminGrid();
    public function setAdminGrid($adminGrid);
}
