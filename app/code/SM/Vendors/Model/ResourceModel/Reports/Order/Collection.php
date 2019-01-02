<?php
namespace SM\Vendors\Model\ResourceModel\Reports\Order;

class Collection extends \SM\Vendors\Model\ResourceModel\Order\Collection
{
	protected $_salesAmountExpression;

	protected $_orderConfig;

	protected $_scopeConfig;

	protected $_storeManager;

    protected $_isLive = false;

    protected $_helperData;

	protected function _construct()
    {
    	parent::_construct();
    }

	public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Magento\Framework\DB\Helper $coreResourceHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\ResourceModel\Report\OrderFactory $reportOrderFactory,
        \SM\Vendors\Helper\Data $helperData,
        \Magento\Framework\DB\Adapter\AdapterInterface $connectionData = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connectionData,
            $resource
        );
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->_orderConfig = $orderConfig;
        $this->_helperData = $helperData;
        $this->joinParentOrder();
    }

    public function checkIsLive($range)
    {
        $this->_isLive = (bool)(!$this->_scopeConfig->getValue(
            'sales/dashboard/use_aggregated_data',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ));
        return $this;
    }

    public function isLive()
    {
        return $this->_isLive;
    }

	protected function _getSalesAmountExpression()
    {
        if (null === $this->_salesAmountExpression) {
            $connectionData = $this->getConnection();
            $expressionTransferObject = new \Magento\Framework\DataObject(
                [
                    'expression' => '%s - %s - %s - (%s - %s - %s)',
                    'arguments' => [
                        $connectionData->getIfNullSql('main_table.base_total_invoiced', 0),
                        $connectionData->getIfNullSql('main_table.base_tax_invoiced', 0),
                        $connectionData->getIfNullSql('main_table.base_shipping_invoiced', 0),
                        $connectionData->getIfNullSql('main_table.base_total_refunded', 0),
                        $connectionData->getIfNullSql('main_table.base_tax_refunded', 0),
                        $connectionData->getIfNullSql('main_table.base_shipping_refunded', 0),
                    ],
                ]
            );

            $this->_eventManager->dispatch(
                'sales_prepare_amount_expression',
                ['collection' => $this, 'expression_object' => $expressionTransferObject]
            );
            $this->_salesAmountExpression = vsprintf(
                $expressionTransferObject->getExpression(),
                $expressionTransferObject->getArguments()
            );
        }

        return $this->_salesAmountExpression;
    }

	public function calculateSales($isFilter = 0)
    {
    	$statuses = $this->_orderConfig->getStateStatuses(\Magento\Sales\Model\Order::STATE_CANCELED);
        if (empty($statuses)) {
            $statuses = [0];
        }
        $connectionData = $this->getConnection();

        if ($this->_scopeConfig->getValue(
            'sales/dashboard/use_aggregated_data',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
        	$this->setMainTable('sales_order_aggregated_created');
            $this->removeAllFieldsFromSelect();
            $averageExpr = $connectionData->getCheckSql(
                'SUM(main_table.orders_count) > 0',
                'SUM(main_table.total_revenue_amount)/SUM(main_table.orders_count)',
                0
            );
            $this->getSelect()->columns(
                ['lifetime' => 'SUM(main_table.total_revenue_amount)', 'average' => $averageExpr]
            );
            if (!$isFilter) {
                $this->addFieldToFilter(
                    'store_id',
                    ['eq' => $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId()]
                );
            }
            $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses);
        }
        else {
        	$this->setMainTable('sm_vendor_order');
            $this->removeAllFieldsFromSelect();
            $expr = $this->_getSalesAmountExpression();
            if ($isFilter == 0) {
                $expr = '(' . $expr . ') * main_table.base_to_global_rate';
            }
            $this->getSelect()->columns(
                ['lifetime' => "SUM({$expr})", 'average' => "AVG({$expr})"]
            )->where(
                'main_table.status NOT IN(?)',
                $statuses
            )->where(
                'main_table.state NOT IN(?)',
                [\Magento\Sales\Model\Order::STATE_NEW, \Magento\Sales\Model\Order::STATE_PENDING_PAYMENT]
            );
        }
    }

    public function getDateRange($range, $customStart, $customEnd, $returnObjects = false)
    {
        $dateEnd = new \DateTime();
        $dateStart = new \DateTime();
        // go to the end of a day
        $dateEnd->setTime(23, 59, 59);
        $dateStart->setTime(0, 0, 0);

        switch ($range) {
            case '24h':
                $dateEnd = new \DateTime();
                $dateEnd->modify('+1 hour');
                $dateStart = clone $dateEnd;
                $dateStart->modify('-1 day');
                break;

            case '7d':
                $dateStart->modify('-6 days');
                break;

            case '1m':
                $dateStart->setDate(
                    $dateStart->format('Y'),
                    $dateStart->format('m'),
                    $this->_scopeConfig->getValue(
                        'reports/dashboard/mtd_start',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                );
                break;

            case 'custom':
                $dateStart = $customStart ? $customStart : $dateEnd;
                $dateEnd = $customEnd ? $customEnd : $dateEnd;
                break;

            case '1y':
            case '2y':
                $startMonthDay = explode(
                    ',',
                    $this->_scopeConfig->getValue(
                        'reports/dashboard/ytd_start',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    )
                );
                $startMonth = isset($startMonthDay[0]) ? (int)$startMonthDay[0] : 1;
                $startDay = isset($startMonthDay[1]) ? (int)$startMonthDay[1] : 1;
                $dateStart->setDate($dateStart->format('Y'), $startMonth, $startDay);
                if ($range == '2y') {
                    $dateStart->modify('-1 year');
                }
                break;
        }

        if ($returnObjects) {
            return [$dateStart, $dateEnd];
        } else {
            return ['from' => $dateStart, 'to' => $dateEnd, 'datetime' => true];
        }
    }

    public function addCreateAtPeriodFilter($period)
    {
        list($from, $to) = $this->getDateRange($period, 0, 0, true);
        $this->checkIsLive($period);

        if ($this->isLive()) {
            $fieldToFilter = 'created_at';
        } else {
            $fieldToFilter = 'period';
        }
        $this->addFieldToFilter(
            $fieldToFilter,
            [
                'from' => $from->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
                'to' => $to->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
            ]
        );
        return $this;
    }

    public function calculateTotals($isFilter = 0)
    {
        if ($this->isLive()) {
            $this->_calculateTotalsLive($isFilter);
        } else {
            $this->_calculateTotalsAggregated($isFilter);
        }

        return $this;
    }

    protected function _calculateTotalsLive($isFilter = 0)
    {
        $this->setMainTable('sm_vendor_order');
        $this->removeAllFieldsFromSelect();
        $connection = $this->getConnection();

        $baseTaxInvoiced = $connection->getIfNullSql('main_table.base_tax_invoiced', 0);
        $baseTaxRefunded = $connection->getIfNullSql('main_table.base_tax_refunded', 0);
        $baseShippingInvoiced = $connection->getIfNullSql('main_table.base_shipping_invoiced', 0);
        $baseShippingRefunded = $connection->getIfNullSql('main_table.base_shipping_refunded', 0);

        $revenueExp = $this->_getSalesAmountExpression();
        $taxExp = sprintf('%s - %s', $baseTaxInvoiced, $baseTaxRefunded);
        $shippingExp = sprintf('%s - %s', $baseShippingInvoiced, $baseShippingRefunded);

        if ($isFilter == 0) {
            $rateExp = $connection->getIfNullSql('po.base_to_global_rate', 0);
            $this->getSelect()->columns(
                [
                    'revenue' => new \Zend_Db_Expr(sprintf('SUM((%s) * %s)', $revenueExp, $rateExp)),
                    'tax' => new \Zend_Db_Expr(sprintf('SUM((%s) * %s)', $taxExp, $rateExp)),
                    'shipping' => new \Zend_Db_Expr(sprintf('SUM((%s) * %s)', $shippingExp, $rateExp)),
                ]
            );
        } else {
            $this->getSelect()->columns(
                [
                    'revenue' => new \Zend_Db_Expr(sprintf('SUM(%s)', $revenueExp)),
                    'tax' => new \Zend_Db_Expr(sprintf('SUM(%s)', $taxExp)),
                    'shipping' => new \Zend_Db_Expr(sprintf('SUM(%s)', $shippingExp)),
                ]
            );
        }

        $this->getSelect()->columns(
            ['quantity' => 'COUNT(main_table.entity_id)']
        )->where(
            'main_table.status NOT IN (?)',
            [\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT, \Magento\Sales\Model\Order::STATE_NEW]
        );

        return $this;
    }

    protected function _calculateTotalsAggregated($isFilter = 0)
    {
        $this->setMainTable('sales_order_aggregated_created');
        $this->removeAllFieldsFromSelect();
        $this->getSelect()->columns(
            [
                'revenue' => 'SUM(main_table.total_revenue_amount)',
                'tax' => 'SUM(main_table.total_tax_amount_actual)',
                'shipping' => 'SUM(main_table.total_shipping_amount_actual)',
                'quantity' => 'SUM(orders_count)',
            ]
        );
        $statuses = $this->_orderConfig->getStateStatuses(\Magento\Sales\Model\Order::STATE_CANCELED);
        if (empty($statuses)) {
            $statuses = [0];
        }
        $this->getSelect()->where('main_table.order_status NOT IN(?)', $statuses);
        return $this;
    }

    public function addItemCountExpr()
    {
        $this->getSelect()->join(['oi' => 'magento_sales_order_item'], 'oi.order_id = po.entity_id AND oi.vendor_id = main_table.vendor_id', ['items_count'=>'COUNT(oi.item_id)']);
        $this->getSelect()->group('main_table.entity_id');
        return $this;
    }

    public function groupByCustomer()
    {
        $this->getSelect()->where('po.customer_id IS NOT NULL')->group('po.customer_id');
        return $this;
    }

    public function addOrdersCount()
    {
        $this->addFieldToFilter('state', ['neq' => \Magento\Sales\Model\Order::STATE_CANCELED]);
        $this->getSelect()->columns(['orders_count' => 'COUNT(main_table.entity_id)']);
        return $this;
    }

    public function joinCustomerName($alias = 'name')
    {
        $fields = ['po.customer_firstname', 'po.customer_lastname'];
        $fieldConcat = $this->getConnection()->getConcatSql($fields, ' ');
        $this->getSelect()->columns([$alias => $fieldConcat]);
        return $this;
    }

    public function orderByCreatedAt($dir = self::SORT_ORDER_DESC)
    {
        $this->getSelect()->order('po.created_at ' .$dir);
        return $this;
    }

    public function addRevenueToSelect($convertCurrency = false)
    {
        if ($convertCurrency) {
            $this->getSelect()->columns(['revenue' => '(main_table.base_grand_total * po.base_to_global_rate)']);
        } else {
            $this->getSelect()->columns(['revenue' => 'base_grand_total']);
        }
        return $this;
    }

    public function addSumAvgTotals($storeId = 0)
    {
        $expr = $this->getTotalsExpression(
            $storeId,
            $this->getConnection()->getIfNullSql('main_table.base_subtotal_refunded', 0),
            $this->getConnection()->getIfNullSql('main_table.base_subtotal_canceled', 0),
            $this->getConnection()->getIfNullSql('main_table.base_discount_canceled', 0)
        );

        $this->getSelect()->columns(
            ['orders_avg_amount' => "AVG({$expr})"]
        )->columns(
            ['orders_sum_amount' => "SUM({$expr})"]
        );
        return $this;
    }

    protected function getTotalsExpression(
        $storeId,
        $baseSubtotalRefunded,
        $baseSubtotalCanceled,
        $baseDiscountCanceled
    ) {
        $template = ($storeId != 0)
            ? '(main_table.base_subtotal - %2$s - %1$s - ABS(main_table.base_discount_amount) - %3$s)'
            : '((main_table.base_subtotal - %1$s - %2$s - ABS(main_table.base_discount_amount) - %3$s) '
                . ' * po.base_to_global_rate)';
        return sprintf($template, $baseSubtotalRefunded, $baseSubtotalCanceled, $baseDiscountCanceled);
    }

    public function orderByTotalAmount($dir = self::SORT_ORDER_DESC)
    {
        $this->getSelect()->order('orders_sum_amount ' . $dir);
        return $this;
    }

    public function joinParentOrder() {
        $this->getSelect()->join(['po' => 'magento_sales_order'], 'main_table.order_id = po.entity_id', null);
        if($vendorData = $this->_helperData->getVendorLogin()) {
            $this->addFieldToFilter('main_table.vendor_id', $vendorData['vendor_id']);
        }
        return $this;
    }

    public function setPageSize($size) {
        $this->getSelect()->limit($size);
        return $this;
    }
}
