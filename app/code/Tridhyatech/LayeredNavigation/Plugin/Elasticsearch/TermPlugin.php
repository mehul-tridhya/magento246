<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Plugin\Elasticsearch;

use Magento\Elasticsearch\SearchAdapter\Filter\Builder\Term;

class TermPlugin
{

    /**
     * Fix request structure.
     *
     * @param \Magento\Elasticsearch\SearchAdapter\Filter\Builder\Term $subject
     * @param                                                          $result
     * @return mixed
     */
    public function afterBuildFilter(Term $subject, $result)
    {
        foreach ($result as $i => $ritems) {
            foreach ($ritems as $operator => $items) {
                foreach ($items as $key => $value) {
                    if (is_array($value)) {
                        if (count($value) == 1 && array_key_exists('in', $value)) {
                            $result[$i][$operator][$key] = $value['in'];
                        }
                    }
                }
            }
        }

        return $result;
    }
}
