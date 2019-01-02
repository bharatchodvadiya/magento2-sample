<?php
namespace SM\Vendors\Model\System\Config\Source;

use Magento\Framework\Option\ArrayInterface;
 
class Role implements ArrayInterface
{
	const VALUE = 'value';
	const LABEL = 'label';

	protected $_resourceCollection;

	public function __construct(
		\Magento\Authorization\Model\ResourceModel\Role\Collection $resourceCollection
	) {
		$this->_resourceCollection = $resourceCollection;
	}

	public function toOptionArray()
    {
    	$collectionData = $this->_resourceCollection->setRolesFilter();
    	$roleOptions = array();
		foreach($collectionData as $roleData) {
			$roleOptions[] = [
				self::VALUE => $roleData->getId(), 
				self::LABEL => $roleData->getRoleName()
			];
		}
        return $roleOptions;
    }
}