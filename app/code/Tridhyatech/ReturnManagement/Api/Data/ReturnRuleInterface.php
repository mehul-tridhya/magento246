<?php

/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */

namespace Tridhyatech\ReturnManagement\Api\Data;

interface ReturnRuleInterface
{
    const RULE_ID = 'rule_id';
    const RULE_NAME = 'rule_name';
    const RULE_STATUS = 'rule_status';
    const PRIORITY = 'priority';
    const RETURN_PERIOD = 'return_period';
    const REPAIR_PERIOD = 'repair_period';
    const EXCHANGE_PERIOD = 'exchange_period';
    const DEFAULT_RESOLUTION_PERIOD = 'default_resolution_period';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const ACTIONS_SERIALIZED = 'actions_serialized';
    const CREATED_DATE = 'created_date';

    public function getRuleId();
    public function setRuleId($ruleId);

    public function getRuleName();
    public function setRuleName($ruleName);

    public function getRuleStatus();
    public function setRuleStatus($ruleStatus);

    public function getPriority();
    public function setPriority($priority);

    public function getReturnPeriod();
    public function setReturnPeriod($returnPeriod);

    public function getRepairPeriod();
    public function setRepairPeriod($repairPeriod);

    public function getExchangePeriod();
    public function setExchangePeriod($exchangePeriod);

    public function getDefaultResolutionPeriod();
    public function setDefaultResolutionPeriod($defaultResolutionPeriod);

    public function getConditionsSerialized();
    public function setConditionsSerialized($conditionsSerialized);

    public function getActionsSerialized();
    public function setActionsSerialized($actionsSerialized);

    public function getCreatedDate();
    public function setCreatedDate($createdDate);
}
