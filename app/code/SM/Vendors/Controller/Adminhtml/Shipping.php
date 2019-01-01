<?php
namespace SM\Vendors\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Shipping extends Action
{
    const ALLOWED_SM_VENDORS = 'SM_Vendors::shipping_methods';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
 
    /**
     * Result page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
 
    /**
     * @param Context $contextData
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $contextData,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($contextData);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultPageFactory = $resultPageFactory;
    }
    
    /**
     * Vendor shipping action
     *
     * @return void
     */
    public function execute() {

    }

    /**
     * Shipping method access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(self::ALLOWED_SM_VENDORS);
    }
}