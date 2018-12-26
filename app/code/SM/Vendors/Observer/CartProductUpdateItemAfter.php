<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class CartProductUpdateItemAfter implements ObserverInterface
{
	const SHIPPING_METHOD = 'shipping_method';

	protected $_requestData;

	protected $_cartData;

	public function __construct(
		\Magento\Framework\App\RequestInterface $requestData,
		\Magento\Checkout\Model\Cart $cartData
	) {
		$this->_requestData = $requestData;
		$this->_cartData = $cartData;
	}

	public function execute(Observer $observer)
    {
    	$shippingMethod = $this->_requestData->getParam(self::SHIPPING_METHOD);
    	if(!empty($shippingMethod)) {
    		$this->_cartData->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod)->save();
    	}
		return $this;
    }
}