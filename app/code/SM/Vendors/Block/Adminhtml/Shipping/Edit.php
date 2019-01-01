<?php
namespace SM\Vendors\Block\Adminhtml\Shipping;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    const OBJECT_ID = 'id';
    const BLOCK_GROUP = 'SM_Vendors';
    const ADMIN_SHIPPING = 'adminhtml_shipping';
    const SAVE_SHIPPING = 'Save Shipping Config';
    const DELETE_SHIPPING = 'Delete Shipping Config';
    const SAVE_CONTINUE_EDIT = 'Save and Continue Edit';
    const CONFIG_SHIPPING = 'Config Shipping';
    const LABEL = 'label';
    const SAVE_BUTTON = 'save';
    const DELETE_BUTTON = 'delete';
    const SAVE_CONTINUE = 'saveandcontinue';
    const BUTTON_CLASS = 'class';
    const DATA_ATTRIBUTE = 'data_attribute';
    const MAGE_INIT = 'mage-init';
    const BUTTON = 'button';
    const EVENT = 'event';
    const SAVE_AND_CONTINUE = 'saveAndContinueEdit';
    const TARGET = 'target';
    const EDIT_FORM = '#edit_form';
    const VENDORS_SAVE = 'vendors/*/save';
    const CURRENT = '_current';
    const BACK = 'back';
    const EDIT = 'edit';
    const ACTIVE_TAB = 'active_tab';
    const TAB_ID = '{{tab_id}}';

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
     * Initialize vendors
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = self::OBJECT_ID;
        $this->_blockGroup = self::BLOCK_GROUP;
        $this->_controller = self::ADMIN_SHIPPING;
        parent::_construct();
        $this->buttonList->update(self::SAVE_BUTTON, self::LABEL, __(self::SAVE_SHIPPING));
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
        $this->buttonList->update(self::DELETE_BUTTON, self::LABEL, __(self::DELETE_SHIPPING));
	}

    /**
     * Retrieve text for header element depending on loaded vendor
     * 
     * @return string
     */
    public function getHeaderText()
    {
        return __(self::CONFIG_SHIPPING);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(self::VENDORS_SAVE, [self::CURRENT => true, self::BACK => self::EDIT, self::ACTIVE_TAB => self::TAB_ID]);
    }
}
