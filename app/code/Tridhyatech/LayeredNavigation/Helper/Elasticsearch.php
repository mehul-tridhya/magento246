<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigationt
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Search\Model\EngineResolver;

/**
 * @since 1.0.0
 * @deprecated since 1.3.0
 * @see \Tridhyatech\LayeredNavigation\Helper\SearchEngine
 */
class Elasticsearch extends AbstractHelper
{
    /**
     * @var \Magento\Search\Model\EngineResolver
     */
    private $engineResolver;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Search\Model\EngineResolver $engineResolver
     */
    public function __construct(
        Context $context,
        EngineResolver $engineResolver
    ) {
        parent::__construct($context);
        $this->engineResolver = $engineResolver;
    }

    /**
     * Check if elastic search is enabled.
     *
     * @return bool
     */
    public function isCurrentSearchEngine(): bool
    {
        $searchEngine = $this->engineResolver->getCurrentSearchEngine();

        return false !== \mb_strpos($searchEngine, 'elasticsearch');
    }
}
