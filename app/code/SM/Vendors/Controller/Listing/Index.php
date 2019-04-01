<?php
namespace SM\Vendors\Controller\Listing;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    const VENDORS_LIST = 'List of Vendors';
    const BREADCRUMB = 'breadcrumbs';
    const LABEL = 'label';
    const TITLE = 'title';
    const CRUMB_LINK = 'link';
    const MAGENTO_STORE = '\Magento\Store\Model\StoreManagerInterface';

    protected $resultPageFactory;

    protected $_resultPage;

    /**
     * @param Context $contextData
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\Page $resultPage
     */
    public function __construct(
        Context $contextData,
        PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\Page $resultPage
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_resultPage = $resultPage;
        parent::__construct($contextData);
    }

    public function execute()
    {
        $resultPageData = $this->resultPageFactory->create();
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
                'label' => __('Vendors'),
                'title' => __('Vendors List'),
            ]);
        }
        $resultPageData->getConfig()->getTitle()->set(__(self::VENDORS_LIST));
        return $resultPageData;
    }
}
