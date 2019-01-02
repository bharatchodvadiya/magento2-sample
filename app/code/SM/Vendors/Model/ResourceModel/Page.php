<?php
namespace SM\Vendors\Model\ResourceModel;

class Page extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	const VENDOR_PAGE_TABEL = 'magento_sm_vendor_page';
	const PAGE_ID = 'id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::VENDOR_PAGE_TABEL, self::PAGE_ID);
    }
}