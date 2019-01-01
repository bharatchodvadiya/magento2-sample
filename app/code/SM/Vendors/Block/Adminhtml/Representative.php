<?php
namespace SM\Vendors\Block\Adminhtml;

class Representative extends \Magento\Backend\Block\Widget\Grid\Container
{
    const ADMIN_CONTROLLER = 'adminhtml_representative';
    const SM_VENDORS = 'SM_Vendors';
    const REPRESENTATIVE_MANAGER = 'Representative Manager';
    const ADD_BUTTON = 'Add New Representative';

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = self::ADMIN_CONTROLLER;
        $this->_blockGroup = self::SM_VENDORS;
        $this->_headerText = __(self::REPRESENTATIVE_MANAGER);
        $this->_addButtonLabel = __(self::ADD_BUTTON);
        parent::_construct();
    }
}