<?php
namespace SM\Vendors\Model\DeliveryArea\Attribute\Backend;

class PostCode extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
	const PRODUCT_DELIVERY_AREA = 'sm_product_delivery_area';
	const COMMA = ',';

	public function beforeSave($objectData) {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == self::PRODUCT_DELIVERY_AREA) {
            $deliveryData = $objectData->getData($attributeCode);
            if (!is_array($deliveryData)) {
                $deliveryData = array();
            }
            $objectData->setData($attributeCode, self::COMMA.join(self::COMMA, $deliveryData).self::COMMA);
        }
        return $this;
    }
 
    public function afterLoad($objectData) {
        $attributeCode = $this->getAttribute()->getName();
        if ($attributeCode == self::PRODUCT_DELIVERY_AREA) {
            $deliveryData = $objectData->getData($attributeCode);
            if ($deliveryData) {
                $objectData->setData($attributeCode, array_filter(explode(self::COMMA, $deliveryData)));
            }
        }
        return $this;
    }
}