<?php
namespace SM\Vendors\Controller\Adminhtml\Representative;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    const VENDOR_SESSION = 'Magento\Backend\Model\Session';
    const REDIRECT_PATH = '*/*/';
    const USER_ID = 'user_id';
    const REPRESENTATIVE_MODEL = 'SM\Vendors\Model\Representative';
    const REPRESENTATIVE_SUCCESS = 'The user has been saved.';
    const EXCEPTION_ERROR = 'Something went wrong while saving the representative.';
    const EDIT = '*/*/edit';
    const CURRENT = '_current';
    const BACK = 'back';
    const REPRESENTATIVE_ERROR = 'This user no longer exists.';
    const NULL_DATA = '';

	/**
     * @param Action\Context $contextData
     */
    public function __construct(Action\Context $contextData)
    {
        parent::__construct($contextData);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $formData = $this->getRequest()->getPostValue();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($formData) {
            $representativeModel = $this->_objectManager->create(self::REPRESENTATIVE_MODEL);
            $userId = $this->getRequest()->getParam(self::USER_ID);
            if ($userId) {
                $representativeModel->load($userId);
            }
            if(!$representativeModel->getId() && $userId) {
                $this->messageManager->addError(__(self::REPRESENTATIVE_ERROR));
                return $resultRedirect->setPath(self::REDIRECT_PATH);
            }
            $representativeModel->addData($formData);

            /*
             * Unsetting new password and password confirmation if they are blank
             */
            if ($representativeModel->hasNewPassword() && $representativeModel->getNewPassword() === self::NULL_DATA) {
                $representativeModel->unsNewPassword();
            }
            if ($representativeModel->hasPasswordConfirmation() && $representativeModel->getPasswordConfirmation() === self::NULL_DATA) {
                $representativeModel->unsPasswordConfirmation();
            }
            $resultData = $representativeModel->validate();
            if (is_array($resultData)) {
                $this->_getSession()->setFormData($formData);
                foreach ($resultData as $errorMessage) {
                    $this->messageManager->addError($errorMessage);
                }
                return $resultRedirect->setPath(self::EDIT, [self::CURRENT => true]);
            }
            
            try {
                $representativeModel->save();
                $this->messageManager->addSuccess(__(self::REPRESENTATIVE_SUCCESS));
                $this->_objectManager->get(self::VENDOR_SESSION)->setFormData(false);
                if ($this->getRequest()->getParam(self::BACK)) {
                    return $resultRedirect->setPath(self::EDIT, [self::USER_ID => $representativeModel->getId(), self::CURRENT => true]);
                }
                return $resultRedirect->setPath(self::REDIRECT_PATH);
            } catch (\Magento\Framework\Exception\LocalizedException $vendorException) {
                $this->messageManager->addError($vendorException->getMessage());
            } catch (\RuntimeException $vendorException) {
                $this->messageManager->addError($vendorException->getMessage());
            } catch (\Exception $vendorException) {
                $this->messageManager->addException($vendorException, __(self::EXCEPTION_ERROR));
            }
            $this->_getSession()->setUserData(false);
            return $resultRedirect->setPath(self::EDIT, [self::USER_ID => $this->getRequest()->getParam(self::USER_ID)]);
        }
        return $resultRedirect->setPath(self::REDIRECT_PATH);
    }
}