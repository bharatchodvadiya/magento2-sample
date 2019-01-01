<?php
namespace SM\Vendors\Block\Adminhtml\Shipping;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	const ENABLED  = 1;
    const DISABLED = 0;
    const VENDORS_GRID = 'vendorsGrid';
    const VENDOR_ID = 'vendor_id';
    const DEFAULT_DIR = 'DESC';
    const EDIT_URL = '*/*/edit';
    const NULL_DATA = '';
    const VENDOR_PAGE_TABLE = 'main_table.vendor_id=?';

    /**
     * @var \SM\Vendors\Model\ResourceModel\Vendor\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \SM\Vendors\Model\Vendor
     */
    protected $_vendorData;

    protected $_helperData;

     /**
     * @param \Magento\Backend\Block\Template\Context $contextData
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \SM\Vendors\Model\Vendor $vendorData
     * @param \SM\Vendors\Model\ResourceModel\Vendor\CollectionFactory $collectionFactory
     * @param \SM\Vendors\Helper\Data $helperData
     * @param array $gridData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $contextData,
        \Magento\Backend\Helper\Data $backendHelper,
        \SM\Vendors\Model\Vendor $vendorData,
        \SM\Vendors\Model\ResourceModel\Vendor\CollectionFactory $collectionFactory,
        \SM\Vendors\Helper\Data $helperData,
        array $gridData = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_vendorData = $vendorData;
        $this->_helperData = $helperData;
        parent::__construct($contextData, $backendHelper, $gridData);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId(self::VENDORS_GRID);
        $this->setDefaultSort(self::VENDOR_ID);
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
            $collectionData->getSelect()->where(self::VENDOR_PAGE_TABLE, $vendorData[self::VENDOR_ID]);
        }
        /* @var $collectionData \SM\Vendors\Model\ResourceModel\Vendor\Collection */
        $this->setCollection($collectionData);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('vendor_id', [
            'header'    => __('ID'),
            'index'     => 'vendor_id',
        ]);

        $this->addColumn('vendor_prefix', [
            'header'    => __('Vendor Prefix'),
            'index'     => 'vendor_prefix',
        ]);

        $this->addColumn('vendor_logo', [
            'header'    => __('Logo'),
            'index'     => 'vendor_logo',
            'renderer'  => 'SM\Vendors\Block\Adminhtml\Banner\Grid\Renderer\Image',
            'sortable' => false,
        ]);

        if(!($vendorData = $this->_helperData->getVendorLogin())) {
            $this->addColumn('vendor_name', [
                'header'    => __('Vendor name'),
                'index'     => 'vendor_name',
            ]);
        }

        $this->addColumn('vendor_contact_email', [
            'header'    => __('Email'),
            'index'     => 'vendor_contact_email',
        ]);

        $this->addColumn('vendor_status', [
            'header'    => __('Vendor Status'),
            'type' => 'options',
            'index'     => 'vendor_status',
            'options'   => [
	            self::ENABLED => __('Active'),
	            self::DISABLED => __('Inactive')
	        ]
        ]);

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'vendor_id'
                    ]
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
        return parent::_prepareColumns();
    }

    public function getRowUrl($rowData)
    {
        if($vendorData = $this->_helperData->getVendorLogin()) {
            return self::NULL_DATA;
        }
        else {
            return $this->getUrl(self::EDIT_URL, [self::VENDOR_ID => $rowData->getVendorId()]);
        }
    }
}