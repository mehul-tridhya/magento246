<?php

/**
 * @author Tridhya Tech Team
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package Tridhyatech_Core
 */

namespace Tridhyatech\Core\Controller\Adminhtml\Marketplace;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Backend\App\Action;

/**
 * Redirect to Tridhyatech store from admin submenu
 */
class Index extends Action
{
   /**
    * Authorization level of a basic admin session
    *
    * @see _isAllowed()
    */
    public const ADMIN_RESOURCE = 'Tridhyatech_Core::marketplace';
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var ResultRedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * External url of Tridhyatech store
     */
    protected const TRIDHYATECH_STORE_URL = 'https://magento-store.tridhya.com/';

    /**
     * Construct method
     *
     * @param Context $context
     * @param Redirect $resultRedirectFactory
     */
    public function __construct(
        Context $context,
        Redirect $resultRedirectFactory
    ) {

        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Execute method
     *
     * @return void
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $redirectLink = self::TRIDHYATECH_STORE_URL;
        $resultRedirect->setUrl($redirectLink);
        return $resultRedirect;
    }
}
