<?php
namespace SM\Vendors\Block\Adminhtml\Vendor\Edit;
/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    const VENDORS_TABS = 'vendors_tabs';
    const EDIT_FORM = 'edit_form';
    const VENDOR_INFORMATION = 'Vendor Information';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId(self::VENDORS_TABS);
        $this->setDestElementId(self::EDIT_FORM);
        $this->setTitle(__(self::VENDOR_INFORMATION));
    }
}
