<?php
namespace SM\Vendors\Model\ResourceModel\Deliveryarea;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    const MODEL_DELIVERY = 'SM\Vendors\Model\Deliveryarea';
    const RESOURCE_DELIVERY = 'SM\Vendors\Model\ResourceModel\Deliveryarea';
    const TEXT_TABLE = 'dat';
    const VENDOR_DELIVERYAREA_TEXT = 'magento_sm_vendor_deliveryarea_text';
    const DELIVERYAREA_ENTITY = 'dat.deliveryarea_id = main_table.entity_id';
    const TABLE_PARENT_ID = 'main_table.parent_id = ?';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(self::MODEL_DELIVERY, self::RESOURCE_DELIVERY);
    }

    public function getDeliveryareaCollection($parentId) {
    	$this->getSelect()->join([self::TEXT_TABLE => self::VENDOR_DELIVERYAREA_TEXT], self::DELIVERYAREA_ENTITY)->where(self::TABLE_PARENT_ID, $parentId);
    	return $this;
    }
}