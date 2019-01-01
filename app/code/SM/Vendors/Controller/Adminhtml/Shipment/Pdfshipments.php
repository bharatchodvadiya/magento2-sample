<?php
namespace SM\Vendors\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\Order\Pdf\Shipment;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;

class Pdfshipments extends \Magento\Backend\App\Action
{
	const SHIPMENT_IDS = 'shipment_ids';
    const ALL_ATTRIBUTE = '*';
    const ENTITY_ID = 'entity_id';
    const IN = 'in';
    const SHIPMENT_PDF = 'packingslip%s.pdf';
    const DATE_TIME = 'Y-m-d_H-i-s';
    const APPLICATION_PDF = 'application/pdf';

	/**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var Shipment
     */
    protected $pdfShipment;

    protected $filterData;

    protected $collectionFactory;

	/**
     * @param Context $contextData
     * @param Filter $filterData
     * @param DateTime $dateTime
     * @param FileFactory $fileFactory
     * @param Shipment $shipmentData
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        Context $contextData,
        Filter $filterData,
        DateTime $dateTime,
        FileFactory $fileFactory,
        Shipment $shipmentData,
        CollectionFactory $collectionFactory
    ) {
        $this->fileFactory = $fileFactory;
        $this->dateTime = $dateTime;
        $this->pdfShipment = $shipmentData;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($contextData, $filterData);
    }

    public function execute() {
    	$shipmentIds = $this->getRequest()->getPost(self::SHIPMENT_IDS);
        if (!empty($shipmentIds)) {
        	$collectionData = $this->collectionFactory->create()
                ->addAttributeToSelect(self::ALL_ATTRIBUTE)
                ->addAttributeToFilter(self::ENTITY_ID, [self::IN => $shipmentIds]);
            return $this->fileFactory->create(
	            sprintf(self::SHIPMENT_PDF, $this->dateTime->date(self::DATE_TIME)),
	            $this->pdfShipment->getPdf($collectionData)->render(),
	            DirectoryList::VAR_DIR,
	            self::APPLICATION_PDF
	        ); 
        }
    }
}