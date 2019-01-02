<?php
namespace SM\Vendors\Model\ResourceModel;

class Banner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	const VENDOR_BANNER_TABEL = 'magento_sm_vendor_banner';
	const BANNER_ID = 'id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::VENDOR_BANNER_TABEL, self::BANNER_ID);
    }
}