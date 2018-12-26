<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class OrderPlaceAfter implements ObserverInterface
{
    const DROP_SHIPPING = 'dropshipping';
    const VENDOR_ID = 'sm_product_vendor_id';

    protected $_helperData;

    protected $_vendorCollection;

    protected $_orderCollection;

    protected $_checkoutSession;

    protected $_productLoader;

    /**
    * @param \SM\Vendors\Helper\Data $helperData
    * @param \SM\Vendors\Model\Vendor $vendorCollection
    * @param \SM\Vendors\Model\Order $orderCollection
    * @param \Magento\Checkout\Model\Session $checkoutSession
    */
    public function __construct(
        \SM\Vendors\Helper\Data $helperData,
        \SM\Vendors\Model\Vendor $vendorCollection,
        \SM\Vendors\Model\Order $orderCollection,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ProductFactory $productLoader
    ) {
        $this->_helperData = $helperData;
        $this->_vendorCollection = $vendorCollection;
        $this->_orderCollection = $orderCollection;
        $this->_checkoutSession = $checkoutSession;
        $this->_productLoader = $productLoader;
    }

    public function execute(Observer $observer)
    {
        $orderInstance = $observer->getEvent()->getOrder();
        $quoteData = $this->_checkoutSession->getQuote();
        $shippingMethod = $orderInstance->getShippingMethod();
        if($shippingMethod != NULL) {
            if($this->_helperData->dropShipIsActive()) {
                $shippingRate = $orderInstance->getShippingAddress()->getShippingRateByCode($shippingMethod);
                if($shippingMethod == self::DROP_SHIPPING) {
                    $methodDetail = $shippingRate->getMethodDetail();
                    $quoteData->getShippingAddress()->setShippingMethodDetail($methodDetail);
                    $shippingMethodDetail = $quoteData->getShippingAddress()->getShippingMethodDetail();
                    $orderInstance->setShippingMethodDetail($shippingMethodDetail);
                    $orderInstance->save();
                }
            }
        }
        if($customerId = $orderInstance->getCustomerId()) {
            $vendorsData = array();
            foreach($orderInstance->getAllItems() as $orderItem) {
                $productData = $this->_productLoader->create()->load($orderItem->getProductId());
                $vendorId = $productData->getData(self::VENDOR_ID);
                $vendorsData[$vendorId] = $vendorId;
            }
            foreach($vendorsData as $vendorId) {
                $this->_vendorCollection->addVendorCustomer($vendorId, $customerId);
            }
        }
        $this->_orderCollection->splitOrder($orderInstance);
        return $this;
    }
}