<?php
namespace SM\Vendors\Controller\Adminhtml\Vendor;

class Delete extends \Magento\Backend\App\Action
{
    const VENDOR_ID = 'vendor_id';
    const VENDOR_MODEL = 'SM\Vendors\Model\Vendor';
    const REDIRECT_PATH = '*/*/';
    const EDIT = '*/*/edit';
    const VENDOR_SUCCESS = 'The vendor has been deleted.';
    const VENDOR_ERROR = 'We can\'t find a vendor to delete.';
    
	/**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
    	$vendorId = $this->getRequest()->getParam(self::VENDOR_ID);
    	$resultRedirect = $this->resultRedirectFactory->create();
    	if ($vendorId) {
    		try {
                // init model and delete
                $vendorModel = $this->_objectManager->create(self::VENDOR_MODEL);
                $vendorModel->load($vendorId);
                $vendorModel->delete();
                // display success message
                $this->messageManager->addSuccess(__(self::VENDOR_SUCCESS));
                return $resultRedirect->setPath(self::REDIRECT_PATH);
            } catch (\Exception $vendorException) {
                // display error message
                $this->messageManager->addError($vendorException->getMessage());
                // go back to edit form
                return $resultRedirect->setPath(self::EDIT, [self::VENDOR_ID => $vendorId]);
            }
    	}
    	// display error message
        $this->messageManager->addError(__(self::VENDOR_ERROR));
        // go to grid
        return $resultRedirect->setPath(self::REDIRECT_PATH);
    }
}