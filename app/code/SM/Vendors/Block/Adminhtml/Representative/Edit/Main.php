<?php
namespace SM\Vendors\Block\Adminhtml\Representative\Edit\Tab;

use SM\Vendors\Model\ResourceModel\Vendor\Collection;

/**
 * Representative edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const REPRESENTATIVE_LABEL = 'Representative Information';
    const VENDORS_REPRESENTATIVE = 'vendors_representative';
    const REPRESENTATIVE_MAIN = 'user_';
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
    const FIELD_ID = 'user_id';
    const PASSWORD = 'password';
    const IS_ACTIVE = 'is_active';
    const NULL_DATA = '';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_vendorCollection;

    protected $_helperData;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $contextData
     * @param \Magento\Framework\Registry $registryData
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param \SM\Vendors\Model\VendorFactory $vendorCollection
     * @param \SM\Vendors\Helper\Data $helperData
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param array $formData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $contextData,
        \Magento\Framework\Registry $registryData,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \SM\Vendors\Model\VendorFactory $vendorCollection,
        \SM\Vendors\Helper\Data $helperData,
        \Magento\Backend\Model\Auth\Session $adminSession,
        array $formData = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_vendorCollection = $vendorCollection;
        $this->_helperData = $helperData;
        $this->_adminSession = $adminSession;
        parent::__construct($contextData, $registryData, $formFactory, $formData);
    }

    /**
     * Prepare representative form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $representativeModel = $this->_coreRegistry->registry(self::VENDORS_REPRESENTATIVE);
        $representativeForm = $this->_formFactory->create();
        $representativeForm->setHtmlIdPrefix(self::REPRESENTATIVE_MAIN);
        $fieldsetData = $representativeForm->addFieldset(self::BASE_FIELDSET, [self::LEGEND => __(self::REPRESENTATIVE_LABEL)]);
        if ($representativeModel->getUserId()) {
            $fieldsetData->addField(self::FIELD_ID, self::HIDDEN, [self::NAME => self::FIELD_ID]);
        }
        else {
            if (!$representativeModel->hasData(self::IS_ACTIVE)) {
                $representativeModel->setIsActive(self::INDEX_FIRST);
            }
        }
        
        $fieldsetData->addField(
            'username',
            'text',
            [
                'name' => 'username',
                'label' => __('User Name'),
                'title' => __('User Name'),
                'required' => true,
                'disabled' => false
            ]
        );
        $fieldsetData->addField(
            'firstname',
            'text',
            [
                'name' => 'firstname',
                'label' => __('First Name'),
                'title' => __('First Name'),
                'required' => true,
                'disabled' => false
            ]
        );
        $fieldsetData->addField(
            'lastname',
            'text',
            [
                'name' => 'lastname',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
                'required' => true,
                'disabled' => false
            ]
        );
        $fieldsetData->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
                'class' => 'validate-email',
                'disabled' => false
            ]
        );

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
        $vendorId = self::NULL_DATA;
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

        if ($representativeModel->getUserId()) {
            $fieldsetData->addField(
                'password',
                'password',
                [
                    'name' => 'new_password',
                    'label' => __('New Password'),
                    'id' => 'new_pass',
                    'title' => __('New Password'),
                    'class' => 'validate-admin-password',
                ]
            );
            $fieldsetData->addField(
                'confirmation',
                'password',
                [
                    'name' => 'password_confirmation',
                    'label' => __('Password Confirmation'),
                    'id' => 'confirmation',
                    'title' => __('Password Confirmation'),
                    'class' => 'validate-cpassword',
                ]
            );
        }
        else {
            $fieldsetData->addField(
                'password',
                'password',
                [
                    'name' => 'password',
                    'label' => __('Password'),
                    'id' => 'customer_pass',
                    'title' => __('Password'),
                    'class' => 'validate-admin-password',
                    'required' => true,
                ]
            );
            $fieldsetData->addField(
                'confirmation',
                'password',
                [
                    'name' => 'password_confirmation',
                    'label' => __('Password Confirmation'),
                    'id' => 'confirmation',
                    'title' => __('Password Confirmation'),
                    'class' => 'validate-cpassword',
                    'required' => true,
                ]
            );
        }
        if($this->_adminSession->getUser()->getId() != $representativeModel->getUserId()) {
            $fieldsetData->addField(
                'is_active',
                'select',
                [
                    'name' => 'is_active',
                    'label' => __('This account is'),
                    'title' => __('Account Status'),
                    'options'   => [
                        self::INDEX_FIRST => __('Active'),
                        self::INDEX_INIT => __('Inactive')
                    ]
                ]
            );
        }
        $fieldsetData->addField(
            'user_roles',
            'hidden',
            [
                'name' => 'user_roles',
                'id' => '_user_roles'
            ]
        );
        if (!$representativeModel->getId()) {
            if(!empty($vendorId)){
                $representativeModel->setData(self::VENDOR_ID, $vendorId);
            }
        }
        $representativeData = $representativeModel->getData();
        unset($representativeData[self::PASSWORD]);

        $representativeForm->setValues($representativeModel->getData());
        $this->setForm($representativeForm);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __(self::REPRESENTATIVE_LABEL);
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __(self::REPRESENTATIVE_LABEL);
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