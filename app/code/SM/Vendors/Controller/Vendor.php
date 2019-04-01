<?php
namespace SM\Vendors\Controller\Router;

class Vendor implements \Magento\Framework\App\RouterInterface
{
	const SLASH = '/';
	const DOT_HTML = '.html';
	const VENDOR_SLUG = 'vendor_slug';
	const CURRENT_VENDOR = 'current_vendor';
	const IN_VENDOR = 'in_vendor';
	const VENDORS = 'vendors';
	const INDEX = 'index';
	const SM_VENDORS = 'SM_Vendors';
	const MAGENTO_FORWARD = 'Magento\Framework\App\Action\Forward';
	const REQUEST = 'request';

	protected $actionFactory;

	protected $_responseData;

	protected $_helperData;

	protected $_vendorCollection;

	protected $_coreRegistry;

	public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\ResponseInterface $responseData,
        \SM\Vendors\Helper\Data $helperData,
        \SM\Vendors\Model\ResourceModel\Vendor\CollectionFactory $vendorCollection,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->actionFactory = $actionFactory;
        $this->_responseData = $responseData;
        $this->_helperData = $helperData;
        $this->_vendorCollection = $vendorCollection;
        $this->_coreRegistry = $coreRegistry;
    }

	public function match(\Magento\Framework\App\RequestInterface $requestData)
    {
    	$vendorHelper = $this->_helperData->enableVendorSlug();
    	if (!$vendorHelper) {
			return;
		}
		$requestPath = trim($requestData->getPathInfo(), self::SLASH);
		if (!$requestPath) {
			return;
		}
		$paramValue = explode(self::SLASH, $requestPath);
		if (strpos($paramValue[0], self::DOT_HTML) !== false) {
	        return;
		}
		$vendorCollectionData = $this->_vendorCollection->create()->addFieldToFilter(self::VENDOR_SLUG, $paramValue[0]);
		$vendorData = $vendorCollectionData->getFirstItem();
		if ($vendorCollectionData->count() && $vendorData && $vendorData->getId()) {
			$this->_coreRegistry->register(self::CURRENT_VENDOR, $vendorData);
			$this->_coreRegistry->register(self::IN_VENDOR, true);

			$requestData->setModuleName(self::VENDORS)->setControllerName(self::INDEX)->setActionName(self::INDEX)->setControllerModule(self::SM_VENDORS);
			return $this->actionFactory->create(
	            self::MAGENTO_FORWARD,
	            [self::REQUEST => $requestData]
	        );
		}
		return;
    }
}