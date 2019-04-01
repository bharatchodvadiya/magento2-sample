<?php
namespace SM\Vendors\Controller\Adminhtml\Page;

use SM\Vendors\Controller\Adminhtml\Page;

class Edit extends Page
{
	const ACTIVE_MENU = 'SM_Vendors::top_vendors';
	const PAGE_ID = 'id';
    const PAGE_MODEL = 'SM\Vendors\Model\Page';
    const PAGE_ERROR = 'This page no longer exists.';
    const VENDOR_SESSION = 'Magento\Backend\Model\Session';
    const REDIRECT_PATH = '*/*/';
    const PAGE_REGISTRY = 'vendors_page';
    const NEW_PAGE = 'New Page';
    const PAGE = 'Page';

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
    	$pageId = $this->getRequest()->getParam(self::PAGE_ID);
        $pageModel = $this->_objectManager->create(self::PAGE_MODEL);
        //Initial checking
        if ($pageId) {
            $pageModel->load($pageId);
            if (!$pageModel->getId()) {
                $this->messageManager->addError(__(self::PAGE_ERROR));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath(self::REDIRECT_PATH);
            }
        }
        // Restore previously entered form data from session
        $pageData = $this->_objectManager->get(self::VENDOR_SESSION)->getFormData(true);
        if (!empty($pageData)) {
            $pageModel->setData($pageData);
        }
        $this->_coreRegistry->register(self::PAGE_REGISTRY, $pageModel);
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__(self::PAGE));
        $resultPage->getConfig()->getTitle()->prepend($pageModel->getId() ? $pageModel->getTitle() : __(self::NEW_PAGE));
        return $resultPage;
    }
}