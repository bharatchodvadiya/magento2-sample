<?php
namespace SM\Vendors\Block\Adminhtml\Shipping\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    const SHIPPING_TABS = 'shipping_tabs';
    const EDIT_FORM = 'edit_form';
    const VENDOR_INFORMATION = 'Vendor Information';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId(self::SHIPPING_TABS);
        $this->setDestElementId(self::EDIT_FORM);
        $this->setTitle(__(self::VENDOR_INFORMATION));
    }
}