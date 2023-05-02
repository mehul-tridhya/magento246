<?php

/**
 * @author Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package Tridhyatech_Core
 */

namespace Tridhyatech\Core\Controller\Adminhtml\Marketplace;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;

/**
 * Redirect to Tridhyatech store from admin submenu.
 */
class Index extends Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Tridhyatech_Core::marketplace';

    /**
     * External url of Tridhyatech store.
     */
    protected const TRIDHYATECH_STORE_URL = 'https://magento-store.tridhya.com/';

    /**
     * Execute method.
     *
     * @return Redirect
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $redirectLink = self::TRIDHYATECH_STORE_URL;

        return $resultRedirect->setUrl($redirectLink);
    }
}
