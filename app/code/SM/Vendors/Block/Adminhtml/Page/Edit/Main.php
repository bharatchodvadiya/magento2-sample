<?php
namespace SM\Vendors\Block\Adminhtml\Page\Edit\Tab;

use SM\Vendors\Model\ResourceModel\Vendor\Collection;

/**
 * Vendor page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const PAGE_LABEL = 'Page Information';
    const VENDORS_PAGE = 'vendors_page';
    const PAGE_MAIN = 'page_';
    const BASE_FIELDSET = 'base_fieldset';
    const LEGEND = 'legend';
    const INDEX_INIT = 0;
    const INDEX_FIRST = 1;
    const VALUE = 'value';
    const VENDOR_ID = 'vendor_id';
    const LABEL = 'label';
    const VENDOR_NAME = 'vendor_name';
    const HIDDEN = 'hidden';
    const NAME = 'name';
    const FIELD_ID = 'id';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_vendorCollection;

    protected $_helperData;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $contextData
     * @param \Magento\Framework\Registry $registryData
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \SM\Vendors\Model\VendorFactory $vendorCollection
     * @param \SM\Vendors\Helper\Data $helperData
     * @param array $formData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $contextData,
        \Magento\Framework\Registry $registryData,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \SM\Vendors\Model\VendorFactory $vendorCollection,
        \SM\Vendors\Helper\Data $helperData,
        array $formData = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_vendorCollection = $vendorCollection;
        $this->_helperData = $helperData;
        parent::__construct($contextData, $registryData, $formFactory, $formData);
    }

    /**
     * Prepare page form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $pageModel = $this->_coreRegistry->registry(self::VENDORS_PAGE);
        $pageForm = $this->_formFactory->create();
        $pageForm->setHtmlIdPrefix(self::PAGE_MAIN);
        $fieldsetData = $pageForm->addFieldset(self::BASE_FIELDSET, [self::LEGEND => __(self::PAGE_LABEL)]);
        if ($pageModel->getId()) {
            $fieldsetData->addField(self::FIELD_ID, self::HIDDEN, [self::NAME => self::FIELD_ID]);
        }
        $vendorLogin = $this->_helperData->getVendorLogin();

        $vendorModel = $this->_vendorCollection->create();
        $vendorData = $vendorModel->getCollection()->getData();
        $vendorOption = [];
        $indexData = self::INDEX_INIT;
        foreach($vendorData as $itemData) {
            $vendorOption[$indexData][self::VALUE] = $itemData[self::VENDOR_ID];
            $vendorOption[$indexData][self::LABEL] = $itemData[self::VENDOR_NAME];
            $indexData = $indexData + self::INDEX_FIRST;
        }
        if($vendorLogin) {
            $fieldsetData->addField(self::VENDOR_ID, self::HIDDEN, [self::NAME => self::VENDOR_ID]);
            $vendorId = $vendorLogin[self::VENDOR_ID];
        }
        else {
            $fieldsetData->addField(
                'vendor_id',
                'select',
                [
                    'name' => 'vendor_id',
                    'label' => __('Vendor'),
                    'title' => __('Vendor'),
                    'required' => true,
                    'values' => $vendorOption
                ]
            );
        }
        $fieldsetData->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Page Title'),
                'title' => __('Page Title'),
                'required' => true,
                'disabled' => false
            ]
        );
        $fieldsetData->addField(
            'identifier',
            'text',
            [
                'name' => 'identifier',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
                'required' => true,
                'note' => __('Relative to Website Base URL'),
                'disabled' => false
            ]
        );
        $fieldsetData->addField(
            'active',
            'select',
            [
                'name' => 'active',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'options'   => [
                    self::INDEX_FIRST => __('Active'),
                    self::INDEX_INIT => __('Inactive')
                ]
            ]
        );
        if (!$pageModel->getId()) {
            if(!empty($vendorId)) {
                $pageModel->setData(self::VENDOR_ID, $vendorId);
            }
        }
        $pageForm->setValues($pageModel->getData());
        $this->setForm($pageForm);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __(self::PAGE_LABEL);
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __(self::PAGE_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}