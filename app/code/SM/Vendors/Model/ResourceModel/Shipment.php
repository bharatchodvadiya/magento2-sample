<?php
namespace SM\Vendors\Model\ResourceModel;

class Shipment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	const SHIPMENT_TABEL = 'magento_sales_shipment_grid';
	const ENTITY_ID = 'entity_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::SHIPMENT_TABEL, self::ENTITY_ID);
    }
}