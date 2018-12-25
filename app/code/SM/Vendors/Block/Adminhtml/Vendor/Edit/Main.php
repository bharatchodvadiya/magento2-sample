<?php
namespace SM\Vendors\Block\Adminhtml\Vendor\Edit\Tab;

/**
 * Vendor manager edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const INIT_VALUE = 0;
    const FIRST_VALUE = 1;
    const NULL_VALUE = "";
    const DASH_VALUE = '-';
    const VENDOR_LABEL = 'Vendor Information';
    const VENDORS_VENDOR = 'vendors_vendor';
    const VENDOR_MAIN = 'vendor_main_';
    const BASE_FIELDSET = 'base_fieldset';
    const LEGEND = 'legend';
    const VENDOR_ID = 'vendor_id';
    const HIDDEN = 'hidden';
    const NAME = 'name';
    const IMAGE_ATTRIBUTE = '<img style="width:300px" src="';
    const PUB_MEDIA = 'pub/media/';
    const SLASH = '"/>';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_storeManager;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $contextData
     * @param \Magento\Framework\Registry $registryData
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $formData
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $contextData,
        \Magento\Framework\Registry $registryData,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $formData = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_storeManager = $storeManager;
        parent::__construct($contextData, $registryData, $formFactory, $formData);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $vendorModel = $this->_coreRegistry->registry(self::VENDORS_VENDOR);
        $vendorForm = $this->_formFactory->create();
        $vendorForm->setHtmlIdPrefix(self::VENDOR_MAIN);
        $fieldsetData = $vendorForm->addFieldset(self::BASE_FIELDSET, [self::LEGEND => __(self::VENDOR_LABEL)]);
        if ($vendorModel->getId()) {
            $fieldsetData->addField(self::VENDOR_ID, self::HIDDEN, [self::NAME => self::VENDOR_ID]);
        }
        if($vendorModel->getVendorSlug() < self::INIT_VALUE || $vendorModel->getVendorSlug() == self::NULL_VALUE) {
            $vendorRandomSlug = mt_rand().self::DASH_VALUE;
            $vendorModel->setVendorSlug($vendorRandomSlug);
        }
        $fieldsetData->addField(
            'vendor_status',
            'select',
            [
                'name' => 'vendor_status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'options'   => [
                    self::FIRST_VALUE => __('Active'),
                    self::INIT_VALUE => __('Inactive')
                ]
            ]
        );
        $fieldsetData->addField(
            'vendor_name',
            'text',
            [
                'name' => 'vendor_name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => false
            ]
        );
        $fieldsetData->addField(
            'vendor_slug',
            'text',
            [
                'name' => 'vendor_slug',
                'label' => __('URL Slug'),
                'title' => __('URL Slug'),
                'unique' => true,
                'required' => true,
                'disabled' => false
            ]
        );
        $fieldsetData->addField(
            'vendor_prefix',
            'text',
            [
                'name' => 'vendor_prefix',
                'label' => __('Prefix'),
                'title' => __('Prefix')
            ]
        );
        $fieldsetData->addField(
            'vendor_domain',
            'hidden',
            [
                'name' => 'vendor_domain',
                'label' => __('Domain'),
                'title' => __('Domain'),
                'unique' => true
            ]
        );
        $fieldsetData->addField(
            'vendor_commission',
            'text',
            [
                'name' => 'vendor_commission',
                'label' => __('Commission'),
                'title' => __('Commission')
            ]
        );
        $fieldsetData->addField(
            'vendor_shipping_methods',
            'multiselect',
            [
                'name' => 'vendor_shipping_methods',
                'label' => __('Shipping Method'),
                'title' => __('Shipping Method'),
                'required' => true,
                'values' => [
                    '1' => [
                        'value'=>'flatrate',
                        'label' => 'Flat Rate'
                    ],
                    '2' => [
                        'value'=>'vendorflatrate',
                        'label' => 'Vendor Flat Rate'
                    ]
                ]
            ]
        );
        $fieldsetData->addField(
            'vendor_contact_email',
            'text',
            [
                'name' => 'vendor_contact_email',
                'label' => __('Contact Email'),
                'title' => __('Contact Email')
            ]
        );
        $bigImage = self::NULL_VALUE;
        $mediaBaseUrl = $this->_storeManager->getStore()->getBaseUrl();
        if($vendorModel->getVendorLogo() != self::NULL_VALUE) {
            $bigImage = self::IMAGE_ATTRIBUTE.$mediaBaseUrl.self::PUB_MEDIA.$vendorModel->getVendorLogo().self::SLASH;
        }
        $fieldsetData->addField(
            'vendor_logo',
            'image',
            [
                'name' => 'vendor_logo',
                'label' => __('Logo'),
                'title' => __('Logo'),
                'after_element_html' => $bigImage
            ]
        );
        $fieldsetData->addField(
            'vendor_sale_postcodes',
            'hidden',
            [
                'name' => 'vendor_sale_postcodes',
                'label' => __('Sales postcodes'),
                'title' => __('Sales postcodes')
            ]
        );
        $fieldsetData->addField(
            'vendor_delivery_areas',
            'hidden',
            [
                'name' => 'vendor_delivery_areas',
                'label' => __('Delivery Area'),
                'title' => __('Delivery Area')
            ]
        );
        $vendorForm->setValues($vendorModel->getData());
        $this->setForm($vendorForm);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __(self::VENDOR_LABEL);
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __(self::VENDOR_LABEL);
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