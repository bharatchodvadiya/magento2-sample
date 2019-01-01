<?php
namespace SM\Vendors\Block\Adminhtml;

class Shipment extends \Magento\Backend\Block\Widget\Grid\Container
{
    const ADMIN_SALES_SHIPMENT = 'adminhtml_shipment';
    const SM_VENDORS = 'SM_Vendors';
    const SHIPMENTS = 'Shipments';
    const ADD_BUTTON = 'add';

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = self::ADMIN_SALES_SHIPMENT;
        $this->_blockGroup = self::SM_VENDORS;
        $this->_headerText = __(self::SHIPMENTS);
        parent::_construct();
        $this->removeButton(self::ADD_BUTTON);
    }
}