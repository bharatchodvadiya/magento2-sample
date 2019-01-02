<?php
namespace SM\Vendors\Model\Customer\Attribute\Source;

class Customergroup extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
	const VENDOR_ID = 'vendor_id';
	const VALUE = 'value';
	const LABEL = 'label';
	const GREATER_THEN = 'gt';
	const NULL_DATA = '';

	protected $_groupCollection;

	protected $_helperData;

	protected $_options;

	public function __construct(
		\Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollection,
		\SM\Vendors\Helper\Data $helperData
	) {
		$this->_groupCollection = $groupCollection;
		$this->_helperData = $helperData;
	}

	public function getAllOptions()
    {
    	$collectionData = $this->_groupCollection->create();
    	$collectionData->addFieldToFilter(self::VENDOR_ID, [self::GREATER_THEN => 0]);
            
        if($vendorData = $this->_helperData->getVendorLogin()) {
            $collectionData->addFilter(self::VENDOR_ID, $vendorData[self::VENDOR_ID]);
        }
        $this->_options = $collectionData->load()->toOptionArray();
        array_unshift($this->_options, array(self::VALUE=> self::NULL_DATA, self::LABEL=> self::NULL_DATA));
        return $this->_options;
    }
}
