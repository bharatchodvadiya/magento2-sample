<?php
namespace SM\Vendors\Model;

class Order extends \Magento\Framework\Model\AbstractModel
{
    const RESOURCE_ORDER = 'SM\Vendors\Model\ResourceModel\Order';
    const VENDOR_ID = 'sm_product_vendor_id';
    const MAGENTO_RESOURCE_CONNECTION = 'Magento\Framework\App\ResourceConnection';
    const MAGENTO_DEFAULT_CONNECTION = '\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION';
    const VENDOR_ORDER_QUERY = 'INSERT INTO magento_sm_vendor_order (order_id,vendor_id,increment_id,status,subtotal,tax_amount,shipping_amount,shipping_tax_amount,shipping_method,discount_amount,discount_description,grand_total,commission_amount,shipping_carrier,shipping_method_title,shipping_carrier_title,base_subtotal,base_discount_amount,base_tax_amount,base_shipping_amount,base_shipping_tax_amount,base_grand_total,subtotal_incl_tax,shipping_incl_tax,base_subtotal_incl_tax,base_shipping_incl_tax,representative_id) VALUES (:orderId,:vendorId,:incrementId,:status,:subTotal,:taxAmount,:shippingAmount,:shippingTaxAmount,:shippingMethod,:discountAmount,:discountDescription,:grandTotal,:commissionAmount,:shippingCarrier,:shippingMethodTitle,:shippingCarrierTitle,:baseSubtotal,:baseDiscountAmount,:baseTaxAmount,:baseShippingAmount,:baseShippingTaxAmount,:baseGrandTotal,:subtotalInclTax,:shippingInclTax,:baseSubtotalInclTax,:baseShippingInclTax,:representativeId)';
    const VENDOR_DATA_ID = ':vendorId';
    const NULL_DATA = 0;
    const FIRST_DATA = 1;
    const STRING_PAD = 9;
    const DASH = '-';
    const VENDOR_TOTAL_ORDERS = "vendor_total_orders";
    const COMMISSION_AMOUNT = 'commission_amount';
    const AMOUNT_REFUNDED = 'commission_amount_refunded';
    const BASE_SUBTOTAL = 'base_subtotal';
    const DISCOUNT_AMOUNT = 'base_discount_amount';
    const VENDOR_FIELD_ID = 'vendor_id';
    const SALES_ORDER_QUERY = 'UPDATE magento_sales_order SET state=:status, status=:status, base_discount_invoiced=:baseDiscountInvoiced, base_shipping_invoiced=:baseShippingInvoiced, base_subtotal_invoiced=:baseSubtotalInvoiced, base_tax_invoiced=:baseTaxInvoiced, base_total_invoiced=:baseTotalInvoiced, base_total_paid=:baseTotalPaid, discount_invoiced=:discountInvoiced, shipping_invoiced=:shippingInvoiced, subtotal_invoiced=:subtotalInvoiced, tax_invoiced=:taxInvoiced, total_invoiced=:totalInvoiced, total_paid=:totalPaid WHERE entity_id=:orderId';
    const SALES_ORDER_GRID = 'UPDATE magento_sales_order_grid SET status=:status, base_total_paid=:baseTotalPaid, total_paid=:totalPaid WHERE entity_id=:orderId';
    const SALES_ORDER_PAYMENT = 'UPDATE magento_sales_order_payment SET base_shipping_captured=:baseShippingCaptured, shipping_captured=:baseShippingCaptured, base_amount_paid=:baseAmountPaid, amount_paid=:baseAmountPaid WHERE entity_id=:orderId';
    const ORDER_ID = ':orderId';
    const PROCESSING = 'processing';
    const STATUS = ':status';
    const BASE_TOTAL_PAID = ':baseTotalPaid';
    const TOTAL_PAID = ':totalPaid';
    const SHIPPING_CAPTURED = ':baseShippingCaptured';
    const AMOUNT_PAID = ':baseAmountPaid';
    const SHIPMENT_ORDER_QUERY = 'UPDATE magento_sales_order SET state=:status, status=:status WHERE entity_id=:orderId';
    const SALES_ORDER_GRID_QUERY = 'UPDATE magento_sales_order_grid SET status=:status WHERE entity_id=:orderId';
    const SHIPMENT_VENDOR_ORDER_QUERY = 'UPDATE magento_sm_vendor_order SET status=:status WHERE order_id=:orderId';
    const ORDER_COMPLETE = 'complete';
    const INVOICE_GRID_VENDOR_QUERY = 'UPDATE magento_sales_invoice_grid SET vendor_id=:vendorId WHERE order_id=:orderId';
    const SHIPMENT_ORDER_VENDOR_QUERY = 'UPDATE magento_sales_shipment_grid SET order_status=:status, vendor_id=:vendorId WHERE order_id=:orderId';
    const VENDOR_PARAM_ID = ':vendorId';
    const SALES_ORDER_ITEM_QUERY = 'UPDATE magento_sales_order_item SET qty_shipped=:qtyShipped WHERE order_id=:orderId';
    const QTY_SHIPPED = ':qtyShipped';
    const SHIPMENT_ORDER_ITEM_QUERY = 'UPDATE magento_sales_order_item SET vendor_id=:vendorId WHERE order_id=:orderId';
    const SALES_ORDER_CREDIT_QUERY = 'UPDATE magento_sales_order SET state=:status, status=:status, base_discount_refunded=:baseDiscountRefunded, base_shipping_refunded=:baseShippingRefunded, base_shipping_tax_refunded=:baseShippingTaxRefunded, base_subtotal_refunded=:baseSubtotalRefunded, base_tax_refunded=:baseTaxRefunded, base_total_offline_refunded=:baseTotalOfflineRefunded, base_total_refunded=:baseTotalRefunded, discount_refunded=:discountRefunded, shipping_refunded=:shippingRefunded, shipping_tax_refunded=:shippingTaxRefunded, subtotal_refunded=:subtotalRefunded, tax_refunded=:taxRefunded, total_offline_refunded=:totalOfflineRefunded, total_refunded=:totalRefunded, adjustment_negative=:adjustmentNegative, adjustment_positive=:adjustmentPositive, base_adjustment_negative=:baseAdjustmentNegative, base_adjustment_positive=:baseAdjustmentPositive, base_total_due=:baseTotalDue, total_due=:baseTotalDue WHERE entity_id=:orderId';
    const SALES_ORDER_GRID_CREDIT_QUERY = 'UPDATE magento_sales_order_grid SET status=:status, total_refunded=:totalRefunded WHERE entity_id=:orderId';
    const SALES_CREDITMEMO_GRID_QUERY = 'UPDATE magento_sales_creditmemo_grid SET state=:state, order_status=:status, vendor_id=:vendorId WHERE order_id=:orderId';
    const SALES_ORDER_ITEM_FIELDS_QUERY = 'UPDATE magento_sales_order_item SET qty_refunded=:qtyRefunded, amount_refunded=:amountRefunded, base_amount_refunded=:baseAmountRefunded, tax_refunded=:taxRefunded, base_tax_refunded=:baseTaxRefunded, discount_refunded=:discountRefunded, base_discount_refunded=:baseDiscountRefunded WHERE order_id=:orderId';
    const ORDER_CLOSED = 'closed';
    const BASE_DISCOUNT_REFUNDED = ':baseDiscountRefunded';
    const BASE_SHIPPING_REFUNDED = ':baseShippingRefunded';
    const BASE_SHIPPING_TAX_REFUNDED = ':baseShippingTaxRefunded';
    const BASE_SUBTOTAL_REFUNDED = ':baseSubtotalRefunded';
    const BASE_TAX_REFUNDED = ':baseTaxRefunded';
    const BASE_TOTAL_OFFLINE_REFUNDED = ':baseTotalOfflineRefunded';
    const BASE_TOTAL_REFUNDED = ':baseTotalRefunded';
    const DISCOUNT_REFUNDED = ':discountRefunded';
    const SHIPPING_REFUNDED = ':shippingRefunded';
    const SHIPPING_TAX_REFUNDED = ':shippingTaxRefunded';
    const SUBTOTAL_REFUNDED = ':subtotalRefunded';
    const TAX_REFUNDED = ':taxRefunded';
    const TOTAL_OFFLINE_REFUNDED = ':totalOfflineRefunded';
    const TOTAL_REFUNDED = ':totalRefunded';
    const ADJUSTMENT_NEGATIVE = ':adjustmentNegative';
    const ADJUSTMENT_POSITIVE = ':adjustmentPositive';
    const BASE_ADJUSTMENT_NEGATIVE = ':baseAdjustmentNegative';
    const BASE_ADJUSTMENT_POSITIVE = ':baseAdjustmentPositive';
    const BASE_TOTAL_DUE = ':baseTotalDue';
    const QUANTITY_REFUNDED = ':qtyRefunded';
    const AMOUNT_REFUNDED_PARAM = ':amountRefunded';
    const BASE_AMOUNT_REFUNDED = ':baseAmountRefunded';
    const ORDER_STATE = ':state';

    protected $_entityType;

    protected $_vendorCollection;

    protected $_adminSession;

    protected $_productLoader;

    protected $databaseConnection;

    protected $_helperData;

    protected $_orderObject;

    protected $_vendorData = null;

    protected $_orderCollection;

    protected $priceCurrency;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_ORDER);
    }

    public function __construct(
        \Magento\Framework\Model\Context $contextData,
        \Magento\Framework\Registry $registryData,
        \Magento\Eav\Model\Entity\Type $entityType,
        \SM\Vendors\Model\VendorFactory $vendorCollection,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Catalog\Model\ProductFactory $productLoader,
        \SM\Vendors\Helper\Email $helperData,
        \SM\Vendors\Model\OrderFactory $orderObject,
        \SM\Vendors\Model\ResourceModel\Order $orderCollection,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resourceData = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $orderData = []
    ) {
        $this->_entityType = $entityType;
        $this->_vendorCollection = $vendorCollection;
        $this->_adminSession = $adminSession;
        $this->_productLoader = $productLoader;
        $this->_helperData = $helperData;
        $this->_orderObject = $orderObject;
        $this->_orderCollection = $orderCollection;
        $this->priceCurrency = $priceCurrency;
        parent::__construct($contextData, $registryData, $resourceData, $resourceCollection, $orderData);
    }

    protected function getConnection()
    {
        $objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
        $this->databaseConnection = $objectManager->get(self::MAGENTO_RESOURCE_CONNECTION)->getConnection(self::MAGENTO_DEFAULT_CONNECTION);
        return $this->databaseConnection;
    }

    public function splitOrder($orderInstance)
    {
        $vendorsData = array();
        $entityType = $this->_entityType->setEntityTypeCode('vendor_order');
        
        foreach ($orderInstance->getAllVisibleItems() as $vendorItem) {
            $productData = $this->_productLoader->create()->load($vendorItem->getProductId());
            $vendorId = $productData->getData(self::VENDOR_ID);
            if(!isset($vendorsData[$vendorId])) {
                $vendorObject = $this->_vendorCollection->create()->load($vendorId);
                $vendorsData[$vendorId] = array(
                    'order_id' => $orderInstance->getId(),
                    'vendor_id' => $vendorId,
                    'commission' => $vendorObject->getVendorCommission(),
                    'status' => $orderInstance->getStatus(),
                    'subtotal' => $vendorItem->getRowTotal(),
                    'tax_amount' => $vendorItem->getTaxAmount(),
                    'discount_amount' => $vendorItem->getDiscountAmount(),
                    'discount_description' => '',
                    'grand_total' => $orderInstance->getGrandTotal() - $vendorItem->getDiscountAmount(),
                );
                $entityType->setData('vendor_object', $vendorObject);
                $lastId = $vendorObject->getData(self::VENDOR_TOTAL_ORDERS);
                $incrementId = $vendorObject->getVendorPrefix() . self::DASH . str_pad($lastId + self::FIRST_DATA, self::STRING_PAD, self::NULL_DATA, STR_PAD_LEFT);
                $vendorObject->setData(self::VENDOR_TOTAL_ORDERS, $lastId + self::FIRST_DATA);
                $vendorObject->save();

                $vendorsData[$vendorId]['increment_id'] = $incrementId;
                $vendorsData[$vendorId]['shipping_amount'] = $orderInstance->getShippingAmount();
                $vendorsData[$vendorId]['shipping_tax_amount'] = self::NULL_DATA;
                $vendorsData[$vendorId]['shipping_method'] = $orderInstance->getShippingMethod();
                $shippingDescription = $orderInstance->getShippingDescription();
                $shippingData = explode("-", $shippingDescription);
				$vendorsData[$vendorId]['shipping_method_title'] = isset($shippingData[self::FIRST_DATA])?$shippingData[self::FIRST_DATA]:null;
                $vendorsData[$vendorId]['shipping_carrier'] = $orderInstance->getShippingMethod();
                $vendorsData[$vendorId]['shipping_carrier_title'] = isset($shippingData[self::NULL_DATA])?$shippingData[self::NULL_DATA]:null;
            } else {
                $vendorsData[$vendorId]['subtotal'] += $vendorItem->getRowTotal();
                $vendorsData[$vendorId]['tax_amount'] += $vendorItem->getTaxAmount();
                $vendorsData[$vendorId]['discount_amount'] += $vendorItem->getDiscountAmount();
                $vendorsData[$vendorId]['grand_total'] += $orderInstance->getGrandTotal() - $vendorItem->getDiscountAmount();
            }
        }
        
        foreach ($vendorsData as $vendorValue) {
            $vendorOrder = $this->_orderObject->create();
            $vendorOrder->setData($vendorValue);
            $representativeId = '';
            $userData = $this->_adminSession->getUser();
            if($userData) {
                $representativeId = $userData->getId();
            }
            $commissionAmount = $vendorValue['grand_total'] * $vendorValue['commission'] / 100;
            $subtotalTaxAmount = $vendorValue['subtotal'] + $vendorValue['tax_amount'];
            $shippingInclTax = $vendorValue['shipping_amount'] + $vendorValue['shipping_tax_amount'];
            $vendorOrderQuery = $this->getConnection()->prepare(self::VENDOR_ORDER_QUERY);
            $vendorOrderQuery->bindParam(self::ORDER_ID, $vendorValue['order_id']);
            $vendorOrderQuery->bindParam(self::VENDOR_DATA_ID, $vendorValue['vendor_id']);
            $vendorOrderQuery->bindParam(':incrementId', $vendorValue['increment_id']);
            $vendorOrderQuery->bindParam(self::STATUS, $vendorValue['status']);
            $vendorOrderQuery->bindParam(':subTotal', $vendorValue['subtotal']);
            $vendorOrderQuery->bindParam(':taxAmount', $vendorValue['tax_amount']);
            $vendorOrderQuery->bindParam(':shippingAmount', $vendorValue['shipping_amount']);
            $vendorOrderQuery->bindParam(':shippingTaxAmount', $vendorValue['shipping_tax_amount']);
            $vendorOrderQuery->bindParam(':shippingMethod', $vendorValue['shipping_method']);
            $vendorOrderQuery->bindParam(':discountAmount', $vendorValue['discount_amount']);
            $vendorOrderQuery->bindParam(':discountDescription', $vendorValue['discount_description']);
            $vendorOrderQuery->bindParam(':grandTotal', $vendorValue['grand_total']);
            $vendorOrderQuery->bindParam(':commissionAmount', $commissionAmount);
            $vendorOrderQuery->bindParam(':shippingCarrier', $vendorValue['shipping_carrier']);
            $vendorOrderQuery->bindParam(':shippingMethodTitle', $vendorValue['shipping_method_title']);
            $vendorOrderQuery->bindParam(':shippingCarrierTitle', $vendorValue['shipping_carrier_title']);
            $vendorOrderQuery->bindParam(':baseSubtotal', $vendorValue['subtotal']);
            $vendorOrderQuery->bindParam(':baseDiscountAmount', $vendorValue['discount_amount']);
            $vendorOrderQuery->bindParam(':baseTaxAmount', $vendorValue['tax_amount']);
            $vendorOrderQuery->bindParam(':baseShippingAmount', $vendorValue['shipping_amount']);
            $vendorOrderQuery->bindParam(':baseShippingTaxAmount', $vendorValue['shipping_tax_amount']);
            $vendorOrderQuery->bindParam(':baseGrandTotal', $vendorValue['grand_total']);
            $vendorOrderQuery->bindParam(':subtotalInclTax', $subtotalTaxAmount);
            $vendorOrderQuery->bindParam(':shippingInclTax', $shippingInclTax);
            $vendorOrderQuery->bindParam(':baseSubtotalInclTax', $subtotalTaxAmount);
            $vendorOrderQuery->bindParam(':baseShippingInclTax', $shippingInclTax);
            $vendorOrderQuery->bindParam(':representativeId', $representativeId);
            $vendorOrderQuery->execute();
            $this->_helperData->sendNewOrderEmailToVendor($vendorOrder, $orderInstance);
        }
        return;
    }

    public function getByOriginOrderId($orderId, $vendorId)
    {
        $vendorOrder = $this->_orderCollection->getByOriginOrderId($orderId, $vendorId);
        if(!empty($vendorOrder)) {
            $vendorOrderObject = $this->_orderObject->create();
            $vendorOrderObject->setData($vendorOrder);
            return $vendorOrderObject;
        }
        return $this;
    }

    public function registerInvoice($orderInvoice)
    {
        $this->setStatus(self::PROCESSING);
        $this->setTotalInvoiced($this->getTotalInvoiced() + $orderInvoice->getGrandTotal());
        $this->setBaseTotalInvoiced($this->getBaseTotalInvoiced() + $orderInvoice->getBaseGrandTotal());
        
        $this->setSubtotalInvoiced($this->getSubtotalInvoiced() + $orderInvoice->getSubtotal());
        $this->setBaseSubtotalInvoiced($this->getBaseSubtotalInvoiced() + $orderInvoice->getBaseSubtotal());
        
        $this->setTaxInvoiced($this->getTaxInvoiced() + $orderInvoice->getTaxAmount());
        $this->setBaseTaxInvoiced($this->getBaseTaxInvoiced() + $orderInvoice->getBaseTaxAmount());
        $this->setShippingTaxInvoiced($this->getShippingTaxInvoiced() + $orderInvoice->getShippingTaxAmount());
        $this->setBaseShippingTaxInvoiced($this->getBaseShippingTaxInvoiced() + $orderInvoice->getBaseShippingTaxAmount());
        
        $this->setShippingInvoiced($this->getShippingInvoiced() + $orderInvoice->getShippingAmount());
        $this->setBaseShippingInvoiced($this->getBaseShippingInvoiced() + $orderInvoice->getBaseShippingAmount());
        
        $this->setDiscountInvoiced($this->getDiscountInvoiced() + $orderInvoice->getDiscountAmount());
        $this->setBaseDiscountInvoiced($this->getBaseDiscountInvoiced() + $orderInvoice->getBaseDiscountAmount());
        $this->setTotalPaid(
            $this->getTotalPaid()+$orderInvoice->getGrandTotal()
        );
        $this->setBaseTotalPaid(
            $this->getBaseTotalPaid()+$orderInvoice->getBaseGrandTotal()
        );
        $vendorId = $this->getVendorId();
        $this->_registerCommission($orderInvoice, self::COMMISSION_AMOUNT, $vendorId);
        $orderInvoice->setVendorId($this->getVendorId());
    }

    public function registerShipment($shipmentObject)
    {
        $shipmentObject->setVendorId($this->getVendorId());
    }

    public function registerCreditmemo($orderCreditmemo) {
        $orderRefund = $this->priceCurrency->round($orderCreditmemo->getGrandTotal());
        $baseOrderRefund = $this->priceCurrency->round($orderCreditmemo->getBaseGrandTotal());
                
        $this->setBaseTotalRefunded($baseOrderRefund);
        $this->setTotalRefunded($orderRefund);
        
        $this->setBaseSubtotalRefunded($orderCreditmemo->getBaseSubtotal());
        $this->setSubtotalRefunded($orderCreditmemo->getSubtotal());
        
        $this->setBaseTaxRefunded($orderCreditmemo->getBaseTaxAmount());
        $this->setTaxRefunded($orderCreditmemo->getTaxAmount());
        
        $this->setBaseShippingRefunded($orderCreditmemo->getBaseShippingAmount());
        $this->setShippingRefunded($orderCreditmemo->getShippingAmount());
        
        $this->setBaseShippingTaxRefunded($orderCreditmemo->getBaseShippingTaxAmount());
        $this->setShippingTaxRefunded($orderCreditmemo->getShippingTaxAmount());
        
        $this->setAdjustmentPositive($orderCreditmemo->getAdjustmentPositive());
        $this->setBaseAdjustmentPositive($orderCreditmemo->getBaseAdjustmentPositive());
        
        $this->setAdjustmentNegative($orderCreditmemo->getAdjustmentNegative());
        $this->setBaseAdjustmentNegative($orderCreditmemo->getBaseAdjustmentNegative());
        
        $this->setDiscountRefunded($orderCreditmemo->getDiscountAmount());
        $this->setBaseDiscountRefunded($orderCreditmemo->getBaseDiscountAmount());
        
        if ($orderCreditmemo->getDoTransaction()) {
            $this->setTotalOnlineRefunded($orderCreditmemo->getGrandTotal());
            $this->setBaseTotalOnlineRefunded($orderCreditmemo->getBaseGrandTotal());
        }
        else {
            $this->setTotalOfflineRefunded($orderCreditmemo->getGrandTotal());
            $this->setBaseTotalOfflineRefunded($orderCreditmemo->getBaseGrandTotal());
        }
        $this->setStatus('closed');
        $vendorId = $this->getVendorId();
        $this->_registerCommission($orderCreditmemo, self::AMOUNT_REFUNDED, $vendorId);
        $orderCreditmemo->setVendorId($this->getVendorId());
    }

    public function _registerCommission($passData, $fieldData, $vendorId)
    {
        if($fieldData !== self::COMMISSION_AMOUNT || $fieldData !== self::AMOUNT_REFUNDED) {
            return $this;
        }
        $vendorData = $this->getVendor($vendorId);
        if($vendorData->getId()) {
            $commissionSubject = $passData->getData(self::BASE_SUBTOTAL) - abs($passData->getData(self::DISCOUNT_AMOUNT));
            $this->setData($fieldData, $this->getData($fieldData) + (($vendorData->getCommission() * $commissionSubject) / 100));
        }        
    }

    public function getVendor($vendorId)
    {
        if($this->_vendorData == null) {
            $this->_vendorData = $this->_vendorCollection->create()->load($vendorId);
        }
        return $this->_vendorData;
    }

    public function saveSalesOrderField($orderInvoice, $orderId) {
        $salesOrderQuery = $this->getConnection()->prepare(self::SALES_ORDER_QUERY);
        $invoiceStatus = self::PROCESSING;
        $baseDiscountInvoiced = $orderInvoice->getBaseDiscountAmount();
        $baseShippingInvoiced = $orderInvoice->getBaseShippingAmount();
        $baseSubtotalInvoiced = $orderInvoice->getBaseSubtotal();
        $baseTaxInvoiced = $orderInvoice->getBaseTaxAmount();
        $baseTotalInvoiced = $orderInvoice->getBaseGrandTotal();
        $baseTotalPaid = $orderInvoice->getGrandTotal();
        $discountInvoiced = $orderInvoice->getDiscountAmount();
        $shippingInvoiced = $orderInvoice->getShippingAmount();
        $subtotalInvoiced = $orderInvoice->getSubtotal();
        $taxInvoiced = $orderInvoice->getTaxAmount();
        $totalInvoiced = $orderInvoice->getGrandTotal();
        $totalPaid = $orderInvoice->getGrandTotal();
        $salesOrderQuery->bindParam(self::STATUS, $invoiceStatus);
        $salesOrderQuery->bindParam(':baseDiscountInvoiced', $baseDiscountInvoiced);
        $salesOrderQuery->bindParam(':baseShippingInvoiced', $baseShippingInvoiced);
        $salesOrderQuery->bindParam(':baseSubtotalInvoiced', $baseSubtotalInvoiced);
        $salesOrderQuery->bindParam(':baseTaxInvoiced', $baseTaxInvoiced);
        $salesOrderQuery->bindParam(':baseTotalInvoiced', $baseTotalInvoiced);
        $salesOrderQuery->bindParam(self::BASE_TOTAL_PAID, $baseTotalPaid);
        $salesOrderQuery->bindParam(':discountInvoiced', $discountInvoiced);
        $salesOrderQuery->bindParam(':shippingInvoiced', $shippingInvoiced);
        $salesOrderQuery->bindParam(':subtotalInvoiced', $subtotalInvoiced);
        $salesOrderQuery->bindParam(':taxInvoiced', $taxInvoiced);
        $salesOrderQuery->bindParam(':totalInvoiced', $totalInvoiced);
        $salesOrderQuery->bindParam(self::TOTAL_PAID, $totalPaid);
        $salesOrderQuery->bindParam(self::ORDER_ID, $orderId);
        $salesOrderQuery->execute();
    }

    public function updateSalesOrderGrid($orderInvoice, $orderId) {
        $salesOrderGrid = $this->getConnection()->prepare(self::SALES_ORDER_GRID);
        $invoiceStatus = self::PROCESSING;
        $baseTotalPaid = $orderInvoice->getGrandTotal();
        $salesOrderGrid->bindParam(self::STATUS, $invoiceStatus);
        $salesOrderGrid->bindParam(self::BASE_TOTAL_PAID, $baseTotalPaid);
        $salesOrderGrid->bindParam(self::TOTAL_PAID, $baseTotalPaid);
        $salesOrderGrid->bindParam(self::ORDER_ID, $orderId);
        $salesOrderGrid->execute();
    }

    public function updateSalesOrderPayment($orderInvoice, $orderId) {
        $salesOrderPayment = $this->getConnection()->prepare(self::SALES_ORDER_PAYMENT);
        $shippingCaptured = $orderInvoice->getShippingAmount();
        $totalPaid = $orderInvoice->getGrandTotal();
        $salesOrderPayment->bindParam(self::SHIPPING_CAPTURED, $shippingCaptured);
        $salesOrderPayment->bindParam(self::AMOUNT_PAID, $totalPaid);
        $salesOrderPayment->bindParam(self::ORDER_ID, $orderId);
        $salesOrderPayment->execute();
    }

    public function updateInvoiceGridVendor($orderId, $vendorId) {
        $invoiceGridVendor = $this->getConnection()->prepare(self::INVOICE_GRID_VENDOR_QUERY);
        $invoiceGridVendor->bindParam(self::VENDOR_PARAM_ID, $vendorId);
        $invoiceGridVendor->bindParam(self::ORDER_ID, $orderId);
        $invoiceGridVendor->execute();
    }

    public function updateShipmentOrder($orderId, $itemQty) {
        $shipmentOrder = $this->getConnection()->prepare(self::SHIPMENT_ORDER_QUERY);
        $orderStatus = self::ORDER_COMPLETE;
        $shipmentOrder->bindParam(self::STATUS, $orderStatus);
        $shipmentOrder->bindParam(self::ORDER_ID, $orderId);
        $shipmentOrder->execute();

        $salesOrderGrid = $this->getConnection()->prepare(self::SALES_ORDER_GRID_QUERY);
        $salesOrderGrid->bindParam(self::STATUS, $orderStatus);
        $salesOrderGrid->bindParam(self::ORDER_ID, $orderId);
        $salesOrderGrid->execute();

        $salesOrderItem = $this->getConnection()->prepare(self::SALES_ORDER_ITEM_QUERY);
        $salesOrderItem->bindParam(self::QTY_SHIPPED, $itemQty);
        $salesOrderItem->bindParam(self::ORDER_ID, $orderId);
        $salesOrderItem->execute();

        $shipmentVendorOrder = $this->getConnection()->prepare(self::SHIPMENT_VENDOR_ORDER_QUERY);
        $shipmentVendorOrder->bindParam(self::STATUS, $orderStatus);
        $shipmentVendorOrder->bindParam(self::ORDER_ID, $orderId);
        $shipmentVendorOrder->execute();
    }

    public function updateShipmentOrderVendor($orderId, $vendorId) {
        $orderStatus = self::ORDER_COMPLETE;
        $shipmentOrderVendor = $this->getConnection()->prepare(self::SHIPMENT_ORDER_VENDOR_QUERY);
        $shipmentOrderVendor->bindParam(self::STATUS, $orderStatus);
        $shipmentOrderVendor->bindParam(self::VENDOR_PARAM_ID, $vendorId);
        $shipmentOrderVendor->bindParam(self::ORDER_ID, $orderId);
        $shipmentOrderVendor->execute();

        $shipmentOrderItem = $this->getConnection()->prepare(self::SHIPMENT_ORDER_ITEM_QUERY);
        $shipmentOrderItem->bindParam(self::VENDOR_PARAM_ID, $vendorId);
        $shipmentOrderItem->bindParam(self::ORDER_ID, $orderId);
        $shipmentOrderItem->execute();
    }

    public function updateCreditmemoOrder($orderId, $orderCreditmemo, $itemQty) {
        $orderStatus = self::ORDER_CLOSED;
        $baseDiscountRefunded = $orderCreditmemo->getBaseDiscountAmount();
        $baseShippingRefunded = $orderCreditmemo->getBaseShippingAmount();
        $baseShippingTaxRefunded = $orderCreditmemo->getBaseShippingTaxAmount();
        $baseSubtotalRefunded = $orderCreditmemo->getBaseSubtotal();
        $baseTaxRefunded = $orderCreditmemo->getBaseTaxAmount();
        $baseTotalOfflineRefunded = $orderCreditmemo->getBaseGrandTotal();
        $baseTotalRefunded = $orderCreditmemo->getBaseGrandTotal();
        $discountRefunded = $orderCreditmemo->getDiscountAmount();
        $shippingRefunded = $orderCreditmemo->getShippingAmount();
        $shippingTaxRefunded = $orderCreditmemo->getShippingTaxAmount();
        $subtotalRefunded = $orderCreditmemo->getSubtotal();
        $taxRefunded = $orderCreditmemo->getTaxAmount();
        $totalOfflineRefunded = $orderCreditmemo->getGrandTotal();
        $totalRefunded = $orderCreditmemo->getGrandTotal();
        $adjustmentNegative = $orderCreditmemo->getAdjustmentNegative();
        $adjustmentPositive = $orderCreditmemo->getAdjustmentPositive();
        $baseAdjustmentNegative = $orderCreditmemo->getBaseAdjustmentNegative();
        $baseAdjustmentPositive = $orderCreditmemo->getBaseAdjustmentPositive();
        $baseTotalDue = self::NULL_DATA;
        $vendorId = $orderCreditmemo->getVendorId();
        $salesOrderCreditmemo = $this->getConnection()->prepare(self::SALES_ORDER_CREDIT_QUERY);
        $salesOrderCreditmemo->bindParam(self::STATUS, $orderStatus);
        $salesOrderCreditmemo->bindParam(self::BASE_DISCOUNT_REFUNDED, $baseDiscountRefunded);
        $salesOrderCreditmemo->bindParam(self::BASE_SHIPPING_REFUNDED, $baseShippingRefunded);
        $salesOrderCreditmemo->bindParam(self::BASE_SHIPPING_TAX_REFUNDED, $baseShippingTaxRefunded);
        $salesOrderCreditmemo->bindParam(self::BASE_SUBTOTAL_REFUNDED, $baseSubtotalRefunded);
        $salesOrderCreditmemo->bindParam(self::BASE_TAX_REFUNDED, $baseTaxRefunded);
        $salesOrderCreditmemo->bindParam(self::BASE_TOTAL_OFFLINE_REFUNDED, $baseTotalOfflineRefunded);
        $salesOrderCreditmemo->bindParam(self::BASE_TOTAL_REFUNDED, $baseTotalRefunded);
        $salesOrderCreditmemo->bindParam(self::DISCOUNT_REFUNDED, $discountRefunded);
        $salesOrderCreditmemo->bindParam(self::SHIPPING_REFUNDED, $shippingRefunded);
        $salesOrderCreditmemo->bindParam(self::SHIPPING_TAX_REFUNDED, $shippingTaxRefunded);
        $salesOrderCreditmemo->bindParam(self::SUBTOTAL_REFUNDED, $subtotalRefunded);
        $salesOrderCreditmemo->bindParam(self::TAX_REFUNDED, $taxRefunded);
        $salesOrderCreditmemo->bindParam(self::TOTAL_OFFLINE_REFUNDED, $totalOfflineRefunded);
        $salesOrderCreditmemo->bindParam(self::TOTAL_REFUNDED, $totalRefunded);
        $salesOrderCreditmemo->bindParam(self::ADJUSTMENT_NEGATIVE, $adjustmentNegative);
        $salesOrderCreditmemo->bindParam(self::ADJUSTMENT_POSITIVE, $adjustmentPositive);
        $salesOrderCreditmemo->bindParam(self::BASE_ADJUSTMENT_NEGATIVE, $baseAdjustmentNegative);
        $salesOrderCreditmemo->bindParam(self::BASE_ADJUSTMENT_POSITIVE, $baseAdjustmentPositive);
        $salesOrderCreditmemo->bindParam(self::BASE_TOTAL_DUE, $baseTotalDue);
        $salesOrderCreditmemo->bindParam(self::ORDER_ID, $orderId);
        $salesOrderCreditmemo->execute();

        $salesOrderGridCreditmemo = $this->getConnection()->prepare(self::SALES_ORDER_GRID_CREDIT_QUERY);
        $salesOrderGridCreditmemo->bindParam(self::STATUS, $orderStatus);
        $salesOrderGridCreditmemo->bindParam(self::TOTAL_REFUNDED, $totalRefunded);
        $salesOrderGridCreditmemo->bindParam(self::ORDER_ID, $orderId);
        $salesOrderGridCreditmemo->execute();

        $salesCreditmemoGrid = $this->getConnection()->prepare(self::SALES_CREDITMEMO_GRID_QUERY);
        $orderState = 2;
        $salesCreditmemoGrid->bindParam(self::ORDER_STATE, $orderState);
        $salesCreditmemoGrid->bindParam(self::STATUS, $orderStatus);
        $salesCreditmemoGrid->bindParam(self::VENDOR_PARAM_ID, $vendorId);
        $salesCreditmemoGrid->bindParam(self::ORDER_ID, $orderId);
        $salesCreditmemoGrid->execute();

        $salesOrderItemQuery = $this->getConnection()->prepare(self::SALES_ORDER_ITEM_FIELDS_QUERY);
        $salesOrderItemQuery->bindParam(self::QUANTITY_REFUNDED, $itemQty);
        $salesOrderItemQuery->bindParam(self::AMOUNT_REFUNDED_PARAM, $baseSubtotalRefunded);
        $salesOrderItemQuery->bindParam(self::BASE_AMOUNT_REFUNDED, $baseSubtotalRefunded);
        $salesOrderItemQuery->bindParam(self::TAX_REFUNDED, $taxRefunded);
        $salesOrderItemQuery->bindParam(self::BASE_TAX_REFUNDED, $baseTaxRefunded);
        $salesOrderItemQuery->bindParam(self::DISCOUNT_REFUNDED, $discountRefunded);
        $salesOrderItemQuery->bindParam(self::BASE_DISCOUNT_REFUNDED, $baseDiscountRefunded);
        $salesOrderItemQuery->bindParam(self::ORDER_ID, $orderId);
        $salesOrderItemQuery->execute();
    }
}