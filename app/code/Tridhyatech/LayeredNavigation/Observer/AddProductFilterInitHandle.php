<?php
/**
 * @package     Tridhyatech_LayeredNavigation
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Tridhyatech\LayeredNavigation\Helper\Config;

/**
 * @since 1.0.0
 */
class AddProductFilterInitHandle implements ObserverInterface
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @param \Tridhyatech\LayeredNavigation\Helper\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Changing attribute values
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->config->isModuleEnabled()) {
            /** @var \Magento\Framework\View\Layout $layout */
            $layout = $observer->getLayout();
            if (array_intersect($layout->getUpdate()->getHandles(), $this->config->getAllowedHandles())) {
                $layout->getUpdate()->addHandle('product_filter_init');
            }
        }
    }
}
