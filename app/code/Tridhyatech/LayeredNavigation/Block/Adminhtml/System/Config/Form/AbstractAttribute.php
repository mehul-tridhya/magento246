<?php
/**
 * @author    Tridhya Tech
 * @copyright Copyright (c) 2023 Tridhya Tech Ltd (https://www.tridhyatech.com)
 * @package   Tridhyatech_LayeredNavigation
 */

namespace Tridhyatech\LayeredNavigation\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;
use Tridhyatech\LayeredNavigation\Model\FilterList;
use Magento\Framework\App\ResourceConnection;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;

class AbstractAttribute extends Field
{
    /**
     * @var string
     */
    protected $_template = 'Tridhyatech_LayeredNavigation::filter/attribute.phtml';

    /**
     * @var string
     */
    protected $_elementId;

    /**
     * @var array
     */
    protected $_values = [];

    /**
     * @var array
     */
    protected $_active = [];

    /**
     * Not active attributes
     *
     * @var array
     */
    protected $_notActive = [];

    /**
     * @var FilterList
     */
    protected $_filterableAttributes;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var string
     */
    protected $_configId;

    /**
     * @var boolean
     */
    protected $_isCustomOptions = false;

    /**
     * Current element
     */
    protected $_element;

    /**
     * @param FilterList         $filterableAttributes
     * @param ResourceConnection $resourceConnection
     * @param Context            $context
     * @param array              $data
     */
    public function __construct(
        FilterList $filterableAttributes,
        ResourceConnection $resourceConnection,
        Context $context,
        array $data = []
    ) {
        $this->_filterableAttributes = $filterableAttributes;
        $this->_resourceConnection = $resourceConnection;
        parent::__construct($context, $data);
    }

    /**
     * Render fieldset html
     *
     * @param  AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->_values = $this->_prepareValue();
        $this->_prepareAttributes();
        $this->_element = $element;
        $this->_elementId = $element->getId();

        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);

        // Disable element if value is inherited from other scope. Flag has to be set before the value is rendered.

        if ($element->getInherit() && $isCheckboxRequired) {
            $this->setIsDisabled(true);
        }

        $config = $element->getFieldConfig();
        $this->_configId = $config['id'];

        return $element->getElementHtml() . $this->toHtml();
    }

    /**
     * Retrieve inherit checkbox html
     *
     * @return string
     */
    public function getInheritCheckboxHtml()
    {
        if ($this->_element === null) {
            return false;
        }

        $isCheckboxRequired = $this->_isInheritCheckboxRequired($this->_element);
        if ($isCheckboxRequired) {
            return $this->_renderInheritCheckbox($this->_element);
        }

        return '<td class=""></td>';
    }

    /**
     * Retrieve element
     *
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Retrieve config id
     *
     * @return string
     */
    public function getConfigId()
    {
        return $this->_configId;
    }

    /**
     * Retrieve element id
     *
     * @return string
     */
    public function getElementId()
    {
        return $this->_elementId;
    }

    /**
     * Retrieve active attributes
     *
     * @return array
     */
    public function getActiveAttributes()
    {
        return $this->_active;
    }

    /**
     * Retrieve not active attributes
     *
     * @return array
     */
    public function getNotActiveAttributes()
    {
        return $this->_notActive;
    }

    /**
     * Prepare selected attributes
     *
     * @return array
     */
    protected function _prepareValue()
    {
        return [];
    }

    /**
     * Get all attributes
     *
     * @return self
     */
    protected function _prepareAttributes()
    {
        return $this;
    }

    /**
     * Is custom options
     *
     * @return boolean
     */
    public function isCustomOptions()
    {
        return $this->_isCustomOptions;
    }
}
