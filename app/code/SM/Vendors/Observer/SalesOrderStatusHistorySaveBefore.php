<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class SalesOrderStatusHistorySaveBefore implements ObserverInterface
{
	const VENDOR_ID = 'vendor_id';
	
	protected $_helperData;

	/**
    * @param \SM\Vendors\Helper\Data $helperData
    */
	public function __construct(
        \SM\Vendors\Helper\Data $helperData
    ) {
        $this->_helperData = $helperData;
    }

	public function execute(Observer $observer)
    {
    	if($vendorData = $this->_helperData->getVendorLogin()) {
    		$historyItem = $observer->getEvent()->getDataObject();
	        if($historyItem->getId() === null) {
	            $historyItem->setVendorId($vendorData[self::VENDOR_ID]);
	        }
    	}
    }
}