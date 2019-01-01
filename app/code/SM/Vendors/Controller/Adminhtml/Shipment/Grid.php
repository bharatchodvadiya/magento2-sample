<?php
namespace SM\Vendors\Controller\Adminhtml\Shipment;

use SM\Vendors\Controller\Adminhtml\Shipment;

class Grid extends Shipment
{
    /**
     * @return void
     */
    public function execute()
    {
        return $this->_resultPageFactory->create();
    }
}