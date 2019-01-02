<?php
namespace SM\Vendors\Model;

class Creditmemo extends \Magento\Framework\Model\AbstractModel
{
	const RESOURCE_CREDITMEMO = 'SM\Vendors\Model\ResourceModel\Creditmemo';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_CREDITMEMO);
    }
}