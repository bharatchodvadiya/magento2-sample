<?php
namespace SM\Vendors\Model\ResourceModel\Shipment;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    const MODEL_SHIPMENT = 'SM\Vendors\Model\Shipment';
    const RESOURCE_SHIPMENT = 'SM\Vendors\Model\ResourceModel\Shipment';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(self::MODEL_SHIPMENT, self::RESOURCE_SHIPMENT);
    }
}