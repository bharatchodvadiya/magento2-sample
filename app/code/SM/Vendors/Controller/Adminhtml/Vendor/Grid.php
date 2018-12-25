<?php
namespace SM\Vendors\Controller\Adminhtml\Vendor;
use SM\Vendors\Controller\Adminhtml\Vendor;
 
class Grid extends Vendor
{
   /**
     * @return void
     */
   public function execute()
   {
      return $this->_resultPageFactory->create();
   }
}