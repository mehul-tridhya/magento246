<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Model\ResourceModel\Product;

use Magento\Framework\Data\Collection\EntityFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Model\EntityFactory as EavEntityFactory;
use Magento\Catalog\Model\ResourceModel\Helper;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Module\Manager;
use Magento\Catalog\Model\Indexer\Product\Flat\State;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\Product\OptionFactory;
use Magento\Catalog\Model\ResourceModel\Url;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\DateTime;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Framework\Search\Request\Builder;
use Magento\Search\Model\SearchEngine;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Api\Search\SearchResultFactory;

class Collection extends \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    protected $config;

    /**
     * @param EntityFactory            $entityFactory
     * @param LoggerInterface          $logger
     * @param FetchStrategyInterface   $fetchStrategy
     * @param ManagerInterface         $eventManager
     * @param EavConfig                $eavConfig
     * @param ResourceConnection       $resource
     * @param EavEntityFactory         $eavEntityFactory
     * @param Helper                   $resourceHelper
     * @param UniversalFactory         $universalFactory
     * @param StoreManagerInterface    $storeManager
     * @param Manager                  $moduleManager
     * @param State                    $catalogProductFlatState
     * @param ScopeConfigInterface     $scopeConfig
     * @param OptionFactory            $productOptionFactory
     * @param Url                      $catalogUrl
     * @param TimezoneInterface        $localeDate
     * @param Session                  $customerSession
     * @param DateTime                 $dateTime
     * @param GroupManagementInterface $groupManagement
     * @param QueryFactory             $catalogSearchData
     * @param Builder                  $requestBuilder
     * @param SearchEngine             $searchEngine
     * @param TemporaryStorageFactory  $temporaryStorageFactory
     * @param Config                   $config
     * @param AdapterInterface|null    $connection
     * @param $searchRequestName
     * @param SearchResultFactory|null $searchResultFactory
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        EavConfig $eavConfig,
        ResourceConnection $resource,
        EavEntityFactory $eavEntityFactory,
        Helper $resourceHelper,
        UniversalFactory $universalFactory,
        StoreManagerInterface $storeManager,
        Manager $moduleManager,
        State $catalogProductFlatState,
        ScopeConfigInterface $scopeConfig,
        OptionFactory $productOptionFactory,
        Url $catalogUrl,
        TimezoneInterface $localeDate,
        Session $customerSession,
        DateTime $dateTime,
        GroupManagementInterface $groupManagement,
        QueryFactory $catalogSearchData,
        Builder $requestBuilder,
        SearchEngine $searchEngine,
        TemporaryStorageFactory $temporaryStorageFactory,
        Config $config,
        AdapterInterface $connection = null,
        string $searchRequestName = 'catalog_view_container',
        SearchResultFactory $searchResultFactory = null
    ) {
        $this->config = $config;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $moduleManager,
            $catalogProductFlatState,
            $scopeConfig,
            $productOptionFactory,
            $catalogUrl,
            $localeDate,
            $customerSession,
            $dateTime,
            $groupManagement,
            $catalogSearchData,
            $requestBuilder,
            $searchEngine,
            $temporaryStorageFactory,
            $connection,
            $searchRequestName,
            $searchResultFactory
        );
    }

    /**
     * @inheritDoc
     */
    public function getSize()
    {
        if (!$this->config->isModuleEnabled()) {
            return parent::getSize();
        }

        $sql = $this->getSelectCountSql();
        $this->_totalRecords = $this->getConnection()->fetchOne($sql, $this->_bindParams);
        return (int) $this->_totalRecords;
    }
}
