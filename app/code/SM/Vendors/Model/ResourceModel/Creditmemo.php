<?php
namespace SM\Vendors\Model\ResourceModel;

class Creditmemo extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	const CREDITMEMO_TABEL = 'magento_sales_creditmemo_grid';
	const ENTITY_ID = 'entity_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::CREDITMEMO_TABEL, self::ENTITY_ID);
    }
}