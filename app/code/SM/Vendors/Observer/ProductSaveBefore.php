<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class ProductSaveBefore implements ObserverInterface
{
	const SM_VENDOR_ID = 'sm_product_vendor_id';
	const VENDOR_SALE_CODE = 'vendor_sale_postcodes';
	const SM_PRODUCT_DELIVERYAREA = 'sm_product_delivery_area';
	const COMMA = ',';

	protected $_vendorCollection;

	/**
	* @param \SM\Vendors\Model\Vendor $vendorCollection
	*/
	public function __construct(
        \SM\Vendors\Model\VendorFactory $vendorCollection
    ) {
		$this->_vendorCollection = $vendorCollection;
	}

	public function execute(Observer $observer)
    {
    	$productData = $observer->getEvent()->getDataObject();
	    if($vendorId = $productData->getData(self::SM_VENDOR_ID)) {
	    	$vendorData = $this->_vendorCollection->create()->load($vendorId);
    	    if($vendorData->getId() && $vendorData->getData(self::VENDOR_SALE_CODE)) {
    	        $productData->setData(self::SM_PRODUCT_DELIVERYAREA, explode(self::COMMA, $vendorData->getData(self::VENDOR_SALE_CODE)));
    	    }
	    }
    }
}
