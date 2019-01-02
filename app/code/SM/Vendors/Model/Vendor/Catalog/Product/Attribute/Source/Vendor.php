<?php
namespace SM\Vendors\Model\Vendor\Catalog\Product\Attribute\Source;

class Vendor extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
	const VALUE = 'value';
	const LABEL = 'label';

	protected $_vendorCollection;

	public function __construct(
		\SM\Vendors\Model\Vendor $vendorCollection
	) {
		$this->_vendorCollection = $vendorCollection;
	}

	public function getAllOptions()
    {
    	$vendorOptions = array();
    	$vendorData = $this->_vendorCollection->getCollection();
    	foreach($vendorData as $vendor) {
			$vendorOptions[] = [
				self::VALUE => $vendor->getId(),
				self::LABEL => $vendor->getVendorName()
			];
		}
        return $vendorOptions;
    }

    public function getOptionText($valueData)
    {
        $vendorOptions = $this->getAllOptions();
        foreach ($vendorOptions as $optionData) {
            if(is_array($valueData)) {
                if (in_array($optionData[self::VALUE], $valueData)) {
                    return $optionData[self::LABEL];
                }
            }
            else {
                if ($optionData[self::VALUE] == $valueData) {
                    return $optionData[self::LABEL];
                }
            }
        }
        return false;
    }
}

