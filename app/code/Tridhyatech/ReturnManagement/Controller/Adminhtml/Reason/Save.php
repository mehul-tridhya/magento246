<?php
/**
 * @author Tridhyatech Team
 * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
 * @package Tridhyatech_ReturnManagement
 */
namespace Tridhyatech\ReturnManagement\Controller\Adminhtml\Reason;

use Magento\Framework\Controller\ResultFactory;
 
class Save extends \Magento\Backend\App\Action
{
    protected $reasonFactory;
    protected $date;
     
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Tridhyatech\ReturnManagement\Model\ReasonFactory $reasonFactory
    ) {
        $this->reasonFactory = $reasonFactory;
        $this->date = $date;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            $this->_redirect('ttrma/reason/form');
            return;
        }
        $currentDate = $this->date->gmtDate('Y-m-d H:i:s');
        $data['created_date'] = $currentDate;
        
        $rowData = $this->reasonFactory->create();
        $rowData->setData($data);

        if (isset($data['entity_id'])) {
            $rowData->setEntityId($data['entity_id']);
        }
                    
        if ($rowData->save()) {
            $this->messageManager->addSuccess(__('Reason has been successfully saved.'));
        } else {
            $this->messageManager->addErrorMessage(__('Data was not saved.'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
