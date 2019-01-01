<?php
namespace SM\Vendors\Block\Adminhtml\Shipping\Edit\Tab\OrderRate\Element;

class Rate extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    const UNDERSCORE = '_';
    const FIRST_VALUE = 1;
    const SECOND_VALUE = 2;
    const RATES = 'rates';
    const REQUEST_URL = 'REQUEST_URI';
    const SLASH = '/';
    const VENDOR_ID = 'vendor_id';
    const VENDOR_STRING = 6;
    const ID_STRING = 7;

    protected $_systemStore;

    protected $_orderrateCollection;

    public function __construct(
        \Magento\Backend\Block\Template\Context $contextData,
        \Magento\Framework\Registry $registryData,
        \Magento\Framework\Data\FormFactory $formFactory,
        \SM\Vendors\Model\Shipping\OrderRate $orderrateCollection,
        \Magento\Store\Model\System\Store $systemStore,
        array $formData = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_orderrateCollection = $orderrateCollection;
        parent::_construct($contextData, $registryData, $formFactory, $formData);
    }

    public function getElementHtml()
    {
        $currentUrl = $_SERVER[self::REQUEST_URL];
        $splitUrl = explode(self::SLASH, $currentUrl);
        if($splitUrl[self::VENDOR_STRING] == self::VENDOR_ID) {
            $vendorId = $splitUrl[self::ID_STRING];
        }
        $shippingOrderModel = $this->_orderrateCollection->loadOrderShipping($vendorId);
        $shippingRates = $this->_orderrateCollection->getRates($shippingOrderModel[self::RATES]);
        $_htmlId = self::UNDERSCORE . uniqid();
        $_colspan = self::SECOND_VALUE;
        $_colspan = $_colspan > self::FIRST_VALUE ? 'colspan="' . $_colspan . '"' : '';
        $orderRateDiv = '<div class="grid" id="grid'.$_htmlId.'" style="position:relative">
            <table cellpadding="0" cellspacing="0" class="border">
            <tbody>';
        $orderRateDiv.= '<tr class="headings" id="headings'.$_htmlId.'" style="background-color: #f1f1f1; border: 1px solid #e3e3e3; height: 27px; font-size: 0.9em;">';
        $orderRateDiv.= '<th width="37%">Order Amount</th>
            <th width="37%" style="border: 1px solid #e3e3e3;">Shipping Price</th><th width="26%"></th></tr>';
        if($shippingRates) {
            foreach($shippingRates as $keyData => $valueData) {
                $orderRateDiv.= '<tr id='.$keyData.'><td class="'.$keyData.'-order_amount" style="border-width:1px;border-color:#e3e3e3;border-style:solid;"><input id="'.$keyData.'_order_amount" style="width:150px;height:25px;margin-left:13px;margin-top:5px;" type="text" name="orderrate[rates]['.$keyData.'][order_amount]" value="'.$valueData['order_amount'].'">&nbsp;&nbsp;&nbsp;up to</td><td class="'.$keyData.'-shipping_price" style="border-width:1px;border-color:#e3e3e3;border-style:solid;"><input id="'.$keyData.'_shipping_price" style="width:150px;height:25px;margin-left:13px;" name="orderrate[rates]['.$keyData.'][shipping_price]" type="text" value="'.$valueData['shipping_price'].'"></td><td style="border-width:1px;border-color:#e3e3e3;border-style:solid;"><button class="delete" type="button" style="margin-left:10px;" id="'.$keyData.'" onclick="jQuery(\'#\'+this.id).remove();"><span>Delete</span></button></td></tr>';
            }
        }
        $orderRateDiv.= '<tr id="addRow'.$_htmlId.'">
            <td colspan="2" style="border-width:1px;border-color:#e3e3e3;border-style:solid;"></td>
            <td style="border-width:1px;border-color:#e3e3e3;border-style:solid;"><button style="margin-left:10px;" onclick="" class="scalable add" type="button" id="addToEndBtn'.$_htmlId.'">
            <span>Add Rate</span>
                    </button></td></tr>';
        $orderRateDiv.= '</tbody></table>
        <input type="hidden" name="orderrate[rates][__empty]" value="" /></div>';
        $orderRateDiv.= '<div id="empty'.$_htmlId.'">
            <button style="" onclick="" class="scalable add" type="button" id="emptyAddBtn'.$_htmlId.'">
                <span>Add Rate</span>
            </button>
        </div>';

        $orderRateDiv.= "\n" .'<script type="text/javascript">
            require(["prototype"], function() {
                function addNewRate() {
                    var dateId = new Date();
                    var getTime = dateId.getTime();
                    var getSeconds = dateId.getMilliseconds();
                    var rateHtml = "<tr id=_"+getTime+"_"+getSeconds+"><td style=border-width:1px;border-color:#e3e3e3;border-style:solid; class=_"+getTime+"_"+getSeconds+"-order_amount><input style=width:150px;height:25px;margin-left:13px;margin-top:5px; type=text name=orderrate[rates][_"+getTime+"_"+getSeconds+"][order_amount] id=_"+getTime+"_"+getSeconds+"_order_amount>&nbsp;&nbsp;&nbsp;up to</td><td style=border-width:1px;border-color:#e3e3e3;border-style:solid; class=_"+getTime+"_"+getSeconds+"-shipping_price><input style=width:150px;height:25px;margin-left:13px; type=text name=orderrate[rates][_"+getTime+"_"+getSeconds+"][shipping_price] id=_"+getTime+"_"+getSeconds+"_shipping_price></td><td style=border-width:1px;border-color:#e3e3e3;border-style:solid;><button class=delete type=button style=margin-left:10px; id=_"+getTime+"_"+getSeconds+" onclick=jQuery(\'#\'+this.id).remove();><span>Delete</span></button></td></tr>";
                    jQuery("#addRow'.$_htmlId.'").before(rateHtml);
                }
                
                Event.observe("addToEndBtn'.$_htmlId.'", "click", function () {
                    addNewRate();
                });
                // initialize standalone button
                jQuery("#empty'.$_htmlId.'").hide();
                Event.observe("emptyAddBtn'.$_htmlId.'", "click", function () {
                    addNewRate();
                    jQuery("#grid'.$_htmlId.'").show();
                    jQuery("#empty'.$_htmlId.'").hide();
                });';

        if (!$shippingRates) {
            $orderRateDiv.= 'jQuery("#grid'.$_htmlId.'").hide();
            jQuery("#empty'.$_htmlId.'").show();';
        }
        if($shippingRates) {
            $orderRateDiv.= 'jQuery("#empty'.$_htmlId.'").hide();';
        }
        $orderRateDiv.= "\n" .'});
            </script>';
        return $orderRateDiv;
    }
}