<?php

/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Magento\Framework\View\Layout;

class AddProductFilterInitHandle implements ObserverInterface
{

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Changing attribute values
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /**
         * @var Layout $layout
         */
        $layout = $observer->getLayout();
        if (array_intersect($layout->getUpdate()->getHandles(), $this->config->getAllowedHandles())) {
            $layout->getUpdate()->addHandle('product_filter_init');
        }
    }
}
