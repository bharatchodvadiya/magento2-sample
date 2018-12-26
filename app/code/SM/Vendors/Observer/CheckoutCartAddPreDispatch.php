<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class CheckoutCartAddPreDispatch implements ObserverInterface
{
	const XML_PATH_STORE_CITY = 'shipping/origin/city';

    const XML_PATH_STORE_REGION_ID = 'shipping/origin/region_id';

    const XML_PATH_STORE_ZIP = 'shipping/origin/postcode';

    const XML_PATH_STORE_COUNTRY_ID = 'shipping/origin/country_id';

	protected $_cartData;

	protected $_storeManager;

	protected $_customerSession;

	protected $_quoteAddress;

	protected $_scopeConfig;

	/**
    * @param \Magento\Checkout\Model\Cart $cartData
    * @param \Magento\Store\Model\StoreManagerInterface $storeManager
    * @param \Magento\Customer\Model\Session $customerSession
    * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    */
	public function __construct(
		\Magento\Checkout\Model\Session $cartData,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Quote\Model\Quote\AddressFactory $quoteAddress,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
	) {
		$this->_cartData = $cartData;
		$this->_storeManager = $storeManager;
		$this->_customerSession = $customerSession;
		$this->_quoteAddress = $quoteAddress;
		$this->_scopeConfig = $scopeConfig;
	}

	public function execute(Observer $observer)
    {
    	if(!$this->_cartData->getQuote()->getShippingAddress()->getCountryId()) {
			$storeData = $this->_storeManager->getStore()->getId();
			$customerData = $this->_customerSession->getCustomer();
			if($customerData->getId()) {
				$shippingAddress = $customerData->getDefaultShippingAddress();
				$billingAddress = $customerData->getDefaultBillingAddress();
				if(!empty($shippingAddress)) {
					$quoteAddressData = $this->_quoteAddress->create();
					$quoteAddressData->setData($shippingAddress->getData());
					$this->_cartData->getQuote()->setShippingAddress($quoteAddressData);
				}
				if(!empty($billingAddress)) {
					$quoteBillingData = $this->_quoteAddress->create();
					$quoteBillingData->setData($billingAddress->getData());
					$this->_cartData->getQuote()->setBillingAddress($quoteBillingData);
				}
				$this->_cartData->getQuote()->setCollectShippingRates(true)->save();
				$this->_cartData->getQuote()->setDataChanges(true);
			}
			else {
				$this->_cartData->getQuote()->getShippingAddress()
					->setCountryId($this->_scopeConfig->getValue(self::XML_PATH_STORE_COUNTRY_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
					->setRegionId($this->_scopeConfig->getValue(self::XML_PATH_STORE_REGION_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
					->setCity($this->_scopeConfig->getValue(self::XML_PATH_STORE_CITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
					->setPostcode($this->_scopeConfig->getValue(self::XML_PATH_STORE_ZIP, \Magento\Store\Model\ScopeInterface::SCOPE_STORE))
					->setCollectShippingRates(true);
				$this->_cartData->getQuote()->save();
				$this->_cartData->getQuote()->setDataChanges(true);
			}
		}
		return $this;
    }
}