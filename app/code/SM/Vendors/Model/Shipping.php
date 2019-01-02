<?php
namespace SM\Vendors\Model\Rewrite\Shipping;

use Magento\Sales\Model\Order\Shipment;

class Shipping extends \Magento\Shipping\Model\Shipping
{
	const CARRIERS = 'carriers';

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
				$this->collectCarrierRates($carrierCode, $requestData);
			}
		}
        return $this;
    }
}