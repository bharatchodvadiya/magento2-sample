<?php
namespace SM\Vendors\Controller\Adminhtml\Shipping;

use SM\Vendors\Controller\Adminhtml\Shipping;
 
class Grid extends Shipping
{
   /**
     * @return void
     */
   public function execute()
   {
      return $this->_resultPageFactory->create();
   }
}