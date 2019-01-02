<?php
namespace SM\Vendors\Model\ResourceModel;

class Deliveryarea extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	const DELIVERY_AREA_TABEL = 'magento_sm_vendor_deliveryarea';
	const ENTITY_ID = 'entity_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::DELIVERY_AREA_TABEL, self::ENTITY_ID);
    }
}