<?php
namespace SM\Vendors\Controller\Adminhtml\Page;

use Magento\Backend\App\Action;

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action
{
    const PAGE_IDS = 'page_ids';
    const PAGE_MODEL = 'SM\Vendors\Model\Page';
    const SUCCESS_MESSAGE = 'A total of %1 record(s) have been deleted.';
    const REDIRECT_BACK = '*/*/';

    /**
     * Execute mass delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $pageIds = $this->getRequest()->getPost(self::PAGE_IDS, array());
        $resultRedirect = $this->resultRedirectFactory->create();
        $pageModel = $this->_objectManager->create(self::PAGE_MODEL);
        foreach ($pageIds as $pageId) {
            $pageModel->setId($pageId)->delete();
        }
        $this->messageManager->addSuccess(__(self::SUCCESS_MESSAGE, count($pageIds)));
        return $resultRedirect->setPath(self::REDIRECT_BACK);
    }
}