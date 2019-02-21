<?php
namespace SM\Vendors\Block\Adminhtml\Banner;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	const ENABLED  = 1;
    const DISABLED = 0;
    const BANNER_GRID = 'bannerGrid';
    const DEFAULT_SORT = 'creation_time';
    const DEFAULT_DIR = 'DESC';
    const VENDOR_TABLE = 'magento_sm_vendor';
    const VENDOR_NAME = 'vendor_name';
    const VENDOR = 'vendor';
    const MAIN_TABLE = 'main_table.vendor_id = vendor.vendor_id';
    const BANNER_ID = 'id';
    const BANNER_IDS = 'banner_ids';
    const DELETE = 'delete';
    const LABEL = 'label';
    const DELETE_BANNER = 'Delete Banner';
    const URL = 'url';
    const MASS_DELETE = 'vendors/banner/massDelete';
    const CONFIRM = 'confirm';
    const CONFIRM_DELETE = 'Are you sure?';
    const VENDOR_ID = 'vendor_id';
    const VENDOR_BANNER_TABLE = 'main_table.vendor_id=?';
    const EDIT_URL = '*/*/edit';

	/**
     * @var \SM\Vendors\Model\ResourceModel\Banner\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \SM\Vendors\Model\Banner
     */
    protected $_bannerData;

    protected $_helperData;

     /**
     * @param \Magento\Backend\Block\Template\Context $contextData
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \SM\Vendors\Model\Banner $bannerData
     * @param \SM\Vendors\Model\ResourceModel\Banner\CollectionFactory $collectionFactory
     * @param \SM\Vendors\Helper\Data $helperData
     * @param array $gridData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $contextData,
        \Magento\Backend\Helper\Data $backendHelper,
        \SM\Vendors\Model\Banner $bannerData,
        \SM\Vendors\Model\ResourceModel\Banner\CollectionFactory $collectionFactory,
        \SM\Vendors\Helper\Data $helperData,
        array $gridData = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_bannerData = $bannerData;
        $this->_helperData = $helperData;
        parent::__construct($contextData, $backendHelper, $gridData);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId(self::BANNER_GRID);
        $this->setDefaultSort(self::DEFAULT_SORT);
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
            $collectionData->getSelect()->where(self::VENDOR_BANNER_TABLE, $vendorData[self::VENDOR_ID]);
        }
        else {
            $collectionData->getSelect()
                ->join([self::VENDOR => self::VENDOR_TABLE], self::MAIN_TABLE, [self::VENDOR_NAME => self::VENDOR_NAME]);
        }
        /* @var $collection \SM\Vendors\Model\ResourceModel\Banner\Collection */
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
    	$this->addColumn('id', [
            'header'    => __('ID'),
            'index'     => 'id',
        ]);

        $this->addColumn('image', [
            'header'    => __('Banner'),
            'index'     => 'image',
            'renderer'  => 'SM\Vendors\Block\Adminhtml\Banner\Grid\Renderer\Image'
        ]);

        $this->addColumn('title', [
            'header'    => __('Title'),
            'index'     => 'title',
        ]);

        if(!($vendorData = $this->_helperData->getVendorLogin())) {
            $this->addColumn('vendor_name', [
                'header'    => __('Vendor'),
                'index'     => 'vendor_name',
            ]);
        }

        $this->addColumn('active', [
            'header'    => __('Status'),
            'type' => 'options',
            'index'     => 'active',
            'options'   => [
                self::ENABLED => __('Enabled'),
                self::DISABLED => __('Disabled')
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
                        ],
                        'field' => 'id'
                    ],
                    [
                        'caption' => __('Delete'),
                        'url' => [
                            'base' => '*/*/delete',
                        ],
                        'field' => 'id'
                    ],
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField(self::BANNER_ID);
        $this->getMassactionBlock()->setFormFieldName(self::BANNER_IDS);
        $this->getMassactionBlock()->setUseSelectAll(true);
        $this->getMassactionBlock()->addItem(
            self::DELETE,
            [
                self::LABEL => __(self::DELETE_BANNER),
                self::URL => $this->getUrl(self::MASS_DELETE,[self::BANNER_ID=>$this->getRequest()->getParam(self::BANNER_ID)]),
                self::CONFIRM => __(self::CONFIRM_DELETE)
            ]
        );
        return $this;
    }

    public function getRowUrl($rowData)
    {
        return $this->getUrl(self::EDIT_URL, array(self::BANNER_ID => $rowData->getId()));
    }
}