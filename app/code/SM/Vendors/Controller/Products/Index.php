<?php
namespace SM\Vendors\Controller\Products;

class Index extends \SM\Vendors\Controller\Action
{
    const CURRENT_VENDOR = 'current_vendor';
    const CURRENT_CATEGORY = 'current_category';
    const VENDOR_PREFIX = 'vendor_';
    const LABEL = 'label';
    const TITLE = 'title';
    const CRUMB_LINK = 'link';
    const PRODUCTS = 'Products';

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $currentVendor = $this->_coreRegistry->registry(self::CURRENT_VENDOR);
        $currentCategory = $this->_coreRegistry->registry(self::CURRENT_CATEGORY);
        $breadcrumbs = $this->_initBreadcrumbs();
        if ($breadcrumbs) {
            if ($currentCategory) {
                $breadcrumbs->addCrumb(self::VENDOR_PREFIX.$currentVendor->getVendorPrefix(), [
                    self::LABEL => $currentVendor->getVendorName(),
                    self::TITLE => $currentVendor->getVendorName(),
                    self::CRUMB_LINK => $currentVendor->getVendorUrl(),
                ]);
            }
            else if($currentVendor) {
                $breadcrumbs->addCrumb(self::VENDOR_PREFIX.$currentVendor->getVendorPrefix(), [
                    self::LABEL => $currentVendor->getVendorName(),
                    self::TITLE => $currentVendor->getVendorName(),
                ]);
            }
        }
        if($currentVendor) {
           $resultPage->getConfig()->getTitle()->set(__("{$currentVendor->getVendorName()} Products"));
        } else {
            $resultPage->getConfig()->getTitle()->set(__(self::PRODUCTS));
        }
        return $resultPage;
    }
}
