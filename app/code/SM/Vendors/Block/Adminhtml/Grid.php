<?php
namespace SM\Vendors\Block\Adminhtml\Representative;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
	const ENABLED  = 1;
    const DISABLED = 0;
    const REPRESENTATIVE_GRID = 'representativeGrid';
    const DEFAULT_SORT = 'username';
    const DEFAULT_DIR = 'DESC';
    const VENDOR_TABLE = 'magento_sm_vendor';
    const VENDOR_NAME = 'vendor_name';
    const VENDOR = 'vendor';
    const MAIN_TABLE = 'main_table.vendor_id = vendor.vendor_id';
    const VENDOR_ID = 'vendor_id';
    const VENDOR_PAGE_TABLE = 'main_table.vendor_id=?';
    const EDIT_URL = '*/*/edit';
    const USER_ID = 'user_id';

    /**
     * @var \SM\Vendors\Model\ResourceModel\Representative\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \SM\Vendors\Model\Representative
     */
    protected $_representativeData;

    protected $_helperData;

     /**
     * @param \Magento\Backend\Block\Template\Context $contextData
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \SM\Vendors\Model\Representative $representativeData
     * @param \SM\Vendors\Model\ResourceModel\Representative\CollectionFactory $collectionFactory
     * @param \SM\Vendors\Helper\Data $helperData
     * @param array $gridData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $contextData,
        \Magento\Backend\Helper\Data $backendHelper,
        \SM\Vendors\Model\Representative $representativeData,
        \SM\Vendors\Model\ResourceModel\Representative\CollectionFactory $collectionFactory,
        \SM\Vendors\Helper\Data $helperData,
        array $gridData = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_representativeData = $representativeData;
        $this->_helperData = $helperData;
        parent::__construct($contextData, $backendHelper, $gridData);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId(self::REPRESENTATIVE_GRID);
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
            $collectionData->getSelect()->where(self::VENDOR_PAGE_TABLE, $vendorData[self::VENDOR_ID]);
        }
        else {
            $collectionData->getSelect()
                ->join([self::VENDOR => self::VENDOR_TABLE], self::MAIN_TABLE, [self::VENDOR_NAME => self::VENDOR_NAME]);
        }
        /* @var $collectionData \SM\Vendors\Model\ResourceModel\Page\Collection */
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
        $this->addColumn('user_id', [
            'header'    => __('ID'),
            'index'     => 'user_id',
            'sortable'  => true,
        ]);

        $this->addColumn('username', [
            'header'    => __('User Name'),
            'index'     => 'username',
        ]);

        $this->addColumn('firstname', [
            'header'    => __('First Name'),
            'index'     => 'firstname',
        ]);

        $this->addColumn('lastname', [
            'header'    => __('Last Name'),
            'index'     => 'lastname',
        ]);

        $this->addColumn('email', [
            'header'    => __('Email'),
            'index'     => 'email',
        ]);

        if(!($vendorData = $this->_helperData->getVendorLogin())) {
            $this->addColumn('vendor_name', [
                'header'    => __('Vendor'),
                'index'     => 'vendor_name',
            ]);
        }

        $this->addColumn('is_active', [
            'header'    => __('Status'),
            'type' => 'options',
            'index'     => 'is_active',
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
                        'field' => 'user_id'
                    ],
                    [
                        'caption' => __('Delete'),
                        'url' => [
                            'base' => '*/*/delete',
                            'params' => ['store' => $this->getRequest()->getParam('store')]
                        ],
                        'field' => 'user_id'
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

    public function getRowUrl($rowData)
    {
        return $this->getUrl(self::EDIT_URL, [self::USER_ID => $rowData->getId()]);
    }
}