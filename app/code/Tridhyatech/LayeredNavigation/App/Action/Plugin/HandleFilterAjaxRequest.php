<?php

/**
 * @author Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\App\Action\Plugin;

use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Tridhyatech\LayeredNavigation\Helper\Config;
use Tridhyatech\LayeredNavigation\Model\AjaxRequestLocator;
use Tridhyatech\LayeredNavigation\Model\AjaxResponse;

class HandleFilterAjaxRequest
{

    /**
     * @var AjaxResponse
     */
    private $ajaxResponse;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @var AjaxRequestLocator
     */
    private $ajaxRequestLocator;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config             $config
     * @param ResultFactory      $resultFactory
     * @param AjaxResponse       $ajaxResponse
     * @param HttpContext        $httpContext
     * @param AjaxRequestLocator $ajaxRequestLocator
     */
    public function __construct(
        Config $config,
        ResultFactory $resultFactory,
        AjaxResponse $ajaxResponse,
        HttpContext $httpContext,
        AjaxRequestLocator $ajaxRequestLocator
    ) {
        $this->ajaxRequestLocator = $ajaxRequestLocator;
        $this->resultFactory = $resultFactory;
        $this->httpContext = $httpContext;
        $this->ajaxResponse = $ajaxResponse;
        $this->config = $config;
    }

    /**
     * Handle filter ajax request.
     *
     * @param AbstractAction                    $subject
     * @param ResponseInterface|\Magento\Framework\Controller\ResultInterface $result
     * @param RequestInterface                         $request
     * @return ResponseInterface: \Magento\Framework\Controller\ResultInterface
     */
    public function afterDispatch(AbstractAction $subject, $result, RequestInterface $request)
    {
        if (!$this->isAllowedAction($request)) {
            return $result;
        }
        $this->httpContext->setValue('pr_filter_request', 1, 0);
        $filterData = $this->ajaxResponse->collectFilterData();
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($filterData);
    }

    /**
     * Handle filter ajax request.
     *
     * @param AbstractAction $subject
     * @param RequestInterface      $request
     */
    public function beforeDispatch(AbstractAction $subject, RequestInterface $request): void
    {
        if ($this->isAllowedAction($request)) {
            $this->httpContext->setValue('pr_filter_request', 1, 0);
        }
    }

    /**
     * Check if this is product filter ajax request.
     *
     * @param RequestInterface $request
     * @return bool
     */
    private function isAllowedAction(RequestInterface $request): bool
    {
        $allowedActions = [AjaxResponse::CATEGORY_VIEW_ACTION_NAME, AjaxResponse::CATALOG_SEARCH_ACTION_NAME];
        return $this->ajaxRequestLocator->isActive()
            && in_array($request->getFullActionName(), $allowedActions, true);
    }
}
