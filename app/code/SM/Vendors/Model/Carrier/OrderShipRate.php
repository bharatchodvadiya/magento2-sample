<?php
namespace SM\Vendors\Model\Carrier;

use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use SM\Vendors\Model\Shipping\OrderRate;

class OrderShipRate extends AbstractCarrier implements CarrierInterface
{
	const ACTIVE = 'active';
	const SHIPPING_NAME = 'name';
	const VENDOR_ORDER_RATE = 'vendororderrate';
	const ORDER_RATE = 'orderrate';
	const SHIPPING_PRICE = '0.00';
	const TITLE = 'title';
	const RATES = 'rates';
	const MIN_AMOUNT = 'min_amount';
	const MAX_AMOUNT = 'max_amount';
	const SHIPPING_PRICE_DATA = 'shipping_price';
	const INIT_VALUE = 0;
	const VENDOR_ID = 'vendor_id';

	protected $_code = 'vendororderrate';

	protected $_isFixed = true;

	protected $_rateResultFactory;
    
    protected $_rateMethodFactory;

    protected $_shippingCollection;

    protected $_shippingFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $shippingLogger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param FlatRate $shippingCollection
     * @param array $shippingData
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $shippingLogger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        OrderRate $shippingCollection,
        \SM\Vendors\Model\Shipping\OrderRateFactory $shippingFactory,
        array $shippingData = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_shippingCollection = $shippingCollection;
        $this->_shippingFactory = $shippingFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $shippingLogger, $shippingData);
    }

    public function getIsSupportDropShipping() {
    	return true;
    }

    /**
     * Generates list of allowed carrier`s shipping methods
     * Displays on cart price rules page
     */
    public function getAllowedMethods()
    {
        return [$this->_code => __($this->getConfigData(self::SHIPPING_NAME))];
    }

    public function saveConfig($observer) {
    	$requestData = $observer->getRequest();
    	$postData = $requestData->getPost(self::ORDER_RATE);
    	$vendorId = $requestData->getParam(self::VENDOR_ID);
    	if(!empty($postData)) {
    		$shippingModel = $this->_shippingFactory->create()->load($vendorId, self::VENDOR_ID);
	    	$postData[self::VENDOR_ID] = $vendorId;
	    	$postData[self::RATES] = serialize($postData[self::RATES]);
	    	$shippingModel->addData($postData);
	    	$shippingModel->save();
    	}
    }

    /**
     * Collect and get rates for storefront
     */
    public function collectRates(RateRequest $requestData)
    {
    	$shippingConfig = $this->_shippingCollection->loadOrderShipping($requestData->getVendorId());
    	if (!$this->getConfigData(self::ACTIVE) && !$shippingConfig[self::ACTIVE]) {
            return false;
        }

        $shippingRates = $this->_shippingCollection->getRateRanger($shippingConfig[self::RATES]);
        $orderAmount = $this->getOrderAmount($requestData);
        $shippingPrice = false;

     	if(empty($shippingRates)) {
     		return false;
     	}
	    foreach($shippingRates as $rateData) {
	     	if($orderAmount >= $rateData[self::MIN_AMOUNT] && $orderAmount <= $rateData[self::MAX_AMOUNT]) {
	     		$shippingPrice = $rateData[self::SHIPPING_PRICE_DATA];
	     		break;
	     	}
	    }
	    $rateResult = $this->_rateResultFactory->create();
	    if ($shippingPrice !== false) {
	    	$rateMethod = $this->_rateMethodFactory->create();
	    	$rateMethod->setCarrier(self::VENDOR_ORDER_RATE);
		    $rateMethod->setCarrierTitle($this->getConfigData(self::TITLE));
		    /**
		     * Displayed as shipping method under Carrier
		     */
		    $rateMethod->setMethod(self::ORDER_RATE);
		    $rateMethod->setMethodTitle($this->getConfigData(self::SHIPPING_NAME));
		    if ($requestData->getFreeShipping() === true || $requestData->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = self::SHIPPING_PRICE;
            }
            $rateMethod->setPrice($shippingPrice);
		    $rateMethod->setCost($shippingPrice);
		    $rateResult->append($rateMethod);
	    }
	    return $rateResult;
    }

    public function getOrderAmount($requestData) {
 		$orderAmount = self::INIT_VALUE;
 		if ($requestData->getAllItems()) {
	 		foreach($requestData->getAllItems() as $orderItem)
	 		{
				$orderAmount += floatval($orderItem->getRowTotal());	 			
	 		}
 		}
 		return $orderAmount;
 	}
}