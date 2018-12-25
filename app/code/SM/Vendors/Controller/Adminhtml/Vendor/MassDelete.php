<?php
namespace SM\Vendors\Controller\Adminhtml\Vendor;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{
    const VENDOR_IDS = 'vendor_ids';
    const SM_VENDOR_MODEL = 'SM\Vendors\Model\Vendor';
    const SUCCESS_MESSAGE = 'A total of %1 record(s) have been deleted.';
    const REDIRECT_BACK = '*/*/';

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $vendorIds = $this->getRequest()->getPost(self::VENDOR_IDS, array());
        $resultRedirect = $this->resultRedirectFactory->create();
        $vendorModel = $this->_objectManager->create(self::SM_VENDOR_MODEL);
        foreach ($vendorIds as $vendorId) {
            $vendorModel->setId($vendorId)->delete();
        }
        $this->messageManager->addSuccess(__(self::SUCCESS_MESSAGE, count($vendorIds)));
        return $resultRedirect->setPath(self::REDIRECT_BACK);
    }
}