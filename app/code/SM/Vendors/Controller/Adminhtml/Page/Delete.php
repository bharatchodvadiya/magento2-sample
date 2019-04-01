<?php
namespace SM\Vendors\Controller\Adminhtml\Page;

class Delete extends \Magento\Backend\App\Action
{
    const PAGE_ID = 'id';
    const PAGE_MODEL = 'SM\Vendors\Model\Page';
    const REDIRECT_PATH = '*/*/';
    const EDIT = '*/*/edit';
    const PAGE_SUCCESS = 'The page has been deleted.';
    const PAGE_ERROR = 'We can\'t find a page to delete.';

	/**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
    	$pageId = $this->getRequest()->getParam(self::PAGE_ID);
    	$resultRedirect = $this->resultRedirectFactory->create();
    	if ($pageId) {
    		try {
                // init model and delete
                $pageModel = $this->_objectManager->create(self::PAGE_MODEL);
                $pageModel->load($pageId);
                $pageModel->delete();
                // display success message
                $this->messageManager->addSuccess(__(self::PAGE_SUCCESS));
                return $resultRedirect->setPath(self::REDIRECT_PATH);
            } catch (\Exception $pageException) {
                // display error message
                $this->messageManager->addError($pageException->getMessage());
                // go back to edit form
                return $resultRedirect->setPath(self::EDIT, [self::PAGE_ID => $pageId]);
            }
    	}
    	// display error message
        $this->messageManager->addError(__(self::PAGE_ERROR));
        // go to grid
        return $resultRedirect->setPath(self::REDIRECT_PATH);
    }
}