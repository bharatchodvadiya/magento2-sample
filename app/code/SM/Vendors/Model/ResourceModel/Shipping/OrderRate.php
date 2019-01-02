<?php
namespace SM\Vendors\Model\ResourceModel\Shipping;

class OrderRate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	const ORDERRATE_TABEL = 'magento_sm_vendor_shipping_order_rate';
	const CONFIG_ID = 'config_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::ORDERRATE_TABEL, self::CONFIG_ID);
    }
}