<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Elasticsearch\Amasty\Search;

use Tridhyatech\LayeredNavigation\Helper\Config;

class GetRequestQuery
{

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Fix get request query
     *
     * @param  $query
     * @return array
     */
    public function afterExecute($query)
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
