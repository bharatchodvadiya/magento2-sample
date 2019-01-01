<?php
namespace SM\Vendors\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Shipping\Model\Shipping\LabelGenerator;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;

class MassPrintShippingLabel extends \Magento\Backend\App\Action
{
	const SHIPMENT_IDS = 'shipment_ids';
    const ALL_ATTRIBUTE = '*';
    const ENTITY_ID = 'entity_id';
    const IN = 'in';
    const SHIPPING_LABEL = 'ShippingLabels.pdf';
    const APPLICATION_PDF = 'application/pdf';
    const LABELS_ERROR = 'There are no shipping labels related to selected shipments.';
    const VENDORS_SHIPMENT = 'vendors/shipment/';
    const ORDER_IDS = 'order_ids';
    const VENDORS_ORDER = 'vendors/order/';

	/**
     * @var LabelGenerator
     */
    protected $labelGenerator;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    protected $filterData;

    protected $collectionFactory;

    /**
     * @param Context $contextData
     * @param Filter $filterData
     * @param FileFactory $fileFactory
     * @param LabelGenerator $labelGenerator
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $contextData,
        Filter $filterData,
        FileFactory $fileFactory,
        LabelGenerator $labelGenerator,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->fileFactory = $fileFactory;
        $this->labelGenerator = $labelGenerator;
        parent::__construct($contextData, $filterData);
    }

	public function execute() {
		$shipmentIds = $this->getRequest()->getPost(self::SHIPMENT_IDS);
        if($shipmentIds) {
		    $collectionData = $this->collectionFactory->create()
                ->addAttributeToSelect(self::ALL_ATTRIBUTE)
                ->addAttributeToFilter(self::ENTITY_ID, [self::IN => $shipmentIds]);
        }
        $orderIds = $this->getRequest()->getPost(self::ORDER_IDS);
        if($orderIds) {
            $collectionData = $this->collectionFactory->create()
                ->addAttributeToSelect(self::ALL_ATTRIBUTE)
                ->addAttributeToFilter(self::ENTITY_ID, [self::IN => $orderIds]);
        }

        $labelsContent = [];

        if ($collectionData->getSize()) {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            foreach ($collectionData as $shipment) {
                $labelContent = $shipment->getShippingLabel();
                if ($labelContent) {
                    $labelsContent[] = $labelContent;
                }
            }
        }

        if (!empty($labelsContent)) {
            $outputPdf = $this->labelGenerator->combineLabelsPdf($labelsContent);
            return $this->fileFactory->create(
                self::SHIPPING_LABEL,
                $outputPdf->render(),
                DirectoryList::VAR_DIR,
                self::APPLICATION_PDF
            );
        }

        $this->messageManager->addError(__(self::LABELS_ERROR));
        if($shipmentIds) {
            return $this->resultRedirectFactory->create()->setPath(self::VENDORS_SHIPMENT);
        }
        else {
            return $this->resultRedirectFactory->create()->setPath(self::VENDORS_ORDER);
        }
	}
}