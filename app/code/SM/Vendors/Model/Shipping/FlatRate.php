<?php
namespace SM\Vendors\Model\Shipping;

class FlatRate extends \Magento\Framework\Model\AbstractModel
{
    const RESOURCE_FLATRATE = 'SM\Vendors\Model\ResourceModel\Shipping\FlatRate';
    const MAGENTO_RESOURCE_CONNECTION = 'Magento\Framework\App\ResourceConnection';
    const MAGENTO_DEFAULT_CONNECTION = '\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION';
    const SHIPPING_SELECT_QUERY = 'select * from magento_sm_vendor_shipping_flatrate where vendor_id=:vendorId limit 1';
    const VENDOR_ID = ':vendorId';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_FLATRATE);
    }

    protected function getConnection()
    {
        $objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
        $this->databaseConnection = $objectManager->get(self::MAGENTO_RESOURCE_CONNECTION)->getConnection(self::MAGENTO_DEFAULT_CONNECTION);
        return $this->databaseConnection;
    }

    public function loadFlatShipping($vendorId) {
        $flatShippingQuery=$this->getConnection()->prepare(self::SHIPPING_SELECT_QUERY);
        $flatShippingQuery->bindParam(self::VENDOR_ID, $vendorId);
        $flatShippingQuery->execute();
        $shippingData=$flatShippingQuery->fetch();
        return $shippingData;
    }
}
