<?php
namespace SM\Vendors\Controller\Index;

class Index extends \SM\Vendors\Controller\Action
{
	const CURRENT_VENDOR = 'current_vendor';
	const PAGE = 'Page';
    const LABEL = 'label';
    const TITLE = 'title';
    const VENDOR_PREFIX = 'vendor_';

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $currentVendor = $this->_coreRegistry->registry(self::CURRENT_VENDOR);
        $breadcrumbs = $this->_initBreadcrumbs();
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(self::VENDOR_PREFIX.$currentVendor->getVendorPrefix(), [
                    self::LABEL => $currentVendor->getVendorName(),
                    self::TITLE => $currentVendor->getVendorName(),
            ]);
        }
        $resultPage->getConfig()->getTitle()->set(__(self::PAGE));
        return $resultPage;
    }
}
