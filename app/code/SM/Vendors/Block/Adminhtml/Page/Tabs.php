<?php
namespace SM\Vendors\Block\Adminhtml\Page\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    const PAGE_TABS = 'page_tabs';
    const EDIT_FORM = 'edit_form';
    const PAGE_INFORMATION = 'Page Information';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId(self::PAGE_TABS);
        $this->setDestElementId(self::EDIT_FORM);
        $this->setTitle(__(self::PAGE_INFORMATION));
    }
}