<?php
namespace SM\Vendors\Model;

class Deliveryarea extends \Magento\Framework\Model\AbstractModel
{
	const RESOURCE_DELIVERY_AREA = 'SM\Vendors\Model\ResourceModel\Deliveryarea';
    const NULL_DATA = 0;
    const TEXT_TABLE = 'dat';
    const VENDOR_DELIVERYAREA_TEXT = 'magento_sm_vendor_deliveryarea_text';
    const DELIVERYAREA_ENTITY = 'dat.deliveryarea_id = main_table.entity_id';
    const MAGENTO_RESOURCE_CONNECTION = 'Magento\Framework\App\ResourceConnection';
    const MAGENTO_DEFAULT_CONNECTION = '\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION';
    const DELIVERY_TEXT_QUERY = 'INSERT INTO magento_sm_vendor_deliveryarea_text (deliveryarea_id,name,store) VALUES (:deliveryId,:name,:store) ON DUPLICATE KEY UPDATE name=:name';
    const DELIVERY_UPDATE_QUERY = 'UPDATE magento_sm_vendor_deliveryarea_text SET name=:name WHERE deliveryarea_id=:deliveryId';
    const DELIVERY_DELETE_QUERY = 'DELETE FROM magento_sm_vendor_deliveryarea_text WHERE deliveryarea_id=:deliveryId';
    const NAME = 'name';
    const DELIVERY_ID = ':deliveryId';
    const NAME_PARAM = ':name';
    const STORE_PARAM = ':store';
    const ID = 'id';

    protected $_deliveryareaCollection = null;

    protected $_deliveryFactory;

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_DELIVERY_AREA);
    }

    protected function getConnection()
    {
        $objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
        $this->databaseConnection = $objectManager->get(self::MAGENTO_RESOURCE_CONNECTION)->getConnection(self::MAGENTO_DEFAULT_CONNECTION);
        return $this->databaseConnection;
    }

    public function __construct(
        \Magento\Framework\Model\Context $contextData,
        \Magento\Framework\Registry $registryData,
        \SM\Vendors\Model\ResourceModel\Deliveryarea\CollectionFactory $deliveryFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resourceData = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $deliveryData = []
    ) {
        parent::__construct($contextData, $registryData, $resourceData, $resourceCollection, $deliveryData);
        $this->_deliveryFactory = $deliveryFactory;
    }

    public function getDeliveryareaCollection($parentId) {
        $collectionData = $this->_deliveryFactory->create();
        $collectionData->getSelect()->join([self::TEXT_TABLE => self::VENDOR_DELIVERYAREA_TEXT], self::DELIVERYAREA_ENTITY);
        return $collectionData;
    }

    public function getDeliveryareaRootCollection($storeData = null) {
        if(is_null($this->_deliveryareaCollection)) {
            $collectionData = $this->getDeliveryareaCollection(self::NULL_DATA);
            $this->_deliveryareaCollection = array();
            foreach($collectionData as $categoryData) {
                $this->_deliveryareaCollection[]= $categoryData;
            }
        }
        return $this->_deliveryareaCollection;
    }

    public function saveDeliveryarea($lastId, $formData, $storeId) {
        $deliveryName = $formData[self::NAME];
        $deliveryQuery = $this->getConnection()->prepare(self::DELIVERY_TEXT_QUERY);
        $deliveryQuery->bindParam(self::DELIVERY_ID, $lastId);
        $deliveryQuery->bindParam(self::NAME_PARAM, $deliveryName);
        $deliveryQuery->bindParam(self::STORE_PARAM, $storeId);
        $deliveryQuery->execute();
        return;
    }

    public function updateDeliveryarea($formData) {
        $deliveryName = $formData[self::NAME];
        $deliveryId = $formData[self::ID];
        $deliveryUpdateQuery = $this->getConnection()->prepare(self::DELIVERY_UPDATE_QUERY);
        $deliveryUpdateQuery->bindParam(self::NAME_PARAM, $deliveryName);
        $deliveryUpdateQuery->bindParam(self::DELIVERY_ID, $deliveryId);
        $deliveryUpdateQuery->execute();
        return;
    }

    public function deleteDeliveryarea($deliveryId) {
        $deliveryDeleteQuery = $this->getConnection()->prepare(self::DELIVERY_DELETE_QUERY);
        $deliveryDeleteQuery->bindParam(self::DELIVERY_ID, $deliveryId);
        $deliveryDeleteQuery->execute();
        return;
    }
}