<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class CustomerGroupSaveBefore implements ObserverInterface
{
	const VENDOR_ID = 'vendor_id';
	const INIT_VALUE = 0;
	const CUSTOMER_GROUP_CODE = 'customer_group_code';

	protected $_requestData;

	protected $_vendorCollection;

	/**
	* @param \Magento\Framework\App\RequestInterface $requestData
	* @param \SM\Vendors\Model\Vendor $vendorCollection
	*/
	public function __construct(
		\Magento\Framework\App\RequestInterface $requestData,
        \SM\Vendors\Model\VendorFactory $vendorCollection
    ) {
    	$this->_requestData = $requestData;
		$this->_vendorCollection = $vendorCollection;
	}

	public function execute(Observer $observer)
    {
    	$customerGroup = $observer->getEvent()->getDataObject();
	    if(!$customerGroup->getVendorId()) {
	        $customerGroup->setVendorId($this->_requestData->getParam(self::VENDOR_ID, self::INIT_VALUE));
	    }
	    
	    if($customerGroup->getVendorId() !== self::INIT_VALUE) {
	    	$vendorData = $this->_vendorCollection->create()->load($customerGroup->getVendorId());
	        if($vendorPrefix = $vendorData->getVendorPrefix()) {
	            $customerGroup->setCustomerGroupCode("{$vendorPrefix}::" . $customerGroup->getData(self::CUSTOMER_GROUP_CODE));
	        }
	    }
    }
}