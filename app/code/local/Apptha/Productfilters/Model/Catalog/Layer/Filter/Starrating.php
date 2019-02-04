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
class Apptha_Productfilters_Model_Catalog_Layer_Filter_Starrating extends Mage_Catalog_Model_Layer_Filter_Abstract
{
	const FILTER_ON_ONE_STAR = 1;
	const FILTER_ON_TWO_STAR = 2;
	const FILTER_ON_THREE_STAR = 3;
	const FILTER_ON_FOUR_STAR = 4;
	//const FILTER_ON_NO_STAR = -1;
	
	/**
	 * Applied Category
	 *
	 * @var Mage_Catalog_Model_Category
	 */
	protected $_appliedCategory = null;
	
	/**
	 * Class constructor
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->_requestVar = 'star';
	}
	
	/**
	 * Apply Star rating filter to layer
	 *
	 * @param   Zend_Controller_Request_Abstract $request
	 * @param   Mage_Core_Block_Abstract $filterBlock
	 * @return  Apptha_Productfilters_Model_Catalog_Layer_Filter_Starrating
	 */
	public function apply(Zend_Controller_Request_Abstract $request, $filterBlock)
	{
		$filter = $request->getParam($this->getRequestVar());
		
		if (!$filter) {
			return $this;
		}
		
		$id = array();
		$_products = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->addStoreFilter();
		
		foreach($_products as $_product) {
			
			$_productRating = Mage::getModel('review/review_summary')
								->setStoreId(Mage::app()->getStore()->getId())
								->load($_product->getId());
			
			/* if ($filter == self::FILTER_ON_NO_STAR)
			{
				$stateLabel = Mage::helper('productfilters')->__('No Stars');
				if((!isset($_productRating['rating_summary'])) || ($_productRating['rating_summary'] == 0))
				{
					array_push($id, $_product->getId());
				}	
			}
			else */
			if ($filter == self::FILTER_ON_ONE_STAR)
			{
				$stateLabel = Mage::helper('productfilters')->__('1 Star & Up');
				if (0 < $_productRating['rating_summary'])
				{
					array_push($id, $_product->getId());
				}
			}
			elseif ($filter == self::FILTER_ON_TWO_STAR)
			{
				$stateLabel = Mage::helper('productfilters')->__('2 Stars & Up');
				if (20 < $_productRating['rating_summary'])
				{
					array_push($id, $_product->getId());
				}
			
			}
			elseif ($filter == self::FILTER_ON_THREE_STAR)
			{
				$stateLabel = Mage::helper('productfilters')->__('3 Stars & Up');
				if (40 < $_productRating['rating_summary'])
				{
					array_push($id, $_product->getId());
				}
			}
			elseif ($filter == self::FILTER_ON_FOUR_STAR)
			{
				$stateLabel = Mage::helper('productfilters')->__('4 Stars & Up');
				if (60 < $_productRating['rating_summary'])
				{
					array_push($id, $_product->getId());
				}
			}
			
		}
		$this->getLayer()->getProductCollection()->addAttributeToFilter('entity_id', array(
				'in' => $id,
		));
		
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
		return Mage::helper('catalog')->__('Customer Review');
	}
	/**
	 * Get data array for building category filter items
	 *
	 * @return array
	 */
	protected function _getItemsData()
	{
		$data = array(); 
		//$nostar = array();
		$star1 = array();
		$star2 = array();
		$star3 = array();
		$star4 = array();
		
		$_products = clone $this->getLayer()->getProductCollection();
		
		foreach($_products as $_product)
		{
			$_productRating = Mage::getModel('review/review_summary')
								->setStoreId(Mage::app()->getStore()->getId())
								->load($_product->getId());
			
			//if((!isset($_productRating['rating_summary'])) || ($_productRating['rating_summary'] == 0))
				//array_push($nostar, $_product->getId());
			if ((0 < $_productRating['rating_summary']))
				array_push($star1, $_product->getId());
			if ((20 < $_productRating['rating_summary']))
				array_push($star2, $_product->getId());
			if ((40 < $_productRating['rating_summary']))
				array_push($star3, $_product->getId());
			if ((60 < $_productRating['rating_summary']))
				array_push($star4, $_product->getId());
		}
		
		$data[] = array(
				'label' => Mage::helper('productfilters')->__('**** & Up'),
				'value' => self::FILTER_ON_FOUR_STAR,
				'count' => count($star4),
		);
		$data[] = array(
				'label' => Mage::helper('productfilters')->__('*** & Up'),
				'value' => self::FILTER_ON_THREE_STAR,
				'count' => count($star3),
		);
		$data[] = array(
				'label' => Mage::helper('productfilters')->__('** & Up'),
				'value' => self::FILTER_ON_TWO_STAR,
				'count' => count($star2),
		);
		$data[] = array(
				'label' => Mage::helper('productfilters')->__('* & Up'),
				'value' => self::FILTER_ON_ONE_STAR,
				'count' => count($star1),
		);
		/* $data[] = array(
				'label' => Mage::helper('productfilters')->__('No Rating'),
				'value' => self::FILTER_ON_NO_STAR,
				'count' => count($nostar),
		); */
		return $data;
	}
	
}
