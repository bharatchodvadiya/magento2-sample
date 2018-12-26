<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class CustomerGroupLoadAfter implements ObserverInterface
{
	const POSITION = 2;
	const HASH_CODE = '::';

	public function execute(Observer $observer)
    {
    	$customerGroup = $observer->getObject();
	    if($groupCode = $customerGroup->getCode()) {
    	    if(($position = strpos($groupCode, self::HASH_CODE)) !== false) {
    	        $groupCode = substr($groupCode, $position+self::POSITION);
        	    $customerGroup->setCode($groupCode);	
    	    }
	    }
    }
}