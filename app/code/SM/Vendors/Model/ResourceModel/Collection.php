<?php
namespace SM\Vendors\Model\ResourceModel\Invoice;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    const MODEL_INVOICE = 'SM\Vendors\Model\Invoice';
    const RESOURCE_INVOICE = 'SM\Vendors\Model\ResourceModel\Invoice';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(self::MODEL_INVOICE, self::RESOURCE_INVOICE);
    }
}