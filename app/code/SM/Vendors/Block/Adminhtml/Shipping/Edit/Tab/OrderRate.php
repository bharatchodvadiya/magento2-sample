<?php
namespace SM\Vendors\Block\Adminhtml\Shipping\Edit\Tab;

/**
 * Vendor shipping method edit form flatrate tab
 */
class OrderRate extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const VENDORS_SHIPPING = 'vendors_shipping';
    const SHIPPING_ORDER = 'shipping_order_';
    const FLATRATE_CONFIG_FORM = 'flatrate_config_form';
    const LEGEND = 'legend';
    const ORDER_RATE_CONFIG = 'Order Rate Config';
    const ORDER_RATE_LABEL = 'Vendor Order Rate';
    const ATTRIBUTE_CLASS = 'class';
    const FIELDSET_WIDE = 'fieldset-wide';
    const ORDERRATE_SHIPPING_ACTIVE = 'orderrate_shipping_active';
    const FIRST_VALUE = '1';
    const RATES = 'rates';

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    protected $_orderrateCollection;
    
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
        \SM\Vendors\Model\Shipping\OrderRate $orderrateCollection,
        \Magento\Store\Model\System\Store $systemStore,
        array $formData = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_orderrateCollection = $orderrateCollection;
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
        $orderForm = $this->_formFactory->create();
        $orderForm->setHtmlIdPrefix(self::SHIPPING_ORDER);
        $fieldsetData = $orderForm->addFieldset(self::FLATRATE_CONFIG_FORM, [self::LEGEND => __(self::ORDER_RATE_CONFIG), self::ATTRIBUTE_CLASS => self::FIELDSET_WIDE]);
        $vendorId = $vendorModel->getId();
        $shippingOrderModel = $this->_orderrateCollection->loadOrderShipping($vendorId);
        $shippingRates = $this->_orderrateCollection->getRates($shippingOrderModel[self::RATES]);
        $fieldsetData->addType('orderrate_config','SM\Vendors\Block\Adminhtml\Shipping\Edit\Tab\OrderRate\Element\Rate');
        $fieldsetData->addField(
            'flatrate_shipping_rate',
            'orderrate_config',
            [
                'name' => 'rates',
                'label' => __('Rate'),
                'title' => __('Rate'),
                'value' => $shippingRates,
                'required' => true,
                'disabled' => false
            ]
        );
        $fieldsetData->addField(
            'orderrate_shipping_active',
            'hidden',
            [
                'name' => 'orderrate[active]',
                'label' => __('Active'),
                'value' => '1'
            ]
        );
        $fieldsetData->addField(
            'config_id',
            'hidden',
            [
                'name' => 'orderrate[config_id]',
                'label' => __('Config Id'),
                'value' => $shippingOrderModel['config_id']
            ]
        );
        $vendorModel->setData(self::ORDERRATE_SHIPPING_ACTIVE, self::FIRST_VALUE);
        $this->setForm($orderForm);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __(self::ORDER_RATE_LABEL);
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __(self::ORDER_RATE_LABEL);
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
