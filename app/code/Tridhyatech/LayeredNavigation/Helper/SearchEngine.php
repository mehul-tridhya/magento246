<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Search\Model\EngineResolver;

class SearchEngine extends AbstractHelper
{
    /**
     * @var EngineResolver
     */
    private $engineResolver;

    /**
     * @param Context        $context
     * @param EngineResolver $engineResolver
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
    public function isElasticSearch(): bool
    {
        $searchEngine = $this->engineResolver->getCurrentSearchEngine();
        return false !== \mb_strpos($searchEngine, 'elasticsearch');
    }

    /**
     * Check if live search is enabled.
     *
     * @return bool
     */
    public function isLiveSearch(): bool
    {
        return 'livesearch' === $this->engineResolver->getCurrentSearchEngine();
    }
}
