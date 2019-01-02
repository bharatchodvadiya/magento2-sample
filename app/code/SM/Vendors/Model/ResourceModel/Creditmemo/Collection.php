<?php
namespace SM\Vendors\Model\ResourceModel\Creditmemo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    const MODEL_CREDITMEMO = 'SM\Vendors\Model\Creditmemo';
    const RESOURCE_CREDITMEMO = 'SM\Vendors\Model\ResourceModel\Creditmemo';

    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(self::MODEL_CREDITMEMO, self::RESOURCE_CREDITMEMO);
    }
}
