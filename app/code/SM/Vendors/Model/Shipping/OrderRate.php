<?php
namespace SM\Vendors\Model\Shipping;

class OrderRate extends \Magento\Framework\Model\AbstractModel
{
	const RESOURCE_ORDERRATE = 'SM\Vendors\Model\ResourceModel\Shipping\OrderRate';
    const MAGENTO_RESOURCE_CONNECTION = 'Magento\Framework\App\ResourceConnection';
    const MAGENTO_DEFAULT_CONNECTION = '\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION';
    const SHIPPING_SELECT_QUERY = 'select * from magento_sm_vendor_shipping_order_rate where vendor_id=:vendorId limit 1';
    const VENDOR_ID = ':vendorId';
    const NULL_DATA = '';
    const MIN_AMOUNT = 'min_amount';
    const MAX_AMOUNT = 'max_amount';
    const SHIPPING_PRICE_DATA = 'shipping_price';
    const ORDER_AMOUNT = 'order_amount';
    const CMP_ARRAY = "cmp_array";
    const INIT_VALUE = 0;
    const FIRST_VALUE = 1;
    const MAX_VALUE = 99999999999999;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_ORDERRATE);
    }

    protected function getConnection()
    {
        $objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
        $this->databaseConnection = $objectManager->get(self::MAGENTO_RESOURCE_CONNECTION)->getConnection(self::MAGENTO_DEFAULT_CONNECTION);
        return $this->databaseConnection;
    }

    public function loadOrderShipping($vendorId) {
        $orderShippingQuery=$this->getConnection()->prepare(self::SHIPPING_SELECT_QUERY);
        $orderShippingQuery->bindParam(self::VENDOR_ID, $vendorId);
        $orderShippingQuery->execute();
        $shippingData=$orderShippingQuery->fetch();
        return $shippingData;
    }

    public function getRates($shippingRates) {
        if(!empty($shippingRates)) {
            $shippingRates = unserialize($shippingRates);
            $shippingRates = array_filter($shippingRates);
            return $shippingRates;
        }
        return self::NULL_DATA;
    }

    public function getRateRanger($shippingRate) {
        $ratesData = $this->getRates($shippingRate);
        $ratesConvert = array();
        $rateRanger = array();
        
        if($ratesData) {
            foreach($ratesData as $orderRate) {
                $ratesConvert[] = $orderRate;
            }
        }
        usort($ratesConvert, array($this, self::CMP_ARRAY));
        
        for($initValue = self::INIT_VALUE ; $initValue < count($ratesConvert) - self::FIRST_VALUE; $initValue++) {
            $rateRanger[] = array(
                self::MIN_AMOUNT => floatval($ratesConvert[$initValue][self::ORDER_AMOUNT]),
                self::MAX_AMOUNT => floatval($ratesConvert[$initValue+self::FIRST_VALUE][self::ORDER_AMOUNT]),
                self::SHIPPING_PRICE_DATA => floatval($ratesConvert[$initValue][self::SHIPPING_PRICE_DATA])
            );
        }
        if(!empty($rateRanger)) {
            $rateRanger[] = array(
                self::MIN_AMOUNT => floatval($ratesConvert[count($ratesConvert)-self::FIRST_VALUE][self::ORDER_AMOUNT]),
                self::MAX_AMOUNT => self::MAX_VALUE,
                self::SHIPPING_PRICE_DATA => floatval($ratesConvert[count($ratesConvert)-self::FIRST_VALUE][self::SHIPPING_PRICE_DATA])
            );
        }
        return $rateRanger;
    }

    static function cmp_array($a, $b) {
        return floatval($a[self::ORDER_AMOUNT]) > floatval($b[self::ORDER_AMOUNT]);
    }
}