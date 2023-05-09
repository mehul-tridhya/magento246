<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\Variable\Params;

use Magento\Framework\HTTP\PhpEnvironment\Request;
use Laminas\Stdlib\Parameters as Laminas;
use Zend\Stdlib\Parameters as Zend;

class Processor
{

    /**
     * Move variables from path to params.
     *
     * @param Request $request
     * @param array   $variables
     */
    public function moveToParams(Request $request, array $variables): void
    {
        $request->setParam('prfilter_ajax', null);
        $request->setParam('prfilter_variables', null);
        $queryParams = $request->getQuery()->toArray();
        unset($queryParams['prfilter_ajax'], $queryParams['prfilter_variables']);

        // Laminas package can be missing in magento 2.3
        if (class_exists('\Laminas\Stdlib\Parameters')) {
            $request->setQuery(new Laminas($queryParams));
        } else if(!class_exists('\Laminas\Stdlib\Parameters')){
            $request->setQuery(new Zend($queryParams));
        }

        $requestUri = parse_url($request->getRequestUri(), PHP_URL_PATH);
        if ($request->getParams()) {
            $requestUri .= '?' . http_build_query($request->getParams());
        }
        $request->setRequestUri($requestUri);

        foreach ($variables as $variable => $values) {
            $request->setParam($variable, implode(',', $values));
        }
    }

    /**
     * Parse only get params.
     */
    public function parseGetParams(Request $request): array
    {
        $query = parse_url($request->getRequestUri(), PHP_URL_QUERY);
        if (!$query) {
            return [];
        }
        parse_str($query, $getParams);
        return $getParams;
    }
}
