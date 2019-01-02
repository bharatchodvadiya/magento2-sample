<?php
namespace SM\Vendors\Model;

class Shipment extends \Magento\Framework\Model\AbstractModel
{
	const RESOURCE_SHIPMENT = 'SM\Vendors\Model\ResourceModel\Shipment';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_SHIPMENT);
    }
}