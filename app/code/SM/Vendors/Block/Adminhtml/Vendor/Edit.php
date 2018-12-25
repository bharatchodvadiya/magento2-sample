<?php
namespace SM\Vendors\Block\Adminhtml\Vendor;
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    const VENDOR_ID = 'vendor_id';
    const BLOCK_GROUP = 'SM_Vendors';
    const ADMIN_VENDOR = 'adminhtml_vendor';
    const SAVE_ITEM = 'Save Item';
    const DELETE_ITEM = 'Delete Item';
    const SAVE_CONTINUE_EDIT = 'Save and Continue Edit';
    const LABEL = 'label';
    const SAVE_BUTTON = 'save';
    const DELETE_BUTTON = 'delete';
    const SAVE_CONTINUE = 'saveandcontinue';
    const VENDORS_VENDOR = 'vendors_vendor';
    const EDIT_VENDOR = "Edit Vendor '%1'";
    const ADD_VENDOR = 'Add Vendor';
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
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

	/**
     * Initialize vendors
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = self::VENDOR_ID;
        $this->_blockGroup = self::BLOCK_GROUP;
        $this->_controller = self::ADMIN_VENDOR;
        parent::_construct();
        $this->buttonList->update(self::SAVE_BUTTON, self::LABEL, __(self::SAVE_ITEM));
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
        $this->buttonList->update(self::DELETE_BUTTON, self::LABEL, __(self::DELETE_ITEM));
	}

    /**
     * Retrieve text for header element depending on loaded vendor
     * 
     * @return string
     */
    public function getHeaderText()
    {
        $vendorRegistry = $this->_coreRegistry->registry(self::VENDORS_VENDOR);
        if ($vendorRegistry->getId()) {
            $vendorTitle = $this->escapeHtml($vendorRegistry->getTitle());
            return __(self::EDIT_VENDOR, $vendorTitle);
        } else {
            return __(self::ADD_VENDOR);
        }
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

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('page_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'content');
                }
            };
        ";
        return parent::_prepareLayout();
    }
}
