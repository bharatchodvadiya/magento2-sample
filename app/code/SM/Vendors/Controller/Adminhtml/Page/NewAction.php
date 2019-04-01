<?php
namespace SM\Vendors\Controller\Adminhtml\Page;

class NewAction extends \Magento\Backend\App\Action
{
    const EDIT = 'edit';

    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;
    
    /**
     * @param \Magento\Backend\App\Action\Context $contextData
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $contextData,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($contextData);
    }

    /**
     * Forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward(self::EDIT);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }
}