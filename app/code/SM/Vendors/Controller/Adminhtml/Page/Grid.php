<?php
namespace SM\Vendors\Controller\Adminhtml\Page;

use SM\Vendors\Controller\Adminhtml\Page;
 
class Grid extends Page
{
    /**
     * @return void
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}