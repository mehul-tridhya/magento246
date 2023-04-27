<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Elasticsearch\Amasty\Search;

use Tridhyatech\LayeredNavigation\Helper\Config;

class GetRequestQuery
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    protected $config;

    /**
     * @param \Tridhyatech\LayeredNavigation\Helper\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Fix get request query
     *
     * @param \Amasty\ElasticSearch\Model\Search\GetRequestQuery $subject
     * @param                                                    $query
     * @return array
     */
    public function afterExecute(\Amasty\ElasticSearch\Model\Search\GetRequestQuery $subject, $query)
    {
        if (isset($query['body']['query']['bool']['must']) && $this->config->isModuleEnabled()) {
            foreach ($query['body']['query']['bool']['must'] as $filterKey => $filterValue) {
                foreach ($filterValue as $beforeTermKey => $beforeTermValue) {
                    foreach ($beforeTermValue as $termKey => $termValue) {
                        if (is_array($termValue)) {
                            foreach ($termValue as $afterTermKey => $afterTermValue) {
                                if ($afterTermKey === 'in') {
                                    if (is_array($afterTermValue)) {
                                        unset($query['body']['query']['bool']['must'][$filterKey][$beforeTermKey]);
                                        $query['body']['query']['bool']['must'][$filterKey]['term'][$termKey] = implode(
                                            ',',
                                            $afterTermValue
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $query;
    }
}
