<?php
namespace SM\Vendors\Block\Adminhtml;

class Page extends \Magento\Backend\Block\Widget\Grid\Container
{
    const ADMIN_PAGE = 'adminhtml_page';
    const SM_VENDORS = 'SM_Vendors';
    const PAGE_MANAGER = 'Page Manager';
    const ADD_BUTTON = 'Add New Page';

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = self::ADMIN_PAGE;
        $this->_blockGroup = self::SM_VENDORS;
        $this->_headerText = __(self::PAGE_MANAGER);
        $this->_addButtonLabel = __(self::ADD_BUTTON);
        parent::_construct();
    }
}