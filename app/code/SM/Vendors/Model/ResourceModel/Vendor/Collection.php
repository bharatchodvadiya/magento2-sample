<?php
namespace SM\Vendors\Model\ResourceModel\Vendor;
 
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{
    const MODEL_VENDOR = 'SM\Vendors\Model\Vendor';
    const RESOURCE_VENDOR = 'SM\Vendors\Model\ResourceModel\Vendor';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(self::MODEL_VENDOR, self::RESOURCE_VENDOR);
    }

    public function toVendorPrefixArray()
	{
		$vendorPrefixResult = array();
		foreach($this as $itemData) {
			$vendorPrefixResult[$itemData->getId()] = $itemData->getVendorPrefix();
		}
		return $vendorPrefixResult;
	}
}
