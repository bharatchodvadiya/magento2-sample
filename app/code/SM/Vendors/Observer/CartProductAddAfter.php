<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class CartProductAddAfter implements ObserverInterface
{
	const INVALID_QUOTE_ITEM = "[smvendors][hookCartProductAddAfter] Invalid quote item.";
	const PRODUCT_NOT_EXIST = "[smvendors][hookCartProductAddAfter] Product not exist. Item id=";
	const VENDOR_ID = 'sm_product_vendor_id';
	const LOG_LEVEL = '400';

	protected $_loggerData;

	protected $_productLoader;

	public function __construct(
		\Psr\Log\LoggerInterface $loggerData,
		\Magento\Catalog\Model\ProductFactory $productLoader
	) {
		$this->_loggerData = $loggerData;
		$this->_productLoader = $productLoader;
	}

	public function execute(Observer $observer)
    {
    	$quoteItem = $observer->getQuoteItem();
    	if($quoteItem == null || $quoteItem->getProductId() == null) {
			$this->_loggerData->log(self::LOG_LEVEL, self::INVALID_QUOTE_ITEM);
		    return $this;
		}
		
		$productData = $observer->getEvent()->getProduct();
		if(!$productData) {
			$productData = $this->_productLoader->create()->load($quoteItem->getProductId());
		}
		if(!$productData->getId()) {
		    $this->_loggerData->log(self::LOG_LEVEL, self::PRODUCT_NOT_EXIST.$quoteItem->getId());
		    return $this;
		}
		$quoteItem->setVendorId($productData->getData(self::VENDOR_ID));
		return $this;
    }
}