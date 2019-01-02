<?php
namespace SM\Vendors\Model\ResourceModel\Group\Grid;

use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SimpleDataObjectConverter;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Customer\Api\GroupRepositoryInterface;

class ServiceCollection extends \Magento\Customer\Model\ResourceModel\Group\Grid\ServiceCollection
{
    const VENDOR_ID = 'vendor_id';
    const VENDOR_NAME = 'vendor_name';

	protected $_helperData;

	protected $_vendorData;

	public function __construct(
        EntityFactory $entityFactory,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        GroupRepositoryInterface $groupRepository,
        SimpleDataObjectConverter $simpleDataObjectConverter,
        \SM\Vendors\Helper\Data $helperData,
        \SM\Vendors\Model\Vendor $vendorData
    ) {
        parent::__construct($entityFactory, $filterBuilder, $searchCriteriaBuilder, $sortOrderBuilder, $groupRepository, $simpleDataObjectConverter);
        $this->_helperData = $helperData;
        $this->_vendorData = $vendorData;
    }

	public function loadData($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $searchCriteria = $this->getSearchCriteria();
            $searchResults = $this->groupRepository->getList($searchCriteria);
            $this->_totalRecords = $searchResults->getTotalCount();
            $groups = $searchResults->getItems();
            foreach ($groups as $group) {
                $groupItem = new \Magento\Framework\DataObject();
                $groupItem->addData($this->simpleDataObjectConverter->toFlatArray($group, '\Magento\Customer\Api\Data\GroupInterface'));
                $groupId = $groupItem->getId();
                $vendorValue = $this->_vendorData->getVendorByGroupId($groupId);
                $groupItem->setData(self::VENDOR_ID, $vendorValue[self::VENDOR_ID]);
                $groupItem->setData(self::VENDOR_NAME, $vendorValue[self::VENDOR_NAME]);
                if($vendorData = $this->_helperData->getVendorLogin()) {
                    if($vendorData[self::VENDOR_ID] == $vendorValue[self::VENDOR_ID]) {
                        $this->_addItem($groupItem);
                    }
                }
                else {
                	$this->_addItem($groupItem);
                }
            }
            $this->_setIsLoaded();
        }
        return $this;
    }
}