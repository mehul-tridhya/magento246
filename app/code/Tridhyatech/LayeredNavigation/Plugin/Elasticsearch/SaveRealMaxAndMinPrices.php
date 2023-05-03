<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Plugin\Elasticsearch;

use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface;
use Tridhyatech\LayeredNavigation\Model\CatalogSearch\Model\ResourceModel\Fulltext\Collection\CurrentLoading;

/**
 * Temporary fix for
 * @link https://github.com/magento/magento2/issues/28919
 * TODO: remove after left support magento version with bug
 *
 * @since 1.0.0
 */
class SaveRealMaxAndMinPrices
{
    /**
     * @var CurrentLoading
     */
    private $currentLoadingCollection;

    /**
     * @param CurrentLoading $currentLoadingCollection
     */
    public function __construct(CurrentLoading $currentLoadingCollection)
    {
        $this->currentLoadingCollection = $currentLoadingCollection;
    }

    /**
     * @param BucketInterface                                                  $bucket
     * @param Dimension[]                                                      $dimensions
     * @param array                                                            $queryResult
     * @param DataProviderInterface                                            $dataProvider
     */
    public function beforeBuild(
        BucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ) {
        if (isset($queryResult['aggregations']['price_bucket']) && 'price_bucket' === $bucket->getName()) {
            $priceData = $queryResult['aggregations']['price_bucket'];
            $this->currentLoadingCollection->setPriceData($priceData);
        }
    }
}
