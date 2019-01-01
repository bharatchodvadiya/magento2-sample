<?php
namespace SM\Vendors\Block\Adminhtml\Shipping\Edit\Tab;

/**
 * Vendor shipping method edit form flatrate tab
 */
class FlatRate extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const VENDORS_SHIPPING = 'vendors_shipping';
    const SHIPPING_MAIN = 'shipping_main_';
    const FLATRATE_CONFIG_FORM = 'flatrate_config_form';
    const LEGEND = 'legend';
    const FLATRATE_CONFIG = 'Flatrate Config';
    const FLATRATE_LABEL = 'Vendor Flat Rate';
    const FLATRATE_SHIPPING_ACTIVE = 'flatrate_shipping_active';
    const FIRST_VALUE = '1';
    const PRICE = 'price';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_shippingCollection;
    
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
        \SM\Vendors\Model\Shipping\FlatRate $shippingCollection,
        \Magento\Store\Model\System\Store $systemStore,
        array $formData = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_shippingCollection = $shippingCollection;
        parent::__construct($contextData, $registryData, $formFactory, $formData);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $vendorModel = $this->_coreRegistry->registry(self::VENDORS_SHIPPING);
        $shippingForm = $this->_formFactory->create();
        $shippingForm->setHtmlIdPrefix(self::SHIPPING_MAIN);
        $fieldsetData = $shippingForm->addFieldset(self::FLATRATE_CONFIG_FORM, [self::LEGEND => __(self::FLATRATE_CONFIG)]);
        $vendorId = $vendorModel->getId();
        $shippingModel = $this->_shippingCollection->loadFlatShipping($vendorId);
        $fieldsetData->addField(
            'flatrate_shipping_price',
            'text',
            [
                'name' => 'price',
                'label' => __('Price'),
                'title' => __('Price'),
                'value' => $shippingModel[self::PRICE],
                'required' => true,
                'disabled' => false
            ]
        );
        $fieldsetData->addField(
            'flatrate_shipping_active',
            'hidden',
            [
                'name' => 'active',
                'label' => __('Active'),
                'value' => '1'
            ]
        );
        $fieldsetData->addField(
            'vendor_id',
            'hidden',
            [
                'name' => 'vendor_id',
                'label' => __('Vendor Id'),
                'value' => $vendorModel->getId()
            ]
        );
        $fieldsetData->addField(
            'config_id',
            'hidden',
            [
                'name' => 'flatrate[config_id]',
                'label' => __('Config Id'),
                'value' => $shippingModel['config_id']
            ]
        );
        $vendorModel->setData(self::FLATRATE_SHIPPING_ACTIVE, self::FIRST_VALUE);
        $this->setForm($shippingForm);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __(self::FLATRATE_LABEL);
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __(self::FLATRATE_LABEL);
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