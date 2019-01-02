<?php
namespace SM\Vendors\Model\ResourceModel\CatalogRule\Grid;

class Collection extends \Magento\CatalogRule\Model\ResourceModel\Grid\Collection
{
	const VENDOR_ID = 'vendor_id';
	const SM_HELPER = '\SM\Vendors\Helper\Data';

	protected function _initSelect()
    {
    	$objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
    	$helperData = $objectManager->create(self::SM_HELPER);
    	if($vendorData = $helperData->getVendorLogin()) {
    		$vendorId = $vendorData[self::VENDOR_ID];
    		$this->getSelect()->from(['main_table' => $this->getMainTable()])
	    		->joinLeft(['v' => 'magento_sm_vendor'], 'v.vendor_id = main_table.vendor_id')
	    		->columns("v.vendor_name")
	    		->where("main_table.vendor_id = {$vendorId}");
    	}
    	else {
	    	$this->getSelect()->from(['main_table' => $this->getMainTable()])
	    		->joinLeft(['v' => 'magento_sm_vendor'], 'v.vendor_id = main_table.vendor_id')
	    		->columns("v.vendor_name");
	    }
    	$this->addWebsitesToResult();
        return $this;
    }
}