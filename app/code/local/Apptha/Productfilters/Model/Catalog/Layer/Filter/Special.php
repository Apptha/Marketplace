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
 * @package    	Apptha_Advancedfilters
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */ ?>
<?php
class Apptha_Productfilters_Model_Catalog_Layer_Filter_Special extends Mage_Catalog_Model_Layer_Filter_Abstract
{
	const FILTER_ON_NEW = 1;
	const FILTER_ON_POPULAR = 2;
	/**
	 * Class constructor
	 */
	public function __construct()
	{	
		parent::__construct();
		$this->_requestVar = 'spec';
	}
	
	/**
	 * Apply Special filter to layer
	 *
	 * @param   Zend_Controller_Request_Abstract $request
	 * @param   Mage_Core_Block_Abstract $filterBlock
	 * @return  Apptha_Productfilters_Model_Catalog_Layer_Filter_Special
	 */
	public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
	{
		$filter = $request->getParam($this->getRequestVar());
		if (!$filter) 
		{
			return $this;
		}
		
		if ($filter == self::FILTER_ON_NEW) 
		{
			
			$stateLabel = Mage::helper('productfilters')->__('New Arrivals');
			$todayStartOfDayDate  = Mage::app()->getLocale()->date()
			->setTime('00:00:00')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
			
			$todayEndOfDayDate  = Mage::app()->getLocale()->date()
			->setTime('23:59:59')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
			
			/** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
			$this->getLayer()->getProductCollection()
		 ->addAttributeToFilter('news_from_date', array('or'=> array(
		 		0 => array('date' => true, 'to' => $todayEndOfDayDate),
		 		1 => array('is' => new Zend_Db_Expr('null')))
		 ), 'left')
		 ->addAttributeToFilter('news_to_date', array('or'=> array(
		 		0 => array('date' => true, 'from' => $todayStartOfDayDate),
		 		1 => array('is' => new Zend_Db_Expr('null')))
		 ), 'left')
		 ->addAttributeToFilter(
		 		array(
		 				array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
		 				array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
		 		)
		 );
		/*  ->addAttributeToSort('news_from_date', 'desc'); */
		}
		else if ($filter == self::FILTER_ON_POPULAR) 
		{
				$stateLabel = Mage::helper('productfilters')->__('Popular Products');
				$id = array();
				// get most viewed products for current category
				$products = Mage::getResourceModel('reports/product_collection')
								->addAttributeToSelect('*')
								->setStoreId(Mage::app()->getStore()->getId())
								->addStoreFilter(Mage::app()->getStore()->getId())
								->addViewsCount()
								->addCategoryFilter(Mage::registry('current_category'));
				foreach($products as $product) {
					array_push($id,$product->getData('entity_id'));
					//echo "ID: " . $product->getData('entity_id') . " - View Count: " . $product->getData('views') . "<br/>";
				}
			
				$ids = array_map('intval', $id);
				$collection = $this->getLayer()->getProductCollection()
							->addAttributeToFilter('entity_id', array('in' => $id,))
							->getSelect();
				
				if(!$request->getParam('order',false)){
					$collection->order("find_in_set(e.entity_id,'".implode(',',$ids)."')");
				}
				
		}
		
		$state = $this->_createItem($stateLabel, $filter)->setVar($this->_requestVar);
		/* @var $state Mage_Catalog_Model_Layer_Filter_Item */
		
		$this->getLayer()->getState()->addFilter($state);
		
		return $this;
	}
	
	/**
	 * Get filter name
	 *
	 * @return string
	 */
	public function getName()
	{
		return Mage::helper('catalog')->__('Shop by Special');
	}
	/**
	 * Get data array for building category filter items
	 *
	 * @return array
	 */
	protected function _getItemsData()
	{
		$data = array();
		
        $status = $this->_getCount();
 
        $data[] = array(
            'label' => Mage::helper('productfilters')->__('New Arrivals'),
            'value' => self::FILTER_ON_NEW,
            'count' => count($status['latest']),
        );
 
        $data[] = array(
            'label' => Mage::helper('productfilters')->__('Popular Products'),
            'value' => self::FILTER_ON_POPULAR,
            'count' => count($status['popular']),
        );
        return $data;
	}
	
	protected function _getCount()
	{
		$id = array();
		$collection = clone $this->getLayer()->getProductCollection();
		$count = $collection->getAllIds();
		// get most viewed products for current category
		// For Popular Products Collection Count
		$popularProducts = Mage::getResourceModel('reports/product_collection')
					        ->addAttributeToSelect('*')     
					        ->setStoreId(Mage::app()->getStore()->getId())
					        ->addStoreFilter(Mage::app()->getStore()->getId())
					        ->addViewsCount()
					        ->addCategoryFilter(Mage::registry('current_category'));
		if(Mage::getStoreConfig('cataloginventory/options/show_out_of_stock')=='0')
		{
			Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($popularProducts);
		}
		$id = $popularProducts->getAllIds();
		$result['popular'] = array_intersect($id, $count);
		
		//For New Arrivals Collection Count
	
		$todayStartOfDayDate  = Mage::app()->getLocale()->date()
		->setTime('00:00:00')
		->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
			
		$todayEndOfDayDate  = Mage::app()->getLocale()->date()
		->setTime('23:59:59')
		->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		$latestProducts = Mage::getResourceModel('reports/product_collection')
							->addAttributeToSelect('*')
							->setStoreId(Mage::app()->getStore()->getId())
							->addStoreFilter(Mage::app()->getStore()->getId())
							->addCategoryFilter(Mage::registry('current_category'))
							
							->addAttributeToFilter('news_from_date', array('or'=> array(
									0 => array('date' => true, 'to' => $todayEndOfDayDate),
									1 => array('is' => new Zend_Db_Expr('null')))
							), 'left')
							->addAttributeToFilter('news_to_date', array('or'=> array(
									0 => array('date' => true, 'from' => $todayStartOfDayDate),
									1 => array('is' => new Zend_Db_Expr('null')))
							), 'left')
							->addAttributeToFilter(
									array(
											array('attribute' => 'news_from_date', 'is'=>new Zend_Db_Expr('not null')),
											array('attribute' => 'news_to_date', 'is'=>new Zend_Db_Expr('not null'))
									)
							);
							/* ->addAttributeToSort('news_from_date', 'desc'); */
		if(Mage::getStoreConfig('cataloginventory/options/show_out_of_stock')=='0')
		{
			Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($latestProducts);
		}
		$id = $latestProducts->getAllIds();
		
		$result['latest'] = array_intersect($id, $count);
		
		return $result;
				
	}
}
?>