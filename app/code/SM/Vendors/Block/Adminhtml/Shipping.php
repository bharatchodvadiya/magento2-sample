<?php
namespace SM\Vendors\Block\Adminhtml;

class Shipping extends \Magento\Backend\Block\Widget\Grid\Container
{
    const ADMIN_SHIPPING = 'adminhtml_shipping';
    const SM_VENDORS = 'SM_Vendors';
    const SHIPPING_MANAGER = 'Shipping Manager';
    const ADD_BUTTON = 'add';

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = self::ADMIN_SHIPPING;
        $this->_blockGroup = self::SM_VENDORS;
        $this->_headerText = __(self::SHIPPING_MANAGER);
        parent::_construct();
        $this->removeButton(self::ADD_BUTTON);
    }
}