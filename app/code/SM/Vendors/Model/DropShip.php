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
use Magento\Sales\Model\Order\Shipment;

class DropShip extends AbstractCarrier implements CarrierInterface
{
    const SHIPPING_NAME = 'name';
    const DROP_SHIPPING = 'dropshipping';
    const TITLE = 'title';
    const PRODUCT_VENDOR_ID = 'sm_product_vendor_id';
    const VENDOR = 'vendor';
    const ITEMS = 'items';
    const SHIPPING_METHODS = 'shipping_methods';
    const RATE = 'rate';
    const NULL_DATA = 0;
    const SHIPPING_RATES_VENDORS = 'shipping_rates_by_vendors';
    const METHOD = 'method';
    const ARRAY_CARTESIAN = 'array_cartesian';
    const UNDERSCORE = '_';
    const SYNTAX_BREAK = '<br/>';
    const CARRIERS = 'carriers';

    protected $_code = 'dropshipping';

    protected $_isFixed = true;

    protected $_rateResultFactory;
    
    protected $_rateMethodFactory;

    protected $_productLoader;

    protected $_vendorCollection;

    protected $_shippingLoader;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $shippingLogger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param array $shippingData
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $shippingLogger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        \Magento\Catalog\Model\ProductFactory $productLoader,
        \SM\Vendors\Model\VendorFactory $vendorCollection,
        \Magento\Shipping\Model\Shipping $shippingLoader,
        array $shippingData = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_productLoader = $productLoader;
        $this->_vendorCollection = $vendorCollection;
        $this->_shippingLoader = $shippingLoader;
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

    public function array_cartesian($arraysField) {
        $resultData = array();
        $keysData = array_keys($arraysField);
        $reverseKeys = array_reverse($keysData);
        $sizeField = intval(count($arraysField) > 0);
        foreach ($arraysField as $array) {
            $sizeField *= count($array);
        }
        for ($integerData = 0; $integerData < $sizeField; $integerData++) {
            $resultData[$integerData] = array();
            foreach ($keysData as $keyValue) {
                $resultData[$integerData][$keyValue] = current($arraysField[$keyValue]);
            }
            foreach ($reverseKeys as $keyValue) {
                if (next($arraysField[$keyValue])) {
                    break;
                }
                elseif (isset ($arraysField[$keyValue])) {
                    reset($arraysField[$keyValue]);
                }
            }
        }
        return $resultData;
    }

    public function collectRates2(\Magento\Quote\Model\Quote\Address\RateRequest $requestData)
    {
        $storeId = $requestData->getStoreId();
        if (!$requestData->getOrig()) {
            $requestData->setCountryId(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_COUNTRY_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $requestData->getStore()
                )
            )->setRegionId(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_REGION_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $requestData->getStore()
                )
            )->setCity(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_CITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $requestData->getStore()
                )
            )->setPostcode(
                $this->_scopeConfig->getValue(
                    Shipment::XML_PATH_STORE_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $requestData->getStore()
                )
            );
        }

        $limitCarrier = $requestData->getLimitCarrier();
        $carriers = $this->_scopeConfig->getValue(
            self::CARRIERS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        
        foreach ($carriers as $carrierCode => $carrierConfig) {
            if(!in_array($carrierCode, $limitCarrier)) {
                $this->_shippingLoader->collectCarrierRates($carrierCode, $requestData);
            }
        }
        return $this;
    }

    /**
     * Collect and get rates for storefront
     */
    public function collectRates(RateRequest $requestData)
    {
        if(!$this->isActive())
            return false;

        $rateResult = $this->_rateResultFactory->create();
        $vendorsData = array();
        if ($requestData->getAllItems()) {
            foreach ($requestData->getAllItems() as $itemValue) {
                $productData = $this->_productLoader->create()->load($itemValue->getProduct()->getId());
                $vendorId = (int)$productData->getData(self::PRODUCT_VENDOR_ID);
                if(empty($vendorsData[$vendorId])) {
                    $vendorObject = $this->_vendorCollection->create()->load($vendorId);
                    $vendorsData[$vendorId] = array(
                        self::VENDOR => $vendorObject,
                        self::ITEMS => array(),
                        self::SHIPPING_METHODS => array()
                    );
                }
                $vendorsData[$vendorId][self::ITEMS][] = $itemValue;
            }
        }
        ksort($vendorsData);

        $shippingMethods = array();
        $integerData=self::NULL_DATA;
        foreach($vendorsData as $vendorValue) {
            $availableShippingMethods = $vendorValue[self::VENDOR]->getAvaiableShippingMethods();
            $requestVendorClone = clone $requestData;
            $requestVendorClone->setAllItems($vendorValue[self::ITEMS]);
            $requestVendorClone->setLimitCarrier(array($this->_code));
            $requestVendorClone->setVendorId($vendorValue[self::VENDOR]->getId());
            $packageQty = self::NULL_DATA;
            $packageWeight = self::NULL_DATA;
            foreach($vendorValue[self::ITEMS] as $itemValue) {
                $packageQty += $itemValue->getQty();
                $packageWeight += $itemValue->getWeight();
            }
            $requestVendorClone->setPackageQty($packageQty);
            $requestVendorClone->setPackageWeight($packageWeight);
            $shipping = $this->collectRates2($requestVendorClone);
            if(!empty($shipping->getResult())) {
                foreach($shipping->getResult()->getAllRates() as $rateValue) {
                    if(in_array($rateValue->getCarrier(), $availableShippingMethods)) {
                        $vendorsData[$vendorValue[self::VENDOR]->getId()][self::SHIPPING_METHODS][] = $rateValue;
                        if(empty($shippingMethods[$integerData])) {
                            $shippingMethods[$integerData] = array();
                        }
                        $shippingMethods[$integerData][] = array(
                            self::VENDOR => $vendorValue[self::VENDOR],
                            self::RATE => $rateValue
                        );
                    }
                }
            }
            $integerData++;
        }

        $shippingMethodsCartesian = $this->array_cartesian($shippingMethods);
        foreach($shippingMethodsCartesian as $shippingRates) {
            $methodTitle = array();
            $methodCode = array();
            $methodCost = self::NULL_DATA;
            $methodPrice = self::NULL_DATA;
            $methodDetail = array();
            
            foreach($shippingRates as $rateValue) {
                $methodTitle[] = $rateValue[self::VENDOR]->getVendorName().' '.$rateValue[self::RATE]->getCarrierTitle().' - ' . $rateValue[self::RATE]->getMethodTitle();
                $methodCode[] = 'v'.$rateValue[self::VENDOR]->getId().self::UNDERSCORE.$rateValue[self::RATE]->getCarrier().self::UNDERSCORE . $rateValue[self::RATE]->getMethod();
                $methodPrice += $rateValue[self::RATE]->getPrice();
                $methodCost += $rateValue[self::RATE]->getCost();
                
                $methodDetail[$rateValue[self::VENDOR]->getId()] = array(
                    self::METHOD => $rateValue[self::RATE]->getData(),
                    self::VENDOR => $rateValue[self::VENDOR]->getData()
                );
            }
            $methodTitle  = implode(self::SYNTAX_BREAK, $methodTitle);
            $methodCode  =  implode('|', $methodCode);

            $rateMethod = $this->_rateMethodFactory->create();
            $rateMethod->setCarrier($this->_code);
            $rateMethod->setCarrierTitle($this->getConfigData(self::TITLE));
            $rateMethod->setMethod(self::DROP_SHIPPING);
            $rateMethod->setMethodTitle($methodTitle);
            $rateMethod->setMethod($methodCode);
            $rateMethod->setCost($methodCost);
            $rateMethod->setPrice($methodPrice);
            $rateMethod->setData(self::SHIPPING_RATES_VENDORS,$vendorsData);
            $methodDetail = serialize($methodDetail);
            $rateMethod->setMethodDetail($methodDetail);
            $rateResult->append($rateMethod);
        }
        return $rateResult;
    }
}