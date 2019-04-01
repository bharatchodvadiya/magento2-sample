<?php
namespace SM\Vendors\Controller\Adminhtml\Page;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    const VENDOR_SESSION = 'Magento\Backend\Model\Session';
    const REDIRECT_PATH = '*/*/';
    const PAGE_ID = 'id';
    const PAGE_MODEL = 'SM\Vendors\Model\Page';
    const PAGE_ERROR = 'Page was successfully saved';
    const EXCEPTION_ERROR = 'Something went wrong while saving the page.';
    const EDIT = '*/*/edit';
    const CURRENT = '_current';
    const BACK = 'back';

	/**
     * @param Action\Context $contextData
     */
    public function __construct(Action\Context $contextData)
    {
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
            $pageModel = $this->_objectManager->create(self::PAGE_MODEL);
            $pageId = $this->getRequest()->getParam(self::PAGE_ID);
            if ($pageId) {
                $pageModel->load($pageId);
            }
            $pageModel->addData($formData);
            try {
                $pageModel->save();
                $this->messageManager->addSuccess(__(self::PAGE_ERROR));
                $this->_objectManager->get(self::VENDOR_SESSION)->setFormData(false);
                if ($this->getRequest()->getParam(self::BACK)) {
                    return $resultRedirect->setPath(self::EDIT, [self::PAGE_ID => $pageModel->getId(), self::CURRENT => true]);
                }
                return $resultRedirect->setPath(self::REDIRECT_PATH);
            } catch (\Magento\Framework\Exception\LocalizedException $vendorException) {
                $this->messageManager->addError($vendorException->getMessage());
            } catch (\RuntimeException $vendorException) {
                $this->messageManager->addError($vendorException->getMessage());
            } catch (\Exception $vendorException) {
                $this->messageManager->addException($vendorException, __(self::EXCEPTION_ERROR));
            }
            $this->_getSession()->setFormData($formData);
            return $resultRedirect->setPath(self::EDIT, [self::PAGE_ID => $this->getRequest()->getParam(self::PAGE_ID)]);
        }
        return $resultRedirect->setPath(self::REDIRECT_PATH);
    }
}