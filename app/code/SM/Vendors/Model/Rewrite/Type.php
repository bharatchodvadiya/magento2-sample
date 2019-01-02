<?php
namespace SM\Vendors\Model\Rewrite\Eav\Entity;

class Type extends \Magento\Eav\Model\Entity\Type
{
	const VENDOR_ORDER = 'vendor_order';
	const ORDER = 'order';
	const INVOICE = 'invoice';
	const CREDIT_MEMO = 'creditmemo';
	const SHIPMENT = 'shipment';
	const VENDOR_OBJECT = 'vendor_object';
	const NULL_DATA = 0;
    const FIRST_DATA = 1;
    const STRING_PAD = 9;

	protected $_helperData;

	public function __construct(
		\SM\Vendors\Helper\Data $helperData
	) {
		$this->_helperData = $helperData;
	}
	/**
     * Retrieve new incrementId
     *
     * @param int $storeId
     * @return string
     */
    public function fetchNewIncrementId($storeId = null)
    {
    	$entityTypeCode = $this->getEntityTypeCode();
        switch ($entityTypeCode) {
        	case self::VENDOR_ORDER:
                $entityTypeCode = self::ORDER;
            case self::INVOICE:
            case self::CREDIT_MEMO:
            case self::SHIPMENT:
            	$vendorData = $this->_helperData->getVendorLogin();
            	if(!$vendorData) {
                    $vendorData = $this->getData(self::VENDOR_OBJECT);
                }
                if($vendorData) {
                    $lastId = $vendorData->getData("vendor_total_{$entityTypeCode}s");
                    $incrementId = $vendorData->getVendorPrefix() . '-' . str_pad($lastId + self::FIRST_DATA, self::STRING_PAD, self::NULL_DATA, STR_PAD_LEFT);
                    $vendorData->setData("vendor_total_{$entityTypeCode}s", $lastId + self::FIRST_DATA);
                    $vendorData->save();
                    return $incrementId;
                } else {
                	return parent::fetchNewIncrementId($storeId);
                }
            default: return parent::fetchNewIncrementId($storeId);
        }
    }
}