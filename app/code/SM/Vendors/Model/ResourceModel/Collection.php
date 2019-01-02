<?php
namespace SM\Vendors\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    const MODEL_ORDER = 'SM\Vendors\Model\Order';
    const RESOURCE_ORDER = 'SM\Vendors\Model\ResourceModel\Order';
    const MAIN_ORDER_ID = 'main_table.order_id = ?';
    const MAIN_VENDOR_ID = 'main_table.vendor_id = ?';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(self::MODEL_ORDER, self::RESOURCE_ORDER);
    }

    public function getByOriginOrderId($orderId, $vendorId) {
    	$this->getSelect()->where(self::MAIN_ORDER_ID, $orderId)->where(self::MAIN_VENDOR_ID, $vendorId)->limit(1);
		return $this;
    }
}