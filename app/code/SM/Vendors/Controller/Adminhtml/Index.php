<?php
namespace SM\Vendors\Controller\Adminhtml\Representative;

use SM\Vendors\Controller\Adminhtml\Representative;

class Index extends Representative
{
	const ACTIVE_MENU = 'SM_Vendors::top_vendors';
    const REPRESENTATIVE_MANAGER = 'Representative Manager';
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
        $resultPage->getConfig()->getTitle()->prepend(__(self::REPRESENTATIVE_MANAGER));
        return $resultPage;
    }
}