<?php
namespace SM\Vendors\Block\Adminhtml\Representative;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    const OBJECT_ID = 'user_id';
    const BLOCK_GROUP = 'SM_Vendors';
    const ADMIN_CONTROLLER = 'adminhtml_representative';
    const SAVE_REPRESENTATIVE = 'Save Representative';
    const DELETE_REPRESENTATIVE = 'Delete Representative';
    const SAVE_CONTINUE_EDIT = 'Save and Continue Edit';
    const LABEL = 'label';
    const SAVE_BUTTON = 'save';
    const DELETE_BUTTON = 'delete';
    const SAVE_CONTINUE = 'saveandcontinue';
    const VENDORS_REPRESENTATIVE = 'vendors_representative';
    const EDIT_REPRESENTATIVE = "Edit Representative '%1'";
    const ADD_REPRESENTATIVE = 'Add Representative';
    const BUTTON_CLASS = 'class';
    const DATA_ATTRIBUTE = 'data_attribute';
    const MAGE_INIT = 'mage-init';
    const BUTTON = 'button';
    const EVENT = 'event';
    const SAVE_AND_CONTINUE = 'saveAndContinueEdit';
    const TARGET = 'target';
    const EDIT_FORM = '#edit_form';

	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $contextData
     * @param \Magento\Framework\Registry $registryData
     * @param array $gridData
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $contextData,
        \Magento\Framework\Registry $registryData,
        array $gridData = []
    ) {
        $this->_coreRegistry = $registryData;
        parent::__construct($contextData, $gridData);
    }

	/**
     * Initialize representative
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = self::OBJECT_ID;
        $this->_blockGroup = self::BLOCK_GROUP;
        $this->_controller = self::ADMIN_CONTROLLER;
        parent::_construct();
        $this->buttonList->update(self::SAVE_BUTTON, self::LABEL, __(self::SAVE_REPRESENTATIVE));
        $this->buttonList->add(
            self::SAVE_CONTINUE,
            [
                self::LABEL => __(self::SAVE_CONTINUE_EDIT),
                self::BUTTON_CLASS => self::SAVE_BUTTON,
                self::DATA_ATTRIBUTE => [
                    self::MAGE_INIT => [
                        self::BUTTON => [self::EVENT => self::SAVE_AND_CONTINUE, self::TARGET => self::EDIT_FORM],
                    ],
                ]
            ],
            -100
        );
        $this->buttonList->update(self::DELETE_BUTTON, self::LABEL, __(self::DELETE_REPRESENTATIVE));
	}

    /**
     * Retrieve text for header element depending on loaded representative
     * 
     * @return string
     */
    public function getHeaderText()
    {
        $representativeRegistry = $this->_coreRegistry->registry(self::VENDORS_REPRESENTATIVE);
        if ($representativeRegistry->getId()) {
            $representativeTitle = $this->escapeHtml($representativeRegistry->getFirstname());
            return __(self::EDIT_REPRESENTATIVE, $representativeTitle);
        } else {
            return __(self::ADD_REPRESENTATIVE);
        }
    }
}