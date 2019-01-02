<?php
namespace SM\Vendors\Model\ResourceModel;

class Invoice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	const INVOICE_TABEL = 'magento_sales_invoice_grid';
	const ENTITY_ID = 'entity_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::INVOICE_TABEL, self::ENTITY_ID);
    }
}