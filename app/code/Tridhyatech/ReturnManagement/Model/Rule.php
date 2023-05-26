<?php

namespace Tridhyatech\ReturnManagement\Model;

use Tridhyatech\ReturnManagement\Model\ResourceModel\Rule as RuleResourceModel;
use Magento\Quote\Model\Quote\Address;
use Magento\Rule\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\CatalogRule\Model\Rule\Condition\CombineFactory as condCombineFactory;
use Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory as condProdCombineF;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

class Rule extends AbstractModel
{
    protected $_eventPrefix = 'dolphin_addconditionfiled';
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
        $this->_init(RuleResourceModel::class);
        $this->setIdFieldName('rule_id');
    }

    public function getConditionsInstance()
    {
        return $this->condProdCombineF->create();
    }

    public function getActionsInstance()
    {
        return $this->condProdCombineF->create();
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
