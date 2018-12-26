<?php
namespace SM\Vendors\Helper;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
	const TYPE_NEW = 'new';
    const TYPE_UPDATE = 'update';
	const ORDER_EMAIL_TO_VENDOR_TEMPLATE_ID = 'smvendors_email/order/template_new_to_vendor';
	const ORDER_XML_PATH_EMAIL_IDENTITY = 'sales_email/order/identity';
	const LOG_LEVEL = '400';
	const VENDOR_NEW_ORDER_ERROR = "[smvendors][sendNewOrderEmailToVendor] Error when sending email. Data=\n";
	const SEND_NEW_ORDER_EMAIL = 'canSendNewOrderEmail';
	const INVOICE_EMAIL_TEMPLATE_ID = 'smvendors_email/invoice/template_new';
	const SEND_NEW_INVOICE_EMAIL = "[smvendors][sendNewInvoiceEmail] Error when sending email. Data=\n";
	const INVOICE_XML_PATH_EMAIL_IDENTITY = 'sales_email/invoice/identity';
    const SEND_UPDATE_ORDER_EMAIL = "[smvendors][sendUpdateOrderEmail] Error when sending email. Data=\n";
    const ORDER_EMAIL_UPDATE_TEMPLATE_ID = 'smvendors_email/order/template_update';
    const ORDER_XML_PATH_UPDATE_EMAIL_IDENTITY = 'sales_email/order_comment/identity';
    const SEND_UPDATE_INVOICE_EMAIL = "[smvendors][sendUpdateInvoiceEmail] Error when sending email. Data=\n";
    const INVOICE_EMAIL_UPDATE_TEMPLATE_ID = 'smvendors_email/invoice/template_update';
    const INVOICE_XML_PATH_UPDATE_EMAIL_IDENTITY = 'sales_email/invoice_comment/identity';
    const SEND_NEW_SHIPMENT_EMAIL = "[smvendors][sendNewShipmentEmail] Error when sending email. Data=\n";
    const SHIPMENT_EMAIL_TEMPLATE_ID = 'smvendors_email/shipment/template_new';
    const SHIPMENT_XML_PATH_EMAIL_IDENTITY = 'sales_email/shipment/identity';
    const SEND_UPDATE_SHIPMENT_EMAIL = "[smvendors][sendUpdateShipmentEmail] Error when sending email. Data=\n";
    const SHIPMENT_EMAIL_UPDATE_TEMPLATE_ID = 'smvendors_email/shipment/template_update';
    const SHIPMENT_XML_PATH_UPDATE_EMAIL_IDENTITY = 'sales_email/shipment_comment/identity';
    const SEND_UPDATE_CREDITMEMO_EMAIL = "[smvendors][sendUpdateCreditmemoEmail] Error when sending email. Data=\n";
    const CREDITMEMO_EMAIL_UPDATE_TEMPLATE_ID = 'smvendors_email/creditmemo/template_update';
    const CREDITMEMO_XML_PATH_UPDATE_EMAIL_IDENTITY = 'sales_email/creditmemo_comment/identity';
    const SEND_NEW_CREDITMEMO_EMAIL = "[smvendors][sendNewCreditMemoEmail] Error when sending email. Data=\n";
    const CREDITMEMO_EMAIL_TEMPLATE_ID = 'smvendors_email/creditmemo/template_new';
    const CREDITMEMO_XML_PATH_EMAIL_IDENTITY = 'sales_email/creditmemo/identity';

	protected $_loggerData;

	public function __construct(
		\Psr\Log\LoggerInterface $loggerData
	) {
		$this->_loggerData = $loggerData;
	}

	public function sendNewOrderEmailToVendor($vendorOrder, $orderInstance) {
		$emailData = array();
        $emailData['order'] = $orderInstance;
        $emailData['can_send_method'] = self::SEND_NEW_ORDER_EMAIL;
        $emailData['template_id'] = self::ORDER_EMAIL_TO_VENDOR_TEMPLATE_ID;
        $emailData['email_identity'] = self::ORDER_XML_PATH_EMAIL_IDENTITY;
        $emailData['notify_customer'] = false;
        $emailData['target_object'] = $orderInstance;

        $emailResult = $this->_sendMail($vendorOrder, $emailData, self::TYPE_NEW);
        if(!$emailResult) {
            $this->_loggerData->log(self::LOG_LEVEL, self::VENDOR_NEW_ORDER_ERROR);
            $this->_loggerData->log(self::LOG_LEVEL, print_r($emailData, true));
        }
        return $emailResult;
	}

	public function sendNewInvoiceEmail($vendorOrder, $invoiceData, $notifyCustomer = true, $commentData = '')
    {
        $emailData = array();
        $emailData['order'] = $invoiceData->getOrder();
        $emailData['can_send_method'] = 'canSendNewInvoiceEmail';
        $emailData['template_id'] = self::INVOICE_EMAIL_TEMPLATE_ID;
        $emailData['email_identity'] = self::INVOICE_XML_PATH_EMAIL_IDENTITY;
        $emailData['notify_customer'] = $notifyCustomer;
        $emailData['target_object'] = $invoiceData;
        $emailData['template_params'] = array('comment' => $commentData, 'invoice' => $invoiceData);
        
        $emailResult = $this->_sendMail($vendorOrder, $emailData, self::TYPE_NEW);
        if(!$emailResult) {
            $this->_loggerData->log(self::LOG_LEVEL, self::SEND_NEW_INVOICE_EMAIL);
            $this->_loggerData->log(self::LOG_LEVEL, print_r($emailData, true));
        }
        return $emailResult;
    }

    public function sendUpdateOrderEmail($vendorOrder, $orderObject, $customerNotify = true, $commentData = '') {
        $emailData = array();
        $emailData['order'] = $orderObject;
        $emailData['can_send_method'] = 'canSendOrderCommentEmail';
        $emailData['template_id'] = self::ORDER_EMAIL_UPDATE_TEMPLATE_ID;
        $emailData['email_identity'] = self::ORDER_XML_PATH_UPDATE_EMAIL_IDENTITY;
        $emailData['notify_customer'] = $customerNotify;
        $emailData['template_params'] = array('comment' => $commentData);

        $emailResult = $this->_sendMail($vendorOrder, $emailData, self::TYPE_UPDATE);
        if(!$emailResult) {
            $this->_loggerData->log(self::LOG_LEVEL, self::SEND_UPDATE_ORDER_EMAIL);
            $this->_loggerData->log(self::LOG_LEVEL, print_r($emailData, true));
        }
        return $emailResult;
    }

    public function sendUpdateInvoiceEmail($vendorOrder, $invoiceObject, $notifyCustomer = true, $commentData = '') {
        $emailData = array();
        $emailData['order'] = $invoiceObject->getOrder();
        $emailData['can_send_method'] = 'canSendInvoiceCommentEmail';
        $emailData['template_id'] = self::INVOICE_EMAIL_UPDATE_TEMPLATE_ID;
        $emailData['email_identity'] = self::INVOICE_XML_PATH_UPDATE_EMAIL_IDENTITY;
        $emailData['notify_customer'] = $notifyCustomer;
        $emailData['target_object'] = $invoiceObject;
        $emailData['template_params'] = array('comment' => $commentData, 'invoice' => $invoiceObject);
        
        $emailResult = $this->_sendMail($vendorOrder, $emailData, self::TYPE_UPDATE);
        if(!$emailResult) {
            $this->_loggerData->log(self::LOG_LEVEL, self::SEND_UPDATE_INVOICE_EMAIL);
            $this->_loggerData->log(self::LOG_LEVEL, print_r($emailData, true));
        }
        return $emailResult;
    }

    public function sendNewShipmentEmail($vendorOrder, $shipmentObject, $notifyCustomer = true, $commentData = '') {
        $emailData = array();
        $emailData['order'] = $shipmentObject->getOrder();
        $emailData['can_send_method'] = 'canSendNewShipmentEmail';
        $emailData['template_id'] = self::SHIPMENT_EMAIL_TEMPLATE_ID;
        $emailData['email_identity'] = self::SHIPMENT_XML_PATH_EMAIL_IDENTITY;
        $emailData['notify_customer'] = $notifyCustomer;
        $emailData['target_object'] = $shipmentObject;
        $emailData['template_params'] = array('comment' => $commentData, 'shipment' => $shipmentObject);
        
        $emailResult = $this->_sendMail($vendorOrder, $emailData, self::TYPE_NEW);
        if(!$emailResult) {
            $this->_loggerData->log(self::LOG_LEVEL, self::SEND_NEW_SHIPMENT_EMAIL);
            $this->_loggerData->log(self::LOG_LEVEL, print_r($emailData, true));
        }
        return $emailResult;
    }

    public function sendUpdateShipmentEmail($vendorOrder, $shipmentObject, $notifyCustomer = true, $commentData = '') {
        $emailData = array();
        $emailData['order'] = $shipmentObject->getOrder();
        $emailData['can_send_method'] = 'canSendShipmentCommentEmail';
        $emailData['template_id'] = self::SHIPMENT_EMAIL_UPDATE_TEMPLATE_ID;
        $emailData['email_identity'] = self::SHIPMENT_XML_PATH_UPDATE_EMAIL_IDENTITY;
        $emailData['notify_customer'] = $notifyCustomer;
        $emailData['target_object'] = $shipmentObject;
        $emailData['template_params'] = array('comment' => $commentData, 'shipment' => $shipmentObject);
        
        $emailResult = $this->_sendMail($vendorOrder, $emailData, self::TYPE_UPDATE);
        if(!$emailResult) {
            $this->_loggerData->log(self::LOG_LEVEL, self::SEND_UPDATE_SHIPMENT_EMAIL);
            $this->_loggerData->log(self::LOG_LEVEL, print_r($emailData, true));
        }
        return $emailResult;
    }

    public function sendUpdateCreditmemoEmail($vendorOrder, $orderCreditmemo, $notifyCustomer = true, $commentData = '') {
        $emailData = array();
        $emailData['order'] = $orderCreditmemo->getOrder();
        $emailData['can_send_method'] = 'canSendCreditmemoCommentEmail';
        $emailData['template_id'] = self::CREDITMEMO_EMAIL_UPDATE_TEMPLATE_ID;
        $emailData['email_identity'] = self::CREDITMEMO_XML_PATH_UPDATE_EMAIL_IDENTITY;
        $emailData['notify_customer'] = $notifyCustomer;
        $emailData['target_object'] = $orderCreditmemo;
        $emailData['template_params'] = array('comment' => $commentData, 'creditmemo' => $orderCreditmemo);
        
        $emailResult = $this->_sendMail($vendorOrder, $emailData, self::TYPE_UPDATE);
        if(!$emailResult) {
            $this->_loggerData->log(self::LOG_LEVEL, self::SEND_UPDATE_CREDITMEMO_EMAIL);
            $this->_loggerData->log(self::LOG_LEVEL, print_r($emailData, true));
        }
        return $emailResult;
    }

    public function sendNewCreditmemoEmail($vendorOrder, $orderCreditmemo, $notifyCustomer = true, $commentData = '') {
        $emailData = array();
        $emailData['order'] = $orderCreditmemo->getOrder();
        $emailData['can_send_method'] = 'canSendNewCreditmemoEmail';
        $emailData['template_id'] = self::CREDITMEMO_EMAIL_TEMPLATE_ID;
        $emailData['email_identity'] = self::CREDITMEMO_XML_PATH_EMAIL_IDENTITY;
        $emailData['notify_customer'] = $notifyCustomer;
        $emailData['target_object'] = $orderCreditmemo;
        $emailData['template_params'] = array('comment' => $commentData, 'creditmemo' => $orderCreditmemo);
        
        $emailResult = $this->_sendMail($vendorOrder, $emailData, self::TYPE_NEW);
        if(!$emailResult) {
            $this->_loggerData->log(self::LOG_LEVEL, self::SEND_NEW_CREDITMEMO_EMAIL);
            $this->_loggerData->log(self::LOG_LEVEL, print_r($emailData, true));
        }
        return $emailResult;
    }

	protected function _sendMail($vendorOrder, $emailData, $emailType = self::TYPE_NEW)
	{
		return true;
	}
}