<?php
namespace SM\Vendors\Controller\Adminhtml\Shipping;

use SM\Vendors\Controller\Adminhtml\Shipping;

class Edit extends Shipping
{
    const ACTIVE_MENU = 'SM_Vendors::top_vendors';
    const VENDOR_ID = 'vendor_id';
    const VENDOR_MODEL = 'SM\Vendors\Model\Vendor';
    const VENDOR_ERROR = 'This post no longer exists.';
    const VENDOR_SESSION = 'Magento\Backend\Model\Session';
    const REDIRECT_PATH = '*/*/';
    const SHIPPING_REGISTRY = 'vendors_shipping';

	/**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu(self::ACTIVE_MENU);
        return $resultPage;
    }

    public function execute()
    {
    	$vendorId = $this->getRequest()->getParam(self::VENDOR_ID);
        $vendorModel = $this->_objectManager->create(self::VENDOR_MODEL);
        //Initial checking
        if ($vendorId) {
            $vendorModel->load($vendorId);
            if (!$vendorModel->getId()) {
                $this->messageManager->addError(__(self::VENDOR_ERROR));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath(self::REDIRECT_PATH);
            }
        }
        // Restore previously entered form data from session
        $vendorData = $this->_objectManager->get(self::VENDOR_SESSION)->getFormData(true);
        if (!empty($vendorData)) {
            $vendorModel->setData($vendorData);
        }
        $this->_coreRegistry->register(self::SHIPPING_REGISTRY, $vendorModel);
        $resultPage = $this->_initAction();
        if($vendorModel->getId()) {
            $resultPage->getConfig()->getTitle()->prepend($vendorModel->getTitle());
        }
        return $resultPage;
    }
}