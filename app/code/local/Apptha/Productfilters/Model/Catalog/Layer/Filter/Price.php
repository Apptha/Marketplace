<?php
/**
 * Apptha
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.apptha.com/LICENSE.txt
 *
 * ==============================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * ==============================================================
 * This package designed for Magento COMMUNITY edition
 * Apptha does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * Apptha does not provide extension support in case of
 * incorrect edition usage.
 * ==============================================================
 *
 * @category    Apptha
 * @package     Apptha_Advancedfilters
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
class Apptha_Productfilters_Model_Catalog_Layer_Filter_Price extends Mage_Catalog_Model_Layer_Filter_Price
{
	/**
     * Get maximum price from layer products set
     *
     * @return float
     */
    public function getMaxPriceFloat()
    {
        if (!$this->hasData('max_price_float')) {
            $this->_collectPriceRange();
        }

        return $this->getData('max_price_float');
    }

    /**
     * Get minimum price from layer products set
     *
     * @return float
     */
    public function getMinPriceFloat()
    {
        if (!$this->hasData('min_price_float')) {
            $this->_collectPriceRange();
        }

        return $this->getData('min_price_float');
    }
    
    /**
     * Collect usefull information - max and min price
     * 
     * @return Apptha_Productfilters_Model_Catalog_Layer_Filter_Price
     */
    protected function _collectPriceRange()
    {
        $collection = $this->getLayer()->getProductCollection()->addAttributeToSelect('*')->addStoreFilter();
        $select = $collection->getSelect();
        $conditions = $select->getPart(Zend_Db_Select::WHERE);
        
        // Remove price sql conditions
        $conditionsNoPrice = array();
        foreach ($conditions as $key => $condition) {
        	
            if (stripos($condition, 'price_index') !== false) {
                continue;
            }
            $conditionsNoPrice[] = $condition;
            
        }        
        $select->setPart(Zend_Db_Select::WHERE, $conditionsNoPrice);
        
        $this->setData('min_price_float', ($collection->getMinPrice()));
        
        $this->setData('max_price_float', ($collection->getMaxPrice()));
        
        // Restore all sql conditions
        $select->setPart(Zend_Db_Select::WHERE, $conditions);
        
        return $this;
    }
    /**
     * Prepare text of item label
     *
     * @deprecated since 1.7.0.0
     * @param   int $range
     * @param   float $value
     * @return  string
     */
    protected function _renderItemLabel($range, $value)
    {
    	$store      = Mage::app()->getStore();
    	$fromPrice  = $store->formatPrice(($value - 1) * $range);
    	$toPrice    = $store->formatPrice($value*$range);
    
    	return Mage::helper('catalog')->__('%s - %s', $fromPrice, $toPrice);
    }
    
    /**
     * Apply price range filter
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param $filterBlock
     *
     * @return Mage_Catalog_Model_Layer_Filter_Price
     */
    public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
    {
    	/**
    	 * Filter must be string: $fromPrice-$toPrice
    	 */
    	$filter = $request->getParam($this->getRequestVar());
    	if (!$filter) {
    		return $this;
    	}
    
    	//validate filter
    	$filterParams = explode(',', $filter);
    	$filter = $this->_validateFilter($filterParams[0]);
    	if (!$filter) {
    		return $this;
    	}
    	
    
    	list($from, $to) = $filter;
    	if(Mage::helper('productfilters')->isPriceSliderEnabled() && (!Mage::helper('productfilters/mobile')->isMobile()) )
    	{
    		$to += Mage_Catalog_Model_Resource_Layer_Filter_Price::MIN_POSSIBLE_PRICE;
    	}
    	$this->setInterval(array($from, $to));
    
    	$priorFilters = array();
    	for ($i = 1; $i < count($filterParams); ++$i) {
    		$priorFilter = $this->_validateFilter($filterParams[$i]);
    		if ($priorFilter) {
    			$priorFilters[] = $priorFilter;
    		} else {
    			//not valid data
    			$priorFilters = array();
    			break;
    		}
    	}
    	if ($priorFilters) {
    		$this->setPriorIntervals($priorFilters);
    	}
    
    	$this->_applyPriceRange();
    	$this->getLayer()->getState()->addFilter($this->_createItem(
    			$this->_renderRangeLabel(empty($from) ? 0 : $from, $to),
    			$filter
    	));
    
    	return $this;
    }
    protected function _getItemsData()
    {
    	if(!Mage::helper('productfilters')->isEnabled()) {
    		return parent::_getItemsData();
    	}
    	if (Mage::app()->getStore()->getConfig(self::XML_PATH_RANGE_CALCULATION) == self::RANGE_CALCULATION_IMPROVED) {
           // return $this->_getCalculatedItemsData();
        } elseif ($this->getInterval()) {
            //return array();
        }

        $range      = $this->getPriceRange();
        $dbRanges   = $this->getRangeItemCounts($range);
        $data       = array();

        if (!empty($dbRanges)) {
            $lastIndex = array_keys($dbRanges);
            $lastIndex = $lastIndex[count($lastIndex) - 1];

            foreach ($dbRanges as $index => $count) {
                $fromPrice = ($index == 1) ? '' : (($index - 1) * $range);
                $toPrice = ($index == $lastIndex) ? '' : ($index * $range);

                $data[] = array(
                    'label' => $this->_renderRangeLabel($fromPrice, $toPrice),
                    'value' => $fromPrice . '-' . $toPrice,
                    'count' => $count,
                );
            }
        }

        return $data;
    }
    
}
