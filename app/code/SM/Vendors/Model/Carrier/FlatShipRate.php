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
use SM\Vendors\Model\Shipping\FlatRate;

class FlatShipRate extends AbstractCarrier implements CarrierInterface
{
	const ACTIVE = 'active';
	const PRICE = 'price';
	const SHIPPING_NAME = 'name';
	const VENDOR_FLAT_RATE = 'vendorflatrate';
	const FLAR_RATE = 'flatrate';
	const SHIPPING_PRICE = '0.00';
	const TITLE = 'title';
	const NULL_DATA = '';
    const VENDOR_ID = 'vendor_id';

	protected $_code = 'vendorflatrate';

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
        FlatRate $shippingCollection,
        \SM\Vendors\Model\Shipping\FlatRateFactory $shippingFactory,
        array $shippingData = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_shippingCollection = $shippingCollection;
        $this->_shippingFactory = $shippingFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $shippingLogger, $shippingData);
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
        $postData = $requestData->getPost(self::FLAR_RATE);
        $vendorId = $requestData->getParam(self::VENDOR_ID);
        if(!empty($postData)) {
            $shippingModel = $this->_shippingFactory->create()->load($vendorId, self::VENDOR_ID);
            $postData[self::VENDOR_ID] = $vendorId;
            $shippingModel->addData($postData);
            $shippingModel->save();
        }
    }

    /**
     * Collect and get rates for storefront
     */
    public function collectRates(RateRequest $request)
    {
    	$shippingConfig = $this->_shippingCollection->loadFlatShipping($request->getVendorId());

    	if (!$this->getConfigData(self::ACTIVE) && !$shippingConfig[self::ACTIVE]) {
            return false;
        }

        $shippingPrice = $shippingConfig[self::PRICE];
        if($shippingPrice == self::NULL_DATA) $shippingPrice = false;
    	$rateResult = $this->_rateResultFactory->create();

    	if ($shippingPrice !== false) {
			$rateMethod = $this->_rateMethodFactory->create();
			$rateMethod->setCarrier(self::VENDOR_FLAT_RATE);
		    $rateMethod->setCarrierTitle($this->getConfigData(self::TITLE));
		    /**
		     * Displayed as shipping method under Carrier
		     */
		    $rateMethod->setMethod(self::FLAR_RATE);
		    $rateMethod->setMethodTitle($this->getConfigData(self::SHIPPING_NAME));
		    if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = self::SHIPPING_PRICE;
            }
		    $rateMethod->setPrice($shippingPrice);
		    $rateMethod->setCost($shippingPrice);
		    $rateResult->append($rateMethod);
		}
        return $rateResult;
    }
}
