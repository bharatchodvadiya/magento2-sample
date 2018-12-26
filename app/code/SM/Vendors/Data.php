<?php
namespace SM\Vendors\Helper;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const VENDOR_ID = 'vendor_id';
    const AS_VENDOR = 'do_as_vendor';
    const IMAGE_JPG = 'jpg';
    const IMAGE_JPEG = 'jpeg';
    const IMAGE_GIF = 'gif';
    const IMAGE_PNG = 'png';
    const FILE_ID = 'fileId';
    const VENDOR = 'vendor/';
    const SLASH = "/";
    const PATH = 'path';
    const SECOND_PARAM = 2;
    const DELIVERYAREA_GRID = 'vendors/deliveryarea/grid';
    const CURRENT = '_current';
    const CARRIERS_DROPSHIPPING = 'carriers/dropshipping/active';
    const INVOICE_GRID_URL = 'vendors/order/invoices';
    const CREDITMEMO_GRID_URL = 'vendors/order/creditmemos';
    const SHIPMENT_GRID_URL = 'vendors/order/shipments';
    const VENDORS_ORDER_HISTORY = 'vendors/order/commentsHistory';
    const REPRESENTATIVE_GRID_URL = 'vendors/customer/representative';
    const XML_PATH_ENABLE_VENDOR_SLUG = 'smvendors/general/enable_vendor_slug';
    const VENDORS_LISTING = 'vendors/listing';
    const VENDORS_INDEX_INDEX = 'vendors/index/index';
    const ID = 'id';

    protected $_loggedVendor = null;

    protected $_vendorCollection;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\Framework\HTTP\Adapter\FileTransferFactory
     */
    protected $httpFactory;

    /**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;
    
    /**
     * File Uploader factory
     *
     * @var \Magento\Framework\Io\File
     */
    protected $_ioFile;

    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestData;

    protected $_backendUrl;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_enableVendorSlug = null;

    protected $_scopeConfig;

    protected $_productToVendor = array();

    protected $_vendorFactory;

    public function __construct(
        \Magento\Backend\Model\Auth\Session $adminSession,
        \SM\Vendors\Model\Vendor $vendorCollection,
        \Magento\Framework\App\RequestInterface $requestData,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \SM\Vendors\Model\VendorFactory $vendorFactory
    ) {
        $this->_adminSession = $adminSession;
        $this->_vendorCollection = $vendorCollection;
        $this->requestData = $requestData;
        $this->filesystem = $filesystem;
        $this->httpFactory = $httpFactory;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_ioFile = $ioFile;
        $this->_backendUrl = $backendUrl;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_vendorFactory = $vendorFactory;
    }

    public function getCoreConfig($configPath)
    {
        return $this->_scopeConfig->getValue(
            $configPath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getVendorLogin()
    {
        if($this->_loggedVendor === null) {
            $this->_loggedVendor = false;
            $userData = $this->_adminSession->getUser();
            if(!$userData) {
                $this->_loggedVendor = false;
            } else {
                $vendorData = $this->_vendorCollection->loadByUserId($userData->getUserId());
                if($vendorData[self::VENDOR_ID]) {
                    $this->_loggedVendor = $vendorData;
                }
                else {
                    $vendorData = $this->_vendorCollection->loadByVendorId($userData->getVendorId());
                    if($vendorData[self::VENDOR_ID]) {
                        $this->_loggedVendor = $vendorData;
                    }
                }
            }
            if(!$this->_loggedVendor) {
                $doAsVendor = $this->requestData->getParams();
                if(isset($doAsVendor[self::AS_VENDOR])) {
                    $vendorData = $this->_vendorCollection->loadByVendorId($doAsVendor[self::AS_VENDOR]);
                    if($vendorData[self::VENDOR_ID]) {
                        $this->_loggedVendor = $vendorData;
                    }
                }
            }
        }
        return $this->_loggedVendor;
    }

    public function removeImage($imageFile, $customDirectory)
    {
        $inputOutput = $this->_ioFile;
        $imageName = explode(self::SLASH, $imageFile);
        $filePath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().self::VENDOR.$customDirectory;
        $inputOutput->open(array(self::PATH => $filePath));
        if ($inputOutput->fileExists($imageName[self::SECOND_PARAM])) {
            return $inputOutput->rm($imageName[self::SECOND_PARAM]);
        }
        return false;
    }

    public function uploadImage($scopeData, $fileName, $customDirectory)
    {
        $uploaderFactory = $this->_fileUploaderFactory->create([self::FILE_ID => $scopeData]);
        $uploaderFactory->setAllowedExtensions([self::IMAGE_JPG, self::IMAGE_JPEG, self::IMAGE_GIF, self::IMAGE_PNG]);
        $uploaderFactory->setAllowRenameFiles(true);
        $uploaderFactory->setFilesDispersion(false);
        $uploaderFactory->setAllowCreateFolders(true);
        $uploadPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath().self::VENDOR.$customDirectory;
        if ($uploaderFactory->save($uploadPath, $fileName)) {
            return $uploaderFactory->getUploadedFileName();
        }
    }

    public function getDeliveryGridUrl()
    {
        return $this->_backendUrl->getUrl(self::DELIVERYAREA_GRID, [self::CURRENT => true]);
    }

    public function getInvoiceGridUrl() {
        return $this->_backendUrl->getUrl(self::INVOICE_GRID_URL, [self::CURRENT => true]);
    }

    public function getCreditmemoGridUrl() {
        return $this->_backendUrl->getUrl(self::CREDITMEMO_GRID_URL, [self::CURRENT => true]);
    }

    public function getShipmentGridUrl() {
        return $this->_backendUrl->getUrl(self::SHIPMENT_GRID_URL, [self::CURRENT => true]);
    }

    public function getRepresentativeUrl() {
        return $this->_backendUrl->getUrl(self::REPRESENTATIVE_GRID_URL, [self::CURRENT => true]);
    }

    public function getOrderHistoryUrl() {
        return $this->_backendUrl->getUrl(self::VENDORS_ORDER_HISTORY, [self::CURRENT => true]);
    }

    public function dropShipIsActive() {
        return $this->getCoreConfig(self::CARRIERS_DROPSHIPPING);
    }

    public function enableVendorSlug()
    {
        if (is_null($this->_enableVendorSlug)) {
            $this->_enableVendorSlug = $this->getCoreConfig(self::XML_PATH_ENABLE_VENDOR_SLUG);
        }
        return $this->_enableVendorSlug;
    }

    public function getVendorListUrl()
    {
        return $this->_storeManager->getStore()->getUrl(self::VENDORS_LISTING, [self::CURRENT => true]);
    }

    public function getImageUrl($imageFile) {
        $mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl.'vendor/images/'.$imageFile;
    }

    public function getVendorByProduct($productData)
    {
        $productId = $productData->getId();
        if (!isset($this->_productToVendor[$productId])) {
            $vendorId = (int) $productData->getSmProductVendorId();
            $vendorData = $this->_vendorFactory->create()->load($vendorId);
            $this->_productToVendor[$productId] = $vendorData;
        }
        return $this->_productToVendor[$productId];
    }

    public function getVendorUrl($vendorData)
    {
        if ($this->enableVendorSlug() && ($vendorSlug = $vendorData->getVendorSlug())) {
            return $this->_storeManager->getStore()->getUrl($vendorSlug, [self::CURRENT => true]);
        }
        return $this->_storeManager->getStore()->getUrl(self::VENDORS_INDEX_INDEX, [self::ID => $vendorData->getId()]);
    }
}