<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */
namespace Tridhyatech\ReturnManagement\Model;

use Tridhyatech\ReturnManagement\Api\Data\ReturnRuleInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory as condCombineFactory;
use Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory as condProdCombineF;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Rule\Model\AbstractModel;
use Magento\Quote\Model\Quote\Address;

class ReturnRule extends AbstractModel implements ReturnRuleInterface
{
    
    const CACHE_TAG = 'tridhyatech_ttrma_return_rule';

    protected $_cacheTag = 'tridhyatech_ttrma_return_rule';
    protected $_eventPrefix = 'tridhyatech_ttrma_return_rule';
    protected $_eventObject = 'rule';
    protected $condCombineFactory;
    protected $condProdCombineF;
    protected $validatedAddresses = [];
    protected $_selectProductIds;
    protected $_displayProductIds;

    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        TimezoneInterface $localeDate,
        condCombineFactory $condCombineFactory,
        condProdCombineF $condProdCombineF,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->condCombineFactory = $condCombineFactory;
        $this->condProdCombineF = $condProdCombineF;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }
    
    protected function _construct()
    {
        parent::_construct();
        $this->_init(\Tridhyatech\ReturnManagement\Model\ResourceModel\Resolution::class);
        $this->setIdFieldName('entity_id');
    }

    public function getConditionsInstance()
    {
        return $this->condCombineFactory->create();
    }
    public function getActionsInstance()
    {
        return $this->condCombineFactory->create();
    }
    
    public function getEntityId(){
        return $this->getData(self::ENTITY_ID);
    }
    public function setEntityId($entityId){
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    public function getPriority(){
        return $this->getData(self::PRIORITY);
    }
    public function setPriority($priority){
        return $this->setData(self::PRIORITY, $priority);
    }

    public function getStatus(){
        return $this->getData(self::STATUS);
    }
    public function setStatus($status){
        return $this->setData(self::STATUS, $status);
    }

    public function getRuleName(){
        return $this->getData(self::RULE_NAME);
    }
    public function setRuleName($ruleName){
        return $this->setData(self::RULE_NAME, $ruleName);
    }

    public function getWebsites(){
        return $this->getData(self::WEBSITES);
    }
    public function setWebsites($websites){
        return $this->setData(self::WEBSITES, $websites);
    }

    public function getCustomerGroup(){
        return $this->getData(self::CUSTOMER_GROUP);
    }
    public function setCustomerGroup($customerGroup){
        return $this->setData(self::CUSTOMER_GROUP, $customerGroup);
    }

    public function getDefaultResolutionPeriod(){
        return $this->getData(self::DEFAULT_RESOLUTION_PERIOD);
    }
    public function setDefaultResolutionPeriod($defaultResolutionPeriod){
        return $this->setData(self::DEFAULT_RESOLUTION_PERIOD, $defaultResolutionPeriod);
    }

    public function getExchangePeriod(){
        return $this->getData(self::EXCHANGE_PERIOD);
    }
    public function setExchangePeriod($exchangePeriod){
        return $this->setData(self::EXCHANGE_PERIOD, $exchangePeriod);
    }

    public function getReturnPeriod(){
        return $this->getData(self::RETURN_PERIOD);
    }
    public function setReturnPeriod($returnPeriod){
        return $this->setData(self::RETURN_PERIOD, $returnPeriod);
    }

    public function getRepairPeriod(){
        return $this->getData(self::REPAIR_PERIOD);
    }
    public function setRepairPeriod($repairPeriod){
        return $this->setData(self::REPAIR_PERIOD, $repairPeriod);
    }

    public function getConditionsSerialized(){
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }
    public function setConditionsSerialized($conditionsSerialized){
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    public function getCreatedAt(){
        return $this->getData(self::CREATED_AT);
    }
    public function setCreatedAt($createdAt){
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getActionsSerialized(){
        return $this->getData(self::ACTIONS_SERIALIZED);
    }
    public function setActionsSerialized($actionsSerialized){
        return $this->setData(self::ACTIONS_SERIALIZED, $actionsSerialized);
    }

    public function hasIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->validatedAddresses[$addressId]) ? true : false;
    }
    public function setIsValidForAddress($address, $validationResult)
    {
        $addressId = $this->_getAddressId($address);
        $this->validatedAddresses[$addressId] = $validationResult;
        return $this;
    }
    public function getIsValidForAddress($address)
    {
        $addressId = $this->_getAddressId($address);
        return isset($this->validatedAddresses[$addressId]) ? $this->validatedAddresses[$addressId] : false;
    }
    private function _getAddressId($address)
    {
        if ($address instanceof Address) {
            return $address->getId();
        }
        return $address;
    }
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }
    public function getActionFieldSetId($formName = '')
    {
        return $formName . 'rule_actions_fieldset_' . $this->getId();
    }
    public function getMatchProductIds()
    {
        $productCollection = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Catalog\Model\ResourceModel\Product\Collection'
        );
        $productFactory = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Catalog\Model\ProductFactory'
        );
        $this->_selectProductIds = [];
        $this->setCollectedAttributes([]);
        $this->getConditions()->collectValidatedAttributes($productCollection);
        \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Framework\Model\ResourceModel\Iterator'
        )->walk(
            $productCollection->getSelect(),
            [[$this, 'callbackValidateProductCondition']],
            [
                'attributes' => $this->getCollectedAttributes(),
                'product' => $productFactory->create(),
            ]
        );
        return $this->_selectProductIds;
    }
    public function callbackValidateProductCondition($args)
    {
        $product = clone $args['product'];
        $product->setData($args['row']);
        $websites = $this->_getWebsitesMap();
        foreach ($websites as $websiteId => $defaultStoreId) {
            $product->setStoreId($defaultStoreId);
            if ($this->getConditions()->validate($product)) {
                $this->_selectProductIds[] = $product->getId();
            }
        }
    }
    protected function _getWebsitesMap()
    {
        $map = [];
        $websites = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magento\Store\Model\StoreManagerInterface'
        )->getWebsites();
        foreach ($websites as $website) {
            if ($website->getDefaultStore() === null) {
                continue;
            }
            $map[$website->getId()] = $website->getDefaultStore()->getId();
        }
        return $map;
    }
}
