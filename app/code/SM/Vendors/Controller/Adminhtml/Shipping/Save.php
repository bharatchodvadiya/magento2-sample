<?php
namespace SM\Vendors\Controller\Adminhtml\Shipping;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    const REDIRECT_PATH = '*/*/';
    const EXCEPTION_ERROR = 'An error occurred while saving this configuration:';
    const CARRIERS = 'carriers';
    const WEBSITE = 'website';
    const STORE = 'store';
    const VENDOR_ID = 'vendor_id';
    const CONFIGURATION_SAVED = 'The configuration has been saved.';
    const EDIT = '*/*/edit';
    const CURRENT = '_current';
    const SECTION = 'section';
    const CONFIG_STATE = 'config_state';
    const CONFIG_STATE_PARAM = 'configState';
    const FLATRATE_MODEL = 'SM\Vendors\Model\Shipping\FlatRate';
    const ORDERRATE_MODEL = 'SM\Vendors\Model\Shipping\OrderRate';
    const VENDOR_SESSION = 'Magento\Backend\Model\Session';
    const BACK = 'back';
    const ORDER_RATE = 'orderrate';
    const RATES = 'rates';
    const FLAT_RATE = 'flatrate';
    const CONFIG_ID = 'config_id';

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    /**
     * @param Action\Context $contextData
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     */
    public function __construct(
        Action\Context $contextData,
        \Magento\Backend\Model\Auth\Session $adminSession
    ) {
        $this->_adminSession = $adminSession;
        parent::__construct($contextData);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $formData = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($formData) {
            $vendorFlatModel = $this->_objectManager->create(self::FLATRATE_MODEL);
            $vendorOrderModel = $this->_objectManager->create(self::ORDERRATE_MODEL);
            $vendorId = $formData[self::VENDOR_ID];
            $flatConfigId = $formData[self::FLAT_RATE][self::CONFIG_ID];
            $orderConfigId = $formData[self::ORDER_RATE][self::CONFIG_ID];
            $serializeRates = serialize($formData[self::ORDER_RATE][self::RATES]);
            $formData[self::RATES] = $serializeRates;
            if ($flatConfigId) {
                $vendorFlatModel->load($flatConfigId);
            }
            if ($orderConfigId) {
                $vendorOrderModel->load($orderConfigId);
            }
            $vendorFlatModel->addData($formData);
            $vendorOrderModel->addData($formData);
            try {
                $sectionData = self::CARRIERS;
                $websiteData = $this->getRequest()->getParam(self::WEBSITE);
                $storeData   = $this->getRequest()->getParam(self::STORE);
                $vendorFlatModel->save();
                $vendorOrderModel->save();
                $this->messageManager->addSuccess(__(self::CONFIGURATION_SAVED));
                $this->_objectManager->get(self::VENDOR_SESSION)->setFormData(false);
                if ($this->getRequest()->getParam(self::BACK)) {
                    return $resultRedirect->setPath(self::EDIT, [self::VENDOR_ID => $vendorId, self::CURRENT => true]);
                }
                return $resultRedirect->setPath(self::REDIRECT_PATH);
            } catch (\Magento\Framework\Exception\LocalizedException $shippingException) {
                $this->messageManager->addError($shippingException->getMessage());
            } catch (\RuntimeException $shippingException) {
                $this->messageManager->addError($shippingException->getMessage());
            } catch (\Exception $shippingException) {
                $this->messageManager->addException($shippingException, __(self::EXCEPTION_ERROR));
            }
            $adminUser = $this->_adminSession->getUser();
            $configState = $this->getRequest()->getPost(self::CONFIG_STATE);
            if (is_array($configState)) {
                $adminExtra = $adminUser->getExtra();
                if (!is_array($adminExtra)) {
                    $adminExtra = array();
                }
                if (!isset($adminExtra[self::CONFIG_STATE_PARAM])) {
                    $adminExtra[self::CONFIG_STATE_PARAM] = array();
                }
                foreach ($configState as $fieldsetData => $stateData) {
                    $adminExtra[self::CONFIG_STATE_PARAM][$fieldsetData] = $stateData;
                }
                $adminUser->saveExtra($adminExtra);
            }
            $this->_getSession()->setFormData($formData);
            return $resultRedirect->setPath(self::EDIT, [self::CURRENT => [self::SECTION, self::WEBSITE, self::STORE, self::VENDOR_ID]]);
        }
        return $resultRedirect->setPath(self::REDIRECT_PATH);
    }
}