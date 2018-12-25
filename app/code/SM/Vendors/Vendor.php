<?php
namespace SM\Vendors\Block\Adminhtml;

class Vendor extends \Magento\Backend\Block\Widget\Grid\Container
{
    const ADMIN_VENDOR = 'adminhtml_vendor';
    const SM_VENDORS = 'SM_Vendors';
    const VENDOR_MANAGER = 'Vendor Manager';
    const ADD_BUTTON = 'Add New Vendor';

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = self::ADMIN_VENDOR;
        $this->_blockGroup = self::SM_VENDORS;
        $this->_headerText = __(self::VENDOR_MANAGER);
        $this->_addButtonLabel = __(self::ADD_BUTTON);
        parent::_construct();
    }
}