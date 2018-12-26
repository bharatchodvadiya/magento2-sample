<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class CustomerRegisterSuccess implements ObserverInterface
{
	const NULL_DATA = '';
	const VENDOR_NAME = 'vendor_name';
	const VENDOR_STATUS = 'vendor_status';
	const USER_PASSWORD = 'user_password';
	const INIT_VALUE = 0;
	const SPACE = ' ';

	protected $_customerValue;

	/**
    * @param \Magento\Customer\Model\Customer $customerValue
    */
	public function __construct(
        \Magento\Customer\Model\Customer $customerValue
    ) {
        $this->_customerValue = $customerValue;
    }

	public function execute(Observer $observer)
    {
    	$customerData = $observer->getEvent()->getCustomer();
		$customerPassword = $this->_customerValue->getPassword();
		if (!empty($customerPassword)) {
			$customerAddresses = $customerData->getAddresses();
			$vendorName = self::NULL_DATA;
			
			if (is_array($customerAddresses) && sizeof($customerAddresses)) {
				$customerAddress = reset($customerAddresses); 
				$vendorName = $customerAddress->getCompany();
			}
			if (!$vendorName) {
				$vendorName = $customerData->getFirstname() . self::SPACE . $customerData->getLastname();
			}
			$customerData->setData(self::VENDOR_NAME , $vendorName);
			$customerData->setData(self::VENDOR_STATUS , self::INIT_VALUE);
			$customerData->setData(self::USER_PASSWORD, $customerPassword);
			$customerData->save();
		}
		return $this;
    }
}