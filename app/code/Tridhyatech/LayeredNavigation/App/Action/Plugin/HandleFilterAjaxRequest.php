<?php
/**
 * @package     Plumrocket_ProductFilter
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
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

/**
 * @since 1.0.0
 */
class HandleFilterAjaxRequest
{

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $resultFactory;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\AjaxResponse
     */
    private $ajaxResponse;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\AjaxRequestLocator
     */
    private $ajaxRequestLocator;

    /**
     * @param \Tridhyatech\LayeredNavigation\Helper\Config            $config
     * @param \Magento\Framework\Controller\ResultFactory                $resultFactory
     * @param \Tridhyatech\LayeredNavigation\Model\AjaxResponse       $ajaxResponse
     * @param \Magento\Framework\App\Http\Context                        $httpContext
     * @param \Tridhyatech\LayeredNavigation\Model\AjaxRequestLocator $ajaxRequestLocator
     */
    public function __construct(
        Config $config,
        ResultFactory $resultFactory,
        AjaxResponse $ajaxResponse,
        HttpContext $httpContext,
        AjaxRequestLocator $ajaxRequestLocator
    ) {
        $this->config = $config;
        $this->resultFactory = $resultFactory;
        $this->ajaxResponse = $ajaxResponse;
        $this->httpContext = $httpContext;
        $this->ajaxRequestLocator = $ajaxRequestLocator;
    }

    /**
     * Handle filter ajax request.
     *
     * @param \Magento\Framework\App\Action\AbstractAction $subject
     * @param \Magento\Framework\App\RequestInterface      $request
     */
    public function beforeDispatch(AbstractAction $subject, RequestInterface $request): void
    {
        if ($this->canProcessResponse($request)) {
            $this->httpContext->setValue('pr_filter_request', 1, 0);
        }
    }

    /**
     * Handle filter ajax request.
     *
     * @param \Magento\Framework\App\Action\AbstractAction                    $subject
     * @param ResponseInterface|\Magento\Framework\Controller\ResultInterface $result
     * @param \Magento\Framework\App\RequestInterface                         $request
     * @return ResponseInterface: \Magento\Framework\Controller\ResultInterface
     */
    public function afterDispatch(AbstractAction $subject, $result, RequestInterface $request)
    {
        if (! $this->canProcessResponse($request)) {
            return $result;
        }
        $this->httpContext->setValue('pr_filter_request', 1, 0);
        $filterData = $this->ajaxResponse->collectFilterData();
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($filterData);
    }

    /**
     * Check if this is product filter ajax request.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    private function canProcessResponse(RequestInterface $request): bool
    {
        $allowedActions = [AjaxResponse::CATEGORY_VIEW_ACTION_NAME, AjaxResponse::CATALOG_SEARCH_ACTION_NAME];
        return $this->ajaxRequestLocator->isActive()
            && in_array($request->getFullActionName(), $allowedActions, true)
            && $this->config->isModuleEnabled();
    }
}
