<?php
namespace SM\Vendors\Ui\DataProvider\Product;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class AddVendorFilterToCollection extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{
    const VENDOR_ID = 'vendor_id';
    const SM_VENDOR_ID = 'sm_product_vendor_id';
    const TOTAL_RECORDS = 'totalRecords';
    const ITEMS = 'items';

    protected $_helperData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \SM\Vendors\Helper\Data $helperData,
        array $addFieldStrategies = [],
        array $addFilterStrategies = [],
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $addFieldStrategies, $addFilterStrategies, $meta, $data);
        $this->_helperData = $helperData;
    }

	public function getData()
    {
        if (!$this->getCollection()->isLoaded()) {
            $this->getCollection()->load();
        }
        $productItems = $this->getCollection()->toArray();
        $totalRecords = $this->getCollection()->getSize();
        if($vendorData = $this->_helperData->getVendorLogin()) {
            $newItems = [];
            foreach($productItems as $itemData) {
                if($itemData[self::SM_VENDOR_ID] == $vendorData[self::VENDOR_ID]) {
                    $newItems[] = $itemData;
                }
            }
            $totalRecords = count($newItems);
            $productItems = $newItems;

        }
        return [
            self::TOTAL_RECORDS => $totalRecords,
            self::ITEMS => array_values($productItems),
        ];
    }
}