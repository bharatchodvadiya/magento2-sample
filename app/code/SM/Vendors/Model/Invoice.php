<?php
namespace SM\Vendors\Model;

class Invoice extends \Magento\Framework\Model\AbstractModel
{
	const RESOURCE_INVOICE = 'SM\Vendors\Model\ResourceModel\Invoice';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_INVOICE);
    }
}