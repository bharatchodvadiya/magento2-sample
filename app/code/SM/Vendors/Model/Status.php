<?php
namespace SM\Vendors\Model\System\Config;
use Magento\Framework\Option\ArrayInterface;
 
class Status implements ArrayInterface
{
    const ENABLED  = 1;
    const DISABLED = 0;
    const ACTIVE = 'Active';
    const INACTIVE = 'Inactive';
 
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            self::ENABLED => __(self::ACTIVE),
            self::DISABLED => __(self::INACTIVE)
        ];
        return $options;
    }
}