<?php
 /**
  * @author Tridhyatech Team
  * @copyright Copyright (c) 2020 Tridhyatech (https://www.tridhya.com)
  * @package Tridhyatech_ReturnManagement
  */
namespace Tridhyatech\ReturnManagement\Ui\Component\Listing\Grid\Resolution\Column;
 
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class StatusAction extends Column
{
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl('ttrma/resolution/form', ['id' => $item['entity_id']]),
                    'label' => __('Edit'),
                    'hidden' => false
                ];
            }
        }
        return $dataSource;
    }
}
