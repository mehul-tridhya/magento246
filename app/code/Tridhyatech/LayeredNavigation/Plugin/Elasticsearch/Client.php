<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Elasticsearch;

class Client
{

    /**
     * Fix condition in
     *
     * @param  $indices
     * @param  $types
     * @param  array $params
     * @return array
     */
    public function beforeQuery($indices, $types, array $params = [])
    {
        if (isset($params['body']['query']['filtered']['filter'])) {
            foreach ($params['body']['query']['filtered']['filter'] as $filterKey => $filterValue) {
                foreach ($filterValue as $andKey => $andValue) {
                    foreach ($andValue as $beforeTermKey => $beforeTermValue) {
                        foreach ($beforeTermValue as $termKey => $termValue) {
                            if (is_array($termValue)) {
                                foreach ($termValue as $afterTermKey => $afterTermValue) {
                                    if ($afterTermKey === 'in') {
                                        if (is_array($afterTermValue)) {
                                            $params['body']['query']['filtered']['filter'][$filterKey][$andKey][$beforeTermKey][$termKey] = implode(
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
        }

        return [$indices, $types, $params];
    }
}
