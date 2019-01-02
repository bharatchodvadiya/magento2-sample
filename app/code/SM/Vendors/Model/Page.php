<?php
namespace SM\Vendors\Model;

class Page extends \Magento\Framework\Model\AbstractModel
{
	const RESOURCE_PAGE = 'SM\Vendors\Model\ResourceModel\Page';
    const MAGENTO_RESOURCE_CONNECTION = 'Magento\Framework\App\ResourceConnection';
    const MAGENTO_DEFAULT_CONNECTION = '\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_PAGE);
    }

    protected function getConnection()
    {
        $objectManager =   \Magento\Framework\App\ObjectManager::getInstance();
        $this->databaseConnection = $objectManager->get(self::MAGENTO_RESOURCE_CONNECTION)->getConnection(self::MAGENTO_DEFAULT_CONNECTION);
        return $this->databaseConnection;
    }
}