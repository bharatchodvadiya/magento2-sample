<?php
namespace SM\Vendors\Controller\Adminhtml\Vendor;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    const VENDOR_ID = 'vendor_id';
    const VENDOR_MODEL = 'SM\Vendors\Model\Vendor';
    const REDIRECT_PATH = '*/*/';
    const EDIT = '*/*/edit';
    const VENDOR_SUCCESS = 'The vendor has been saved.';
    const VENDOR_SESSION = 'Magento\Backend\Model\Session';
    const VENDOR_ERROR = 'Something went wrong while saving the vendor.';
    const CURRENT = '_current';
    const BACK = 'back';
    const COMMA_STRING = ',';
    const SM_HELPER = 'SM\Vendors\Helper\Data';
    const VENDOR_LOGO = 'vendor_logo';
    const VENDOR_IMAGES = 'vendor/images/';
    const NAME = 'name';
    const FIRST_DATA = 1;
    const DOT_STRING = '.';
    const IMAGES = 'images';
    const NULL_DATA = '';
    const DELETE = 'delete';
    const VALUE = 'value';
    const VENDOR_SLASH = '/vendor_id/';

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
            $vendorModel = $this->_objectManager->create(self::VENDOR_MODEL);
            $vendorId = $this->getRequest()->getParam(self::VENDOR_ID);
            if ($vendorId) {
                $vendorModel->load($vendorId);
            }
            foreach ($formData as $keyData => $valueData) {
                if (is_array($valueData)) {
                    $formData[$keyData] = implode(self::COMMA_STRING, $this->getRequest()->getParam($keyData));
                }
            }
            $vendorModel->addData($formData);

            $imageData = array();
            $imageHelper = $this->_objectManager->get(self::SM_HELPER);
            if (!empty($_FILES[self::VENDOR_LOGO][self::NAME])) {
                $fileExtension = substr($_FILES[self::VENDOR_LOGO][self::NAME], strrpos($_FILES[self::VENDOR_LOGO][self::NAME], self::DOT_STRING) + self::FIRST_DATA);
                $fileName = $vendorModel->getVendorSlug().self::DOT_STRING.$fileExtension;
                $imageFile = $imageHelper->uploadImage(self::VENDOR_LOGO, $fileName, self::IMAGES);
                if ($imageFile) {
                    $imageData[self::VENDOR_LOGO] = self::VENDOR_IMAGES.$imageFile;
                    $vendorModel->setVendorLogo($imageData[self::VENDOR_LOGO]);
                }
            }
            if (empty($imageData[self::VENDOR_LOGO])) {
                $vendorImages = $this->getRequest()->getPost(self::VENDOR_LOGO);
                if (isset($vendorImages[self::DELETE]) && $vendorImages[self::DELETE] == self::FIRST_DATA) {
                    if ($vendorImages[self::VALUE] != self::NULL_DATA) {
                        $imageFile = $vendorImages[self::VALUE];
                        $uploadedFile = $imageHelper->removeImage($imageFile, self::IMAGES);
                    }
                    $imageData[self::VENDOR_LOGO] = self::NULL_DATA;
                    $vendorModel->setData(self::VENDOR_LOGO, $imageData[self::VENDOR_LOGO]);
                }
                else {
                    $vendorModel->setData(self::VENDOR_LOGO, $vendorImages[self::VALUE]);
                }
            }
            try {
                $vendorModel->save();
                $this->messageManager->addSuccess(__(self::VENDOR_SUCCESS));
                $this->_objectManager->get(self::VENDOR_SESSION)->setFormData(false);
                if ($this->getRequest()->getParam(self::BACK)) {
                    return $resultRedirect->setPath(self::EDIT, [self::VENDOR_ID => $vendorModel->getId(), self::CURRENT => true]);
                }
                $vendorData = $imageHelper->getVendorLogin();
                if($vendorData) {
                    $vendorId = $vendorData[self::VENDOR_ID];
                    return $resultRedirect->setPath(self::EDIT.self::VENDOR_SLASH.$vendorId);
                }
                else {
                    return $resultRedirect->setPath(self::REDIRECT_PATH);
                }
            } catch (\Magento\Framework\Exception\LocalizedException $vendorException) {
                $this->messageManager->addError($vendorException->getMessage());
            } catch (\RuntimeException $vendorException) {
                $this->messageManager->addError($vendorException->getMessage());
            } catch (\Exception $vendorException) {
                $this->messageManager->addException($vendorException, __(self::VENDOR_ERROR));
            }
            $this->_getSession()->setFormData($formData);
            return $resultRedirect->setPath(self::EDIT, [self::VENDOR_ID => $this->getRequest()->getParam(self::VENDOR_ID)]);
        }
        return $resultRedirect->setPath(self::REDIRECT_PATH);
    }
}