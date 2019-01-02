<?php
namespace SM\Vendors\Model;

class Representative extends \Magento\User\Model\User
{
	const RESOURCE_REPRESENTATIVE = 'SM\Vendors\Model\ResourceModel\Representative';
    const MAGENTO_RESOURCE_CONNECTION = 'Magento\Framework\App\ResourceConnection';
    const MAGENTO_DEFAULT_CONNECTION = '\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION';
    const DELETE_REPRESENTATIVE = 'DELETE FROM magento_sm_vendor_customer_representative WHERE customer_id=:customerId';
    const CUSTOMER_ID = ':customerId';
    const CUSTOMER_REPRESENTATIVE = 'INSERT INTO magento_sm_vendor_customer_representative (customer_id,representative_id) VALUES (:customerId,:representativeId) ON DUPLICATE KEY UPDATE customer_id=:customerId, representative_id=:representativeId';
    const REPRESENTATIVE_ID = ':representativeId';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_REPRESENTATIVE);
    }

    protected function getConnection()
    {
        $objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
        $this->databaseConnection = $objectManager->get(self::MAGENTO_RESOURCE_CONNECTION)->getConnection(self::MAGENTO_DEFAULT_CONNECTION);
        return $this->databaseConnection;
    }

    public function deleteRepresentative($customerId) {
        $representativeQuery = $this->getConnection()->prepare(self::DELETE_REPRESENTATIVE);
        $representativeQuery->bindParam(self::CUSTOMER_ID, $customerId);
        $representativeQuery->execute();
    }

    public function addCustomerRepresentative($customerId, $representativeId) {
        $customerRepresentative = $this->getConnection()->prepare(self::CUSTOMER_REPRESENTATIVE);
        $customerRepresentative->bindParam(self::CUSTOMER_ID, $customerId);
        $customerRepresentative->bindParam(self::REPRESENTATIVE_ID, $representativeId);
        $customerRepresentative->execute();
    }
}