<?php
namespace SM\Vendors\Controller;

class Action extends \Magento\Framework\App\Action\Action
{
	const LABEL = 'label';
    const TITLE = 'title';
    const BREADCRUMB = 'breadcrumbs';
    const CRUMB_LINK = 'link';
    const MAGENTO_STORE = '\Magento\Store\Model\StoreManagerInterface';

	protected $resultPageFactory;

	protected $_coreRegistry;

	protected $_resultPage;

	protected $_helperData;

	public function __construct(
        \Magento\Framework\App\Action\Context $contextData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\Page $resultPage,
        \SM\Vendors\Helper\Data $helperData
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_helperData = $helperData;
        parent::__construct($contextData);
        $this->_resultPage = $resultPage;
    }

	protected function _initBreadcrumbs()
	{
		$breadcrumbs = $this->_resultPage->getLayout()->getBlock(self::BREADCRUMB);
		if ($breadcrumbs) {
			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$storeManager = $objectManager->get(self::MAGENTO_STORE);
			$baseUrl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
			$breadcrumbs->addCrumb('home', [
				self::LABEL => __('Home'),
				self::TITLE => __('Go to Home Page'),
				self::CRUMB_LINK => $baseUrl
			]);
			$breadcrumbs->addCrumb('vendors_list', [
				self::LABEL => __('Pages'),
				self::TITLE => __('Go to Pages List'),
				self::CRUMB_LINK => $this->_helperData->getVendorListUrl()
			]);
		}
		return $breadcrumbs;
	}

	public function execute() {

	}
}