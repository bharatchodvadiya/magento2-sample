<?php
namespace SM\Vendors\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
 
class CustomerAccountEditPostPreDispatch implements ObserverInterface
{
	const PASSWORD = 'password';
	const CONFIRMATION = 'confirmation';
	const USER_PASSWORD = 'user_password';

	protected $_customerSession;

	protected $_requestData;

	/**
	* @param \Magento\Customer\Model\Session $customerSession
	* @param \Magento\Framework\App\RequestInterface $requestData
	*/
	public function __construct(
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\App\RequestInterface $requestData
	) {
		$this->_customerSession = $customerSession;
		$this->_requestData = $requestData;
	}

	public function execute(Observer $observer)
    {
    	$customerData = $this->_customerSession->getCustomer();
		$postData = $this->_requestData->getPost();
		if(!empty($postData[self::PASSWORD]) && !empty($postData[self::CONFIRMATION]) && $postData[self::CONFIRMATION] == $postData[self::PASSWORD]) {
			$customerData->setData(self::USER_PASSWORD, $postData[self::PASSWORD]);
			$customerData->save();
		}
		return $this;
    }
}