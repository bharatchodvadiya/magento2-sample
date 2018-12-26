<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class AdminCustomerPrepareSave implements ObserverInterface
{
	const PASSWORD = 'password';
	const USER_PASSWORD = 'user_password';
	const VENDOR = 'vendor';
	const ACCOUNT = 'account';
	const NEW_PASSWORD = 'new_password';
	const VENDOR_SHIPPING_METHODS = 'vendor_shipping_methods';
	const NULL_DATA = 0;
	const COMMA = ',';
    const NAME = 'name';
    const DOT_STRING = '.';
    const FIRST_DATA = 1;
    const VENDOR_LOGO = 'vendor_logo';
    const SALE_POSTCODES = 'vendor_sale_postcodes';
    const FILE_NAME = 'File-';
    const VENDOR_BANNERS = 'vendor/banners/';
    const PRODUCT_DELIVERY_AREA = 'sm_product_delivery_area';
    const DELETE = 'delete';
    const VALUE = 'value';
    const SALES_REPRESENTATIVE = 'in_sales_representative';
    const NULL_VALUE = '';
    const VENDOR_IMAGES = 'vendor/images/';
    const IMAGES = 'images';

	protected $_vendorCollection;

	protected $_productAction;

	protected $_helperData;

	protected $_vendorRepresentative;

	/**
	* @param \SM\Vendors\Model\Vendor $vendorCollection
	*/
	public function __construct(
        \SM\Vendors\Model\VendorFactory $vendorCollection,
        \Magento\Catalog\Model\Product\Action $productAction,
        \SM\Vendors\Helper\Data $helperData,
        \SM\Vendors\Model\Representative $vendorRepresentative
    ) {
		$this->_vendorCollection = $vendorCollection;
		$this->_productAction = $productAction;
		$this->_helperData = $helperData;
		$this->_vendorRepresentative = $vendorRepresentative;
	}

	public function execute(Observer $observer)
    {
		$customerData = $observer->getCustomer();
		$requestData = $observer->getRequest();
		$vendorData = $requestData->getPost(self::VENDOR);
		$postData = $requestData->getPost(self::ACCOUNT);
		if(!empty($postData[self::PASSWORD])) {
			$customerData->setData(self::USER_PASSWORD,$postData[self::PASSWORD]);
		}
		elseif(!empty($postData[self::NEW_PASSWORD])) {
			$customerData->setData(self::USER_PASSWORD,$postData[self::NEW_PASSWORD]);
		}
		if(!empty($vendorData)) {
			$customerData->addData($vendorData);
		}
		if(isset($vendorData[self::VENDOR_SHIPPING_METHODS])) {
			$vendorShippingMethods = $vendorData[self::VENDOR_SHIPPING_METHODS];
			$customerData->setData(self::VENDOR_SHIPPING_METHODS,implode(self::COMMA, $vendorShippingMethods));
		}
		$vendorId = $customerData->getVendorId();

		if(isset($vendorData[self::SALE_POSTCODES])) {
			if(!empty($vendorId)) {
				$vendorData = $this->_vendorCollection->create()->load($vendorId);
				if($vendorData->getId()) {
					$productsData = $vendorData->getProductCollection();
					$pidArray = array();
					foreach ($productsData as $productValue) {
					    $pidArray[] = $productValue->getId();
					    $this->_productAction->updateAttributes($pidArray, array(self::PRODUCT_DELIVERY_AREA => self::COMMA . $vendorData[self::SALE_POSTCODES] .self::COMMA), self::NULL_DATA);
					}
				}
			}
		}

		$imageData = array();
        if (!empty($_FILES[self::VENDOR_LOGO][self::NAME])) {
            $extension = substr($_FILES[self::VENDOR_LOGO][self::NAME], strrpos($_FILES[self::VENDOR_LOGO][self::NAME], self::DOT_STRING) + self::FIRST_DATA);
            $fileName = self::FILE_NAME.time().self::DOT_STRING.$extension;
            $this->_helperData->uploadImage(self::VENDOR_LOGO, $fileName, self::IMAGES);
            $imageData[self::VENDOR_LOGO] = self::VENDOR_IMAGES.$fileName;
			$customerData->setData(self::VENDOR_LOGO,$imageData[self::VENDOR_LOGO]);
        }

        if (empty($imageData[self::VENDOR_LOGO])) {
			$vendorLogo = $requestData->getPost(self::VENDOR_LOGO);
            if (isset($vendorLogo[self::DELETE]) && $vendorLogo[self::DELETE] == self::FIRST_DATA) {
				if ($vendorLogo[self::VALUE] != self::NULL_VALUE) {
					$imageFile = $vendorLogo[self::VALUE];
					$resultData = $this->_helperData->removeImage($imageFile, self::IMAGES);
				}
				$imageData[self::VENDOR_LOGO] = self::NULL_VALUE;
				$customerData->setData(self::VENDOR_LOGO, $imageData[self::VENDOR_LOGO]);
			}
		}

		if(isset($vendorData[self::SALES_REPRESENTATIVE])) {
			$representatives = array();
			parse_str($vendorData[self::SALES_REPRESENTATIVE], $representatives);
			$vendorId = $customerData->getVendorId();
			$this->_vendorRepresentative->deleteRepresentative($customerData->getId());
			foreach($representatives as $representativeId) {
				$this->_vendorRepresentative->addCustomerRepresentative($customerData->getId(), $representativeId);
			}
		}
		$customerData->save();
	}
}