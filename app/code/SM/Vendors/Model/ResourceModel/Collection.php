<?php
namespace SM\Vendors\Model\ResourceModel\Reports\Customer;

class Collection extends \Magento\Reports\Model\ResourceModel\Customer\Collection
{
	const SM_HELPER = '\SM\Vendors\Helper\Data';
	const ENTITY_ID = 'entity_id';

	protected $_addOrderStatistics = false;

	protected $_addOrderStatFilter = false;

	public function addCustomerName()
    {
        $this->addNameToSelect();
        return $this;
    }

    public function joinOrders($from = '', $to = '')
    {
        if ($from != '' && $to != '') {
            $dateFilter = " AND orders.created_at BETWEEN '{$from}' AND '{$to}'";
        } else {
            $dateFilter = '';
        }
        $this->getSelect()->joinLeft(['orders' => 'sm_vendor_order'], "orders.customer_id = e.entity_id".$dateFilter, []);
        return $this;
    }

    public function joinVendorCustomer() {
    	$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    	$helperData = $objectManager->create(self::SM_HELPER);
        if($vendorData = $helperData->getVendorLogin()) {
    		$this->getSelect()->join(['vc' => 'magento_sm_vendor_customer'], 'vc.customer_id = main_table.entity_id',null)->where('vc.vendor_id = ?', $vendorData['vendor_id'])->group('main_table.entity_id');
        }
        return $this;
    }

    public function addOrdersStatistics($isFilter = false)
    {
        $this->_addOrderStatistics = true;
        $this->_addOrderStatFilter = (bool)$isFilter;
        return $this;
    }

    protected function _addOrdersStatistics()
    {
        $customerIds = $this->getColumnValues($this->getResource()->getIdFieldName());
        if ($this->_addOrderStatistics && !empty($customerIds)) {
            $connection = $this->orderResource->getConnection();
            $baseSubtotalRefunded = $connection->getIfNullSql('orders.base_subtotal_refunded', 0);
            $baseSubtotalCanceled = $connection->getIfNullSql('orders.base_subtotal_canceled', 0);
            $baseDiscountCanceled = $connection->getIfNullSql('orders.base_discount_canceled', 0);
            $totalExpr = $this->_addOrderStatFilter ?
                "(orders.base_subtotal-{$baseSubtotalCanceled}-{$baseSubtotalRefunded} - {$baseDiscountCanceled}"
                    . " - ABS(orders.base_discount_amount))*orders.base_to_global_rate" :
                "orders.base_subtotal-{$baseSubtotalCanceled}-{$baseSubtotalRefunded} - {$baseDiscountCanceled}"
                    . " - ABS(orders.base_discount_amount)";

            $select = $this->orderResource->getConnection()->select();
            $select->from(
                ['orders' => 'magento_sm_vendor_order'],
                [
                    'orders_avg_amount' => "AVG({$totalExpr})",
                    'orders_sum_amount' => "SUM({$totalExpr})",
                    'orders_count' => 'COUNT(orders.entity_id)',
                    'customer_id' => 'po.customer_id'
                ]
            )->where(
                'orders.state <> ?',
                \Magento\Sales\Model\Order::STATE_CANCELED
            )->where(
                'po.customer_id IN(?)',
                $customerIds
            )->group(
                'po.customer_id'
            );

            $select->join(['po' => 'magento_sales_order'], 'orders.order_id = po.entity_id', null);
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helperData = $objectManager->create(self::SM_HELPER);
            if($vendorData = $helperData->getVendorLogin()) {
                $select->where('orders.vendor_id = ?', $vendorData['vendor_id']);
            }
            foreach ($this->orderResource->getConnection()->fetchAll($select) as $ordersInfo) {
                $this->getItemById($ordersInfo['customer_id'])->addData($ordersInfo);
            }
        }
        return $this;
    }

    protected function _afterLoad()
    {
        $this->_addOrdersStatistics();
        return $this;
    }

    public function orderByCustomerRegistration($dir = self::SORT_ORDER_DESC)
    {
        $this->addAttributeToSort(self::ENTITY_ID, $dir);
        return $this;
    }
}