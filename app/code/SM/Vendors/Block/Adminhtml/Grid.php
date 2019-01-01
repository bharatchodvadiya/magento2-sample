<?php
namespace SM\Vendors\Block\Adminhtml\Shipment;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	const DEFAULT_DIR = 'DESC';
    const ROW_URL = 'vendors/order_shipment/view';
    const SHIPMENT_ID = 'shipment_id';
    const COME_FROM = 'come_from';
    const SHIPMENT = 'shipment';
    const SHIPMENT_GRID = 'shipmentGrid';
    const ORDER_PREFIX = 'smo';
    const ORDER_TABLE = 'magento_sm_vendor_order';
    const MAIN_TABLE_ORDER = "main_table.order_id = smo.order_id AND smo.vendor_id = ";
    const MAIN_TABLE_VENDOR = " AND main_table.vendor_id = ";
    const VENDOR_ID = 'vendor_id';
    const VENDOR_INCREMENT = 'order_increment_id';
    const SMO_INCREMENT = 'smo.increment_id';
    const ENTITY_ID = 'entity_id';
    const SHIPMENT_IDS = 'shipment_ids';
    const PDF_SHIPMENTS = 'pdfshipments_order';
    const LABEL = 'label';
    const PDF_PACKING_SLIPS = 'PDF Packingslips';
    const URL = 'url';
    const VENDORS_PDF = 'vendors/shipment/pdfshipments';
    const PRINT_SHIPPING = 'print_shipping_label';
    const SHIPPING_LABELS = 'Print Shipping Labels';
    const PRINT_SHIPPING_METHOD = 'vendors/shipment/massPrintShippingLabel';
	
    /**
     * @var \SM\Vendors\Model\ResourceModel\Shipment\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \SM\Vendors\Model\Shipment
     */
    protected $_shipmentData;

	protected $_helperData;

	/**
     * @param \Magento\Backend\Block\Template\Context $contextData
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \SM\Vendors\Model\Shipment $shipmentData
     * @param \SM\Vendors\Model\ResourceModel\Shipment\CollectionFactory $collectionFactory
     * @param \SM\Vendors\Helper\Data $helperData
     * @param array $gridData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $contextData,
        \Magento\Backend\Helper\Data $backendHelper,
        \SM\Vendors\Model\Shipment $shipmentData,
        \SM\Vendors\Model\ResourceModel\Shipment\CollectionFactory $collectionFactory,
        \SM\Vendors\Helper\Data $helperData,
        array $gridData = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_shipmentData = $shipmentData;
        $this->_helperData = $helperData;
        parent::__construct($contextData, $backendHelper, $gridData);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId(self::SHIPMENT_GRID);
        $this->setDefaultDir(self::DEFAULT_DIR);
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collectionData = $this->_collectionFactory->create();
        if($vendorData = $this->_helperData->getVendorLogin()) {
            $collectionData->getSelect()
                ->join([self::ORDER_PREFIX => self::ORDER_TABLE], 
                    self::MAIN_TABLE_ORDER.$vendorData[self::VENDOR_ID].self::MAIN_TABLE_VENDOR.$vendorData[self::VENDOR_ID], 
                    [self::VENDOR_INCREMENT => self::SMO_INCREMENT]);
            $this->setCollection($collectionData);
            return parent::_prepareCollection();
        }
        else {
            $this->setCollection($collectionData);
            return parent::_prepareCollection();
        }
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', [
            'header'    => __('Shipment #'),
            'index'     => 'increment_id',
            'filter_index' => 'main_table.increment_id',
            'type'      => 'text',
        ]);

        $this->addColumn('created_at', [
            'header' => __('Date Shipped'),
            'index'  => 'created_at',
            'type'   => 'datetime',
        ]);

        $this->addColumn('order_increment_id', [
            'header'    => __('Order #'),
            'index'     => 'order_increment_id',
            'filter_index' => 'smo.increment_id',
            'type'      => 'text',
        ]);

        $this->addColumn('order_created_at', [
            'header' => __('Order Date'),
            'index'  => 'order_created_at',
            'type'   => 'datetime',
        ]);

        $this->addColumn('shipping_name', [
            'header'    => __('Ship to Name'),
            'index'     => 'shipping_name',
        ]);

        $this->addColumn('total_qty', [
            'header' => __('Total Qty'),
            'index'  => 'total_qty',
            'type'   => 'number',
        ]);

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('View'),
                        'url' => [
                            'base' => 'vendors/order_shipment/view',
                            'params' => ['come_from' => 'shipment']
                        ],
                        'field' => 'shipment_id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
        $this->sortColumnsByOrder();
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField(self::ENTITY_ID);
        $this->getMassactionBlock()->setFormFieldName(self::SHIPMENT_IDS);
        $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->addItem(
            self::PDF_SHIPMENTS,
            [
                self::LABEL => __(self::PDF_PACKING_SLIPS),
                self::URL => $this->getUrl(self::VENDORS_PDF,[self::ENTITY_ID=>$this->getRequest()->getParam(self::ENTITY_ID)])
            ]
        );
        $this->getMassactionBlock()->addItem(
            self::PRINT_SHIPPING,
            [
                self::LABEL => __(self::SHIPPING_LABELS),
                self::URL => $this->getUrl(self::PRINT_SHIPPING_METHOD,[self::ENTITY_ID=>$this->getRequest()->getParam(self::ENTITY_ID)])
            ]
        );
        return $this;
    }

    public function getRowUrl($rowData)
    {
        return $this->getUrl(self::ROW_URL, [self::SHIPMENT_ID => $rowData->getId(), self::COME_FROM => self::SHIPMENT]);
    }
}