<?php
/**
* @author Tridhya Tech
* @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
* @package Tridhyatech_LayeredNavigation
 */

declare(strict_types=1);

namespace Tridhyatech\LayeredNavigation\Model\FilterMeta;

use Magento\Catalog\Model\Product\ProductList\Toolbar;
use Magento\Framework\Exception\NoSuchEntityException;
use Tridhyatech\LayeredNavigation\Api\Data\FilterMetaInterface;
use Tridhyatech\LayeredNavigation\Api\FilterMetaRepositoryInterface;
use Tridhyatech\LayeredNavigation\Helper\Config\Attribute;

/**
 * @since 1.0.0
 */
class Repository implements FilterMetaRepositoryInterface
{

    /**
     * Toolbar variables
     * @var array
     */
    private $toolbarVars = [
        Toolbar::ORDER_PARAM_NAME,
        Toolbar::DIRECTION_PARAM_NAME,
        Toolbar::MODE_PARAM_NAME,
        Toolbar::LIMIT_PARAM_NAME
    ];

    /**
     * @var array|null
     */
    protected $variables;

    /**
     * @var \Tridhyatech\LayeredNavigation\Model\FilterMeta\Factory
     */
    protected $filterMetaFactory;

    /**
     * @var \Tridhyatech\LayeredNavigation\Helper\Config\Attribute
     */
    protected $attributeConfig;

    /**
     * @param \Tridhyatech\LayeredNavigation\Model\FilterMeta\Factory $filterMetaFactory
     * @param \Tridhyatech\LayeredNavigation\Helper\Config\Attribute  $attributeConfig
     * @param array                                                      $toolbarVars
     */
    public function __construct(
        Factory $filterMetaFactory,
        Attribute $attributeConfig,
        array $toolbarVars = []
    ) {
        $this->toolbarVars = array_merge($this->toolbarVars, $toolbarVars);
        $this->filterMetaFactory = $filterMetaFactory;
        $this->attributeConfig = $attributeConfig;
    }

    /**
     * Get filter meta.
     *
     * @param string $requestVar
     * @return \Tridhyatech\LayeredNavigation\Api\Data\FilterMetaInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $requestVar): FilterMetaInterface
    {
        if (! isset($this->getList()[$requestVar])) {
            throw NoSuchEntityException::singleField('requestVar', $requestVar);
        }
        return $this->getList()[$requestVar];
    }

    /**
     * Get list of filters meta.
     *
     * @return array
     */
    public function getList(): array
    {
        if (null === $this->variables) {
            foreach ($this->toolbarVars as $toolbarVar) {
                $this->variables[$toolbarVar] = $this->filterMetaFactory->create(
                    $toolbarVar,
                    FilterMetaInterface::TYPE_TOOLBAR_VAR
                );
            }

            foreach ($this->attributeConfig->getSelectedAttributeCodes() as $attributeCode) {
                $this->variables[$attributeCode] = $this->filterMetaFactory->create(
                    $attributeCode,
                    FilterMetaInterface::TYPE_ATTRIBUTE
                );
            }

            $this->variables['cat'] = $this->filterMetaFactory->create(
                'cat',
                FilterMetaInterface::TYPE_CATEGORY
            );
        }

        return $this->variables;
    }
}
