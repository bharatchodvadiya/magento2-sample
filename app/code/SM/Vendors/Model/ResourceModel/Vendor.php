<?php
namespace SM\Vendors\Model\ResourceModel;

class Vendor extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	const VENDOR_TABEL = 'magento_sm_vendor';
	const VENDOR_ID = 'vendor_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::VENDOR_TABEL, self::VENDOR_ID);
    }
}