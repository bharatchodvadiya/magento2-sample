<?php
namespace SM\Vendors\Controller\Adminhtml\Vendor;

class Profile extends \Magento\Backend\App\Action
{
    const EDIT = 'edit';
    const ALLOWED_SM_VENDORS = 'SM_Vendors::vendors_profile';
    const SM_HELPER = 'SM\Vendors\Helper\Data';
    const VENDOR_ID = 'vendor_id';
    const VENDORS_EDIT = '*/*/edit/';
    const VENDOR_SLASH = 'vendor_id/';

    /**
     * Forward to edit
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $vendorHelper = $this->_objectManager->get(self::SM_HELPER);
        $vendorData = $vendorHelper->getVendorLogin();
        if($vendorData) {
            $vendorId = $vendorData[self::VENDOR_ID];
            return $resultRedirect->setPath(self::VENDORS_EDIT.self::VENDOR_SLASH.$vendorId);
        }
        else {
            return $resultRedirect->setPath(self::VENDORS_EDIT);
        }
    }

    /**
     * Vendor access rights checking
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ALLOWED_SM_VENDORS);
    }
}