<?php
namespace SM\Vendors\Controller\Adminhtml\Representative;

use SM\Vendors\Controller\Adminhtml\Representative;
 
class Grid extends Representative
{
    /**
     * @return void
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}