<?php
namespace SM\Vendors\Controller\Adminhtml\Page;

use SM\Vendors\Controller\Adminhtml\Page;

class Index extends Page
{
	const ACTIVE_MENU = 'SM_Vendors::top_vendors';
    const PAGE_MANAGER = 'Page Manager';
    const AJAX = 'ajax';
    const GRID = 'grid';

	/**
     * Index action
     *
     * @return void
     */
    public function execute() {
      	if ($this->getRequest()->getQuery(self::AJAX)) {
            $this->_forward(self::GRID);
            return;
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu(self::ACTIVE_MENU);
        $resultPage->getConfig()->getTitle()->prepend(__(self::PAGE_MANAGER));
        return $resultPage;
    }
}
