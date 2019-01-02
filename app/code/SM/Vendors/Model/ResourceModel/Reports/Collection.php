<?php
namespace SM\Vendors\Model\ResourceModel\Reports\Sales\Product\Bestsellers;

class Collection extends \Magento\Sales\Model\ResourceModel\Report\Bestsellers\Collection
{
	const SM_HELPER = '\SM\Vendors\Helper\Data';
	const VENDOR_ID = 'vendor_id';

	protected function _getSelectedColumns()
    {
        $connection = $this->getConnection();
        if (!$this->_selectedColumns) {
            if ($this->isTotals()) {
                $this->_selectedColumns = $this->getAggregatedColumns();
            } else {
                $this->_selectedColumns = [
                    'period' => sprintf('MAX(%s)', $connection->getDateFormatSql('period', '%Y-%m-%d')),
                    $this->getOrderedField() => 'SUM(' . $this->getOrderedField() . ')',
                    'product_id' => 'product_id',
                    'product_name' => 'MAX(product_name)',
                    'product_price' => 'MAX(product_price)',
                    'vendor_id' => 'product_vendor_id.value',
                ];
                if ('year' == $this->_period) {
                    $this->_selectedColumns['period'] = $connection->getDateFormatSql('period', '%Y');
                } elseif ('month' == $this->_period) {
                    $this->_selectedColumns['period'] = $connection->getDateFormatSql('period', '%Y-%m');
                }
            }
        }
        return $this->_selectedColumns;
    }

    protected function _initSelect()
    {
    	$select = $this->getSelect();
    	if (!$this->_period) {
    		$cols = $this->_getSelectedColumns();
    		$cols['qty_ordered'] = 'SUM(qty_ordered)';
    		if ($this->_from || $this->_to) {
                $mainTable = $this->getTable($this->getTableByAggregationPeriod('daily'));
                $select->from($mainTable, $cols);
            } else {
                $mainTable = $this->getTable($this->getTableByAggregationPeriod('yearly'));
                $select->from($mainTable, $cols);
            }
            $subSelect = $this->getConnection()->select();
            $subSelect->from(['existed_products' => $this->getTable('catalog_product')], new \Zend_Db_Expr('1)'));

            $select->exists($subSelect, $mainTable . '.product_id = existed_products.entity_id')
                ->group('product_id')
                ->order('qty_ordered ' . \Magento\Framework\DB\Select::SQL_DESC)
                ->limit($this->_ratingLimit);
            return $this;
    	}
    	if ('year' == $this->_period) {
            $mainTable = $this->getTable($this->getTableByAggregationPeriod('yearly'));
            $select->from($mainTable, $this->_getSelectedColumns());
        } elseif ('month' == $this->_period) {
            $mainTable = $this->getTable($this->getTableByAggregationPeriod('monthly'));
            $select->from($mainTable, $this->_getSelectedColumns());
        } else {
            $mainTable = $this->getTable($this->getTableByAggregationPeriod('daily'));
            $select->from($mainTable, $this->_getSelectedColumns());
        }
        if (!$this->isTotals()) {
            $select->group(['period', 'product_id']);
        }
        $select->where('rating_pos <= ?', $this->_ratingLimit);
        return $this;
    }

    protected function _beforeLoad()
    {
    	parent::_beforeLoad();
        $this->_applyStoresFilter();
        $this->_applyDateRangeFilter();
        if ($this->_period) {
            $selectUnions = [];
            $periodFrom = !is_null($this->_from) ? new \DateTime($this->_from) : null;
            $periodTo = !is_null($this->_to) ? new \DateTime($this->_to) : null;
            if ('year' == $this->_period) {
            	if ($periodFrom) {
                    // not the first day of the year
                    if ($periodFrom->format('m') != 1 || $periodFrom->format('d') != 1) {
                        $dtFrom = clone $periodFrom;
                        // last day of the year
                        $dtTo = clone $periodFrom;
                        $dtTo->setDate($dtTo->format('Y'), 12, 31);
                        if (!$periodTo || $dtTo < $periodTo) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->format('Y-m-d'),
                                $dtTo->format('Y-m-d')
                            );

                            // first day of the next year
                            $this->_from = clone $periodFrom;
                            $this->_from->modify('+1 year');
                            $this->_from->setDate($this->_from->format('Y'), 1, 1);
                            $this->_from = $this->_from->format('Y-m-d');
                        }
                    }
                }
	            if ($periodTo) {
	                // not the last day of the year
	                if ($periodTo->format('m') != 12 || $periodTo->format('d') != 31) {
	                    $dtFrom = clone $periodTo;
	                    $dtFrom->setDate($dtFrom->format('Y'), 1, 1);
	                    // first day of the year
	                    $dtTo = clone $periodTo;
	                    if (!$periodFrom || $dtFrom > $periodFrom) {
	                        $selectUnions[] = $this->_makeBoundarySelect(
	                            $dtFrom->format('Y-m-d'),
	                            $dtTo->format('Y-m-d')
	                        );
	                        // last day of the previous year
	                        $this->_to = clone $periodTo;
	                        $this->_to->modify('-1 year');
	                        $this->_to->setDate($this->_to->format('Y'), 12, 31);
	                        $this->_to = $this->_to->format('Y-m-d');
	                    }
	                }
	            }
	            if ($periodFrom && $periodTo) {
	                // the same year
	                if ($periodTo->format('Y') == $periodFrom->format('Y')) {
	                    $dtFrom = clone $periodFrom;
	                    $dtTo = clone $periodTo;
	                    $selectUnions[] = $this->_makeBoundarySelect(
	                        $dtFrom->format('Y-m-d'),
	                        $dtTo->format('Y-m-d')
	                    );

	                    $this->getSelect()->where('1<>1');
	                }
	            }
	        }
	        else if ('month' == $this->_period) {
	        	if ($periodFrom) {
                    // not the first day of the month
                    if ($periodFrom->format('d') != 1) {
                        $dtFrom = clone $periodFrom;
                        // last day of the month
                        $dtTo = clone $periodFrom;
                        $dtTo->modify('+1 month');
                        $dtTo->setDate($dtTo->format('Y'), $dtTo->format('m'), 1);
                        $dtTo->modify('-1 day');
                        if (!$periodTo || $dtTo < $periodTo) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->format('Y-m-d'),
                                $dtTo->format('Y-m-d')
                            );
                            // first day of the next month
                            $this->_from = clone $periodFrom;
                            $this->_from->modify('+1 month');
                            $this->_from->setDate($this->_from->format('Y'), $this->_from->format('m'), 1);
                            $this->_from = $this->_from->format('Y-m-d');
                        }
                    }
                }
                if ($periodTo) {
                    // not the last day of the month
                    if ($periodTo->format('d') != $periodTo->format('t')) {
                        $dtFrom = clone $periodTo;
                        $dtFrom->setDate($dtFrom->format('Y'), $dtFrom->format('m'), 1);
                        // first day of the month
                        $dtTo = clone $periodTo;
                        if (!$periodFrom || $dtFrom > $periodFrom) {
                            $selectUnions[] = $this->_makeBoundarySelect(
                                $dtFrom->format('Y-m-d'),
                                $dtTo->format('Y-m-d')
                            );
                            // last day of the previous month
                            $this->_to = clone $periodTo;
                            $this->_to->setDate($this->_to->format('Y'), $this->_to->format('m'), 1);
                            $this->_to->modify('-1 day');
                            $this->_to = $this->_to->format('Y-m-d');
                        }
                    }
                }
                if ($periodFrom && $periodTo) {
                    // the same month
                    if ($periodTo->format('Y') == $periodFrom->format('Y') &&
                        $periodTo->format('m') == $periodFrom->format('m')
                    ) {
                        $dtFrom = clone $periodFrom;
                        $dtTo = clone $periodTo;
                        $selectUnions[] = $this->_makeBoundarySelect(
                            $dtFrom->format('Y-m-d'),
                            $dtTo->format('Y-m-d')
                        );
                        $this->getSelect()->where('1<>1');
                    }
                }
	        }
	        if ($selectUnions) {
                $unionParts = [];
                $cloneSelect = clone $this->getSelect();
                $unionParts[] = '(' . $cloneSelect . ')';
                foreach ($selectUnions as $union) {
                    $unionParts[] = '(' . $union . ')';
                }
                $this->getSelect()->reset()->union($unionParts, \Magento\Framework\DB\Select::SQL_UNION_ALL);
            }
            if ($this->isTotals()) {
                // calculate total
                $cloneSelect = clone $this->getSelect();
                $this->getSelect()->reset()->from($cloneSelect, $this->getAggregatedColumns());
            } else {
                // add sorting
                $this->getSelect()->order(['period ASC', $this->getOrderedField() . ' DESC']);
            }
        }
        $connection = $this->getConnection();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productResource = $objectManager->get('Magento\Catalog\Model\ResourceModel\Product');
        $productVendorAttribute = $productResource->getResource()->getAttribute('sm_product_vendor_id');
        $salesTable = $this->getTable($this->getTableByAggregationPeriod('yearly'));
        $joinExprProductVendorId = [
            'product_vendor_id.entity_id = '.$salesTable.'.product_id',
            'product_vendor_id.store_id = '.$salesTable.'.store_id',
            $connection->quoteInto('product_vendor_id.entity_type_id = ?', $productResource->getTypeId()),
            $connection->quoteInto('product_vendor_id.attribute_id = ?', $productVendorAttribute->getAttributeId())
        ];
        $joinExprProductVendorId = implode(' AND ', $joinExprProductVendorId);
        $joinExprProductDefaultVendorId = array(
            'product_default_vendor_id.entity_id = '.$salesTable.'.product_id',
            'product_default_vendor_id.store_id = 0',
            $connection->quoteInto('product_default_vendor_id.entity_type_id = ?', $productResource->getTypeId()),
            $connection->quoteInto('product_default_vendor_id.attribute_id = ?', $productVendorAttribute->getAttributeId())
        );
        $joinExprProductDefaultVendorId = implode(' AND ', $joinExprProductDefaultVendorId);
        $this->getSelect()->joinLeft(['product_vendor_id' => $productVendorAttribute->getBackend()->getTable()], $joinExprProductVendorId, []
        )
        ->joinLeft(
            ['product_default_vendor_id' => $productVendorAttribute->getBackend()->getTable()], $joinExprProductDefaultVendorId, []
        );
    	$helperData = $objectManager->create(self::SM_HELPER);
    	if($vendorData = $helperData->getVendorLogin()) {
    		$this->getSelect()->where('product_vendor_id.value = ?', $vendorData[self::VENDOR_ID]);
    	}
        return $this;
    }
}