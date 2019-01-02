<?php
namespace SM\Vendors\Model\ResourceModel\Reports\vendorOrders;

class Collection extends \Magento\Sales\Model\ResourceModel\Report\Collection\AbstractCollection
{
    protected $_type = 'created_at';

    protected $_periodFormat;

    protected $_selectedColumns = [];

    protected $_vendorIds = [];

    protected $_vendorsOrderTable = 'magento_sm_vendor_order';

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\ResourceModel\Report $resource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $resource->init($this->_vendorsOrderTable, 'entity_id');
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $resource, $connection);
    }

    /**
     * Get selected columns
     */
    protected function _getSelectedColumns()
    {
        $connection = $this->getConnection();
        if ('month' == $this->_period) {
            $this->_periodFormat = $connection->getDateFormatSql('mo.'.$this->_type, '%Y-%m');
        } elseif ('year' == $this->_period) {
            $this->_periodFormat = $connection->getDateExtractSql(
                'mo.'.$this->_type,
                \Magento\Framework\DB\Adapter\AdapterInterface::INTERVAL_YEAR
            );
        } else {
            $this->_periodFormat = $connection->getDateFormatSql('mo.'.$this->_type, '%Y-%m-%d');
        }

        $this->setAggregatedColumns([
            'number_order' => 'count(distinct(e.entity_id))',
            'grand_total' => 'sum(e.grand_total)',
            'shipping_amount' => 'sum(e.shipping_amount)',
            'currency_code' => 'mo.order_currency_code',
            'tax_amount' => 'sum(e.tax_amount)',
            'commission_amount' => 'sum(commission_amount)',
            'period' => $this->_periodFormat
        ]);
        
        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->_selectedColumns = [
                'number_order' => 'count(e.entity_id)',
                'store_name' => 'mo.store_name',
                'store_id' => 'mo.store_id',
                'period' => $this->_periodFormat,
                'currency_code' => 'mo.order_currency_code',
                'grand_total' => 'sum(e.grand_total)',
                'shipping_amount' => 'sum(e.shipping_amount)',
                'tax_amount' => 'sum(e.tax_amount)',
                'vendor_name' => 'v.vendor_name',
                'vendor_prefix' => 'v.vendor_prefix',
                'commission_amount' => 'sum(commission_amount)'
            ];
        }

        if ($this->isTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns();
        }

        if ($this->isSubTotals()) {
            $this->_selectedColumns = $this->getAggregatedColumns() + array('period' => $this->_periodFormat);
        }
        return $this->_selectedColumns;
    }

    /**
     * Add selected data
     */
    protected  function _initSelect()
    {
        $columns = $this->_getSelectedColumns();
        $mainTable = $this->getResource()->getMainTable();
        
        $this->getSelect()->from(['e' => $mainTable], $columns)
            ->join(['mo' => 'magento_sales_order'], 'mo.entity_id = e.order_id')
            ->join(['v' => 'magento_sm_vendor'], 'v.vendor_id = e.vendor_id');
        
        if (!$this->isTotals() && !$this->isSubTotals()) {
            $this->getSelect()->group([
                $this->_periodFormat,'e.vendor_id'
            ]);
        }
        if ($this->isSubTotals()) {
            $this->getSelect()->group([
                $this->_periodFormat
            ]);
        }
        return $this;
    }

    /*
     * Apply date range filter
     */
    protected function _applyDateRangeFilter()
    {
        if (!is_null($this->_from)) {
            $this->getSelect()->where('DATE(mo.'.$this->_type.') >= ?', $this->_from);
        }
        if (!is_null($this->_to)) {
            $this->getSelect()->where('DATE(mo.'.$this->_type.') <= ?', $this->_to);
        }
        return $this;
    }

    /**
     * Apply stores filter to select object
     */
    protected function _applyStoresFilter()
    {
        $nullCheck = false;
        $storeIds = $this->_storesIds;
        if (!is_array($storeIds)) {
            $storeIds = array($storeIds);
        }
        $storeIds = array_unique($storeIds);
        if ($index = array_search(null, $storeIds)) {
            unset($storeIds[$index]);
            $nullCheck = true;
        }
        if ($nullCheck) {
            $this->getSelect()->where('mo.store_id IN(?) OR mo.store_id IS NULL', $storeIds);
        } elseif ($storeIds[0] != '') {
            $this->getSelect()->where('mo.store_id IN(?)', $storeIds);
        }
        return $this;
    }

    /**
     * Apply order status filter
     */
    protected function _applyOrderStatusFilter()
    {
        if (is_null($this->_orderStatus)) {
            return $this;
        }
        $orderStatus = $this->_orderStatus;
        if (!is_array($orderStatus)) {
            $orderStatus = array($orderStatus);
        }
        $this->getSelect()->where('e.status IN(?)', $orderStatus);
        return $this;
    }

    /**
     * Apply vendor filter
     */
    protected function _applyVendorFilter()
    {
        $nullCheck = false;
        $vendorIds = $this->_vendorIds;
        if (!is_array($vendorIds)) {
            $vendorIds = array($vendorIds);
        }
        $vendorIds = array_unique($vendorIds);
        if ($index = array_search(null, $vendorIds)) {
            unset($vendorIds[$index]);
            $nullCheck = true;
        }
        if(!empty($vendorIds)) {
            if ($nullCheck) {
                $this->getSelect()->where('e.vendor_id IN(?) OR e.vendor_id IS NULL', $vendorIds);
            } elseif ($vendorIds[0] != '') {
                $this->getSelect()->where('e.vendor_id IN(?)', $vendorIds);
            }
        }
        return $this;
    }

    public function addVendorFilter($vendorIds)
    {
        if(!empty($vendorIds)) {
            return $this->addFieldToFilter('e.vendor_id', ['in' => $vendorIds]);
        }
        return $this;
    }
}
