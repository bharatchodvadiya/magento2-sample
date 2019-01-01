<?php
namespace SM\Vendors\Block\Adminhtml\Representative\Edit;

/**
 * Admin representative left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    const REPRESENTATIVE_TABS = 'representative_tabs';
    const EDIT_FORM = 'edit_form';
    const REPRESENTATIVE_INFORMATION = 'Representative Information';

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId(self::REPRESENTATIVE_TABS);
        $this->setDestElementId(self::EDIT_FORM);
        $this->setTitle(__(self::REPRESENTATIVE_INFORMATION));
    }
}