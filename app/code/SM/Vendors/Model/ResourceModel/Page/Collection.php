<?php
namespace SM\Vendors\Model\ResourceModel\Page;
 
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
 
class Collection extends AbstractCollection
{
    const MODEL_PAGE = 'SM\Vendors\Model\Page';
    const RESOURCE_PAGE = 'SM\Vendors\Model\ResourceModel\Page';
    const MAIN_VENDOR_ID = 'main_table.vendor_id = ?';
    
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(self::MODEL_PAGE, self::RESOURCE_PAGE);
    }

    public function addVendorToFilter($vendorId) {
		$this->getSelect()->where(self::MAIN_VENDOR_ID, $vendorId);
		return $this;
	}
}
