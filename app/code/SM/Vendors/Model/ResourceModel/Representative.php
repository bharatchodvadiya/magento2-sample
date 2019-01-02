<?php
namespace SM\Vendors\Model\ResourceModel;

class Representative extends \Magento\User\Model\ResourceModel\User
{
	const REPRESENTATIVE_TABEL = 'magento_admin_user';
	const USER_ID = 'user_id';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(self::REPRESENTATIVE_TABEL, self::USER_ID);
    }
}