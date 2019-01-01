<?php
namespace SM\Vendors\Controller\Adminhtml\Representative;

use Magento\Backend\App\Action;

class Delete extends \Magento\Backend\App\Action
{
    const USER_ID = 'user_id';
    const REPRESENTATIVE_MODEL = 'SM\Vendors\Model\Representative';
    const REDIRECT_PATH = '*/*/';
    const EDIT = '*/*/edit';
    const REPRESENTATIVE_SUCCESS = 'The user has been deleted.';
    const DELETE_ERROR = 'Unable to find a user to delete.';
    const REPRESENTATIVE_ERROR = 'You cannot delete your own account.';

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    public function __construct(
        \Magento\Backend\App\Action\Context $contextData,
        \Magento\Backend\Model\Auth\Session $adminSession
    ) {
        $this->_adminSession = $adminSession;
        parent::__construct($contextData);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $currentUser = $this->_adminSession->getUser();
        $userId = $this->getRequest()->getParam(self::USER_ID);
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($userId) {
            if ($currentUser->getId() == $userId) {
                $this->messageManager->addError(__(self::REPRESENTATIVE_ERROR));
                return $resultRedirect->setPath(self::EDIT, [self::USER_ID => $userId]);
            }
            try {
                // init model and delete
                $representativeModel = $this->_objectManager->create(self::REPRESENTATIVE_MODEL);
                $representativeModel->load($userId);
                $representativeModel->delete();
                // display success message
                $this->messageManager->addSuccess(__(self::REPRESENTATIVE_SUCCESS));
                return $resultRedirect->setPath(self::REDIRECT_PATH);
            } catch (\Exception $representativeException) {
                // display error message
                $this->messageManager->addError($representativeException->getMessage());
                // go back to edit form
                return $resultRedirect->setPath(self::EDIT, [self::USER_ID => $userId]);
            }
        }
        // display error message
        $this->messageManager->addError(__(self::DELETE_ERROR));
        // go to grid
        return $resultRedirect->setPath(self::REDIRECT_PATH);
    }
}