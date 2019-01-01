<?php
namespace SM\Vendors\Controller\Adminhtml\Representative;

use SM\Vendors\Controller\Adminhtml\Representative;

class Edit extends Representative
{
	const ACTIVE_MENU = 'SM_Vendors::top_vendors';
	const USER_ID = 'user_id';
    const REPRESENTATIVE_MODEL = 'SM\Vendors\Model\Representative';
    const USER_ERROR = 'This user no longer exists.';
    const VENDOR_SESSION = 'Magento\Backend\Model\Session';
    const REDIRECT_PATH = '*/*/';
    const REPRESENTATIVE_REGISTRY = 'vendors_representative';
    const NEW_REPRESENTATIVE = 'New Representative';
    const REPRESENTATIVE = 'Representative';

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
    	$userId = $this->getRequest()->getParam(self::USER_ID);
        $representativeModel = $this->_objectManager->create(self::REPRESENTATIVE_MODEL);
        //Initial checking
        if ($userId) {
            $representativeModel->load($userId);
            if (!$representativeModel->getId()) {
                $this->messageManager->addError(__(self::USER_ERROR));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath(self::REDIRECT_PATH);
            }
        }
        // Restore previously entered form data from session
        $userData = $this->_objectManager->get(self::VENDOR_SESSION)->getFormData(true);
        if (!empty($userData)) {
            $representativeModel->setData($userData);
        }
        $this->_coreRegistry->register(self::REPRESENTATIVE_REGISTRY, $representativeModel);
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__(self::REPRESENTATIVE));
        $resultPage->getConfig()->getTitle()->prepend($representativeModel->getId() ? $representativeModel->getTitle() : __(self::NEW_REPRESENTATIVE));
        return $resultPage;
    }
}