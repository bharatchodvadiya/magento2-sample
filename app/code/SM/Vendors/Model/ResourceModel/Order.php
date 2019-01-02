<?php
namespace SM\Vendors\Model\ResourceModel;

class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const ORDER_TABEL = 'magento_sm_vendor_order';
    const ENTITY_ID = 'entity_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::ORDER_TABEL, self::ENTITY_ID);
    }

    public function getByOriginOrderId($orderId, $vendorId)
    {
        $connectionData = $this->getConnection();
        $selectData = $connectionData->select()
            ->from($this->getTable(self::ORDER_TABEL))
            ->where('order_id = ?', (int)$orderId)
            ->where('vendor_id = ?', (int)$vendorId)
            ->limit(1);
        $resultData = $connectionData->fetchRow($selectData);
        return $resultData;
    }
}