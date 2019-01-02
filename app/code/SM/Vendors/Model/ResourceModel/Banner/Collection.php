<?php
namespace SM\Vendors\Model\ResourceModel\Banner;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{
    const MODEL_BANNER = 'SM\Vendors\Model\Banner';
    const RESOURCE_BANNER = 'SM\Vendors\Model\ResourceModel\Banner';
    const MAIN_VENDOR_ID = 'main_table.vendor_id = ?';
    
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(self::MODEL_BANNER, self::RESOURCE_BANNER);
    }

    public function addVendorToFilter($vendorId) {
		$this->getSelect()->where(self::MAIN_VENDOR_ID, $vendorId);
		return $this;
	}
}
