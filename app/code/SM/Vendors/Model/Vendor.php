<?php
namespace SM\Vendors\Model;

class Vendor extends \Magento\Framework\Model\AbstractModel
{
    const RESOURCE_VENDOR = 'SM\Vendors\Model\ResourceModel\Vendor';
    const CORE_WRITE = 'core_write';
    const MAGENTO_RESOURCE_CONNECTION = 'Magento\Framework\App\ResourceConnection';
    const MAGENTO_DEFAULT_CONNECTION = '\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION';
    const VENDOR_SELECT_QUERY = 'select vendor_id from magento_sm_vendor where user_id=:userId limit 1';
    const ALL_VENDOR_SELECT = 'select * from magento_sm_vendor where vendor_status=:vendorStatus';
    const USER_ID = ':userId';
    const VENDOR_ID_QUERY = 'select vendor_id from magento_sm_vendor where vendor_id=:vendorId';
    const VENDOR_ID = ':vendorId';
    const CUSTOMER_QUERY = 'INSERT INTO magento_sm_vendor_customer (vendor_id,customer_id) VALUES (:vendorId,:customerId) ON DUPLICATE KEY UPDATE vendor_id=:vendorId, customer_id=:customerId';
    const CUSTOMER_ID = ':customerId';
    const VENDOR_STATUS = ':vendorStatus';
    const FIRST_DATA = 1;
    const VENDOR_SHIPPING = 'vendor_shipping_methods';
    const COMMA = ',';
    const VENDOR_NAME_QUERY = 'select a.vendor_id, vendor_name from magento_customer_group as a, magento_sm_vendor as vendor where a.vendor_id=vendor.vendor_id and a.customer_group_id=:groupId';
    const GROUP_ID = ':groupId';
    const ID = 'id';
    const CURRENT = '_current';
    const MAGENTO_STORE = '\Magento\Store\Model\StoreManagerInterface';
    const VENDORS_VIEW_INDEX = 'vendors/view/index';

    protected $databaseConnection;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_VENDOR);
    }

    protected function getConnection()
    {
        $objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
        $this->databaseConnection = $objectManager->get(self::MAGENTO_RESOURCE_CONNECTION)->getConnection(self::MAGENTO_DEFAULT_CONNECTION);
        return $this->databaseConnection;
    }

    public function loadByUserId($userId) {
        $vendorIdQuery=$this->getConnection()->prepare(self::VENDOR_SELECT_QUERY);
        $vendorIdQuery->bindParam(self::USER_ID, $userId);
        $vendorIdQuery->execute();
        $vendorData=$vendorIdQuery->fetch();
        return $vendorData;
    }

    public function loadByVendorId($vendorId) {
        $vendorIdQuery=$this->getConnection()->prepare(self::VENDOR_ID_QUERY);
        $vendorIdQuery->bindParam(self::VENDOR_ID, $vendorId);
        $vendorIdQuery->execute();
        $vendorData=$vendorIdQuery->fetch();
        return $vendorData;
    }

    public function addVendorCustomer($vendorId, $customerId) {
        $customerQuery = $this->getConnection()->prepare(self::CUSTOMER_QUERY);
        $customerQuery->bindParam(self::VENDOR_ID, $vendorId);
        $customerQuery->bindParam(self::CUSTOMER_ID, $customerId);
        $customerQuery->execute();
    }

    public function getAllVendors() {
        $vendorStatus = self::FIRST_DATA;
        $vendorSelectQuery=$this->getConnection()->prepare(self::ALL_VENDOR_SELECT);
        $vendorSelectQuery->bindParam(self::VENDOR_STATUS, $vendorStatus);
        $vendorSelectQuery->execute();
        $vendorData=$vendorSelectQuery->fetchAll();
        return $vendorData;
    }

    public function getAvaiableShippingMethods() {
        $avaiableShippingMethods = array();
        if($vendorShippingMethod = $this->getData(self::VENDOR_SHIPPING)) {
            $avaiableShippingMethods = explode(self::COMMA, $vendorShippingMethod);
        }
        return $avaiableShippingMethods;
    }

    public function getVendorByGroupId($groupId) {
        $vendorNameQuery=$this->getConnection()->prepare(self::VENDOR_NAME_QUERY);
        $vendorNameQuery->bindParam(self::GROUP_ID, $groupId);
        $vendorNameQuery->execute();
        $vendorNameData=$vendorNameQuery->fetch();
        return $vendorNameData;
    }

    public function getVendorUrl($vendorData)
    {
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->create(self::MAGENTO_STORE);
        if ($vendorSlug = $vendorData->getVendorSlug()) {
            return $storeManager->getStore()->getUrl($vendorSlug, [self::CURRENT => true]);
        }
        return $storeManager->getStore()->getUrl(self::VENDORS_VIEW_INDEX, [self::ID => $vendorData->getId()]);
    }
}
