<?php

/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */

namespace Tridhyatech\ReturnManagement\Api\Data;

interface ReturnRuleInterface
{
    const ENTITY_ID = 'entity_id';
    const PRIORITY = 'priority';
    const STATUS = 'status';
    const RULE_NAME = 'rule_name';
    const WEBSITES = 'websites';
    const CUSTOMER_GROUP = 'customer_group';
    const DEFAULT_RESOLUTION_PERIOD = 'default_resolution_period';
    const EXCHANGE_PERIOD = 'exchange_period';
    const RETURN_PERIOD = 'return_period';
    const REPAIR_PERIOD = 'repair_period';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const CREATED_AT = 'created_at';
    const ACTIONS_SERIALIZED = 'actions_serialized';

    public function getEntityId();
    public function setEntityId($entityId);

    public function getPriority();
    public function setPriority($priority);

    public function getStatus();
    public function setStatus($status);

    public function getRuleName();
    public function setRuleName($ruleName);

    public function getWebsites();
    public function setWebsites($websites);

    public function getCustomerGroup();
    public function setCustomerGroup($customerGroup);

    public function getDefaultResolutionPeriod();
    public function setDefaultResolutionPeriod($defaultResolutionPeriod);

    public function getExchangePeriod();
    public function setExchangePeriod($exchangePeriod);

    public function getReturnPeriod();
    public function setReturnPeriod($returnPeriod);

    public function getRepairPeriod();
    public function setRepairPeriod($repairPeriod);

    public function getConditionsSerialized();
    public function setConditionsSerialized($conditionsSerialized);

    public function getCreatedAt();
    public function setCreatedAt($createdAt);

    public function getActionsSerialized();
    public function setActionsSerialized($actionsSerialized);
}
