<?php
namespace SM\Vendors\Model\Customer\Attribute\Source;

class Type extends \Magento\Eav\Model\Entity\Attribute\Source\Table
{
	public function getAllOptions()
    {
    	$optionsData = [
			[
				'value'=> 'vendor',
				'label'=> __('Vendor')
			],
			[
				'value'=> 'buyer',
				'label'=> __('Buyer')
			],
		];
        return $optionsData;
    }
}