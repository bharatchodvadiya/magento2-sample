<?php
namespace SM\Vendors\Model\ResourceModel\Representative;
 
class Collection extends \Magento\User\Model\ResourceModel\User\Collection
{
    const MODEL_REPRESENTATIVE = 'SM\Vendors\Model\Representative';
    const RESOURCE_REPRESENTATIVE = 'SM\Vendors\Model\ResourceModel\Representative';
    const MAIN_VENDOR_ID = 'main_table.vendor_id = ?';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(self::MODEL_REPRESENTATIVE, self::RESOURCE_REPRESENTATIVE);
    }

    public function addVendorToFilter($vendorId) {
		$this->getSelect()->where(self::MAIN_VENDOR_ID, $vendorId);
		return $this;
	}

    public function addCustomerToFilter($customerId) {
        $this->getSelect()->joinLeft(['cr' => 'magento_sm_vendor_customer_representative'], 'main_table.user_id = cr.representative_id')->where('cr.customer_id =?', $customerId);
        return $this;
    }
}