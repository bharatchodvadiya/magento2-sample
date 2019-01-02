<?php
namespace SM\Vendors\Model;

class Banner extends \Magento\Framework\Model\AbstractModel
{
	const RESOURCE_BANNER = 'SM\Vendors\Model\ResourceModel\Banner';

	/**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::RESOURCE_BANNER);
    }
}