<?php
namespace SM\Vendors\Model\ResourceModel\Shipping;

class FlatRate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	const FLATRATE_TABEL = 'magento_sm_vendor_shipping_flatrate';
	const CONFIG_ID = 'config_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::FLATRATE_TABEL, self::CONFIG_ID);
    }
}