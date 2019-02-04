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
 */ ?>
<?php
class Apptha_Productfilters_Block_Catalog_Layer_View extends Mage_Catalog_Block_Layer_View
{
	/**
	 * Special Filter Block Name
	 *
	 * @var string
	 */
	protected $_specialFilterBlockName;
	
	/**
	 * Star Rating Filter Block Name
	 *
	 * @var string
	 */
	protected $_starRatingFilterBlockName;
	
	const SPECIAL_FILTER_POSITION = 2;
	const STARRATE_FILTER_POSITION = 3;

	/**
	 * Internal constructor
	 */
	protected function _construct()
	{
		parent::_construct();
	}
	protected function _initBlocks()
	{
		parent::_initBlocks();
		$this->_starRatingFilterBlockName	='productfilters/catalog_layer_filter_starrating';
		$this->_specialFilterBlockName		='productfilters/catalog_layer_filter_special';
			
	}
	protected function _prepareLayout()
	{
		if(!$this->helper('productfilters')->isEnabled()) {
			return parent::_prepareLayout();
		}
		$specialBlock = $this->getLayout()->createBlock($this->_specialFilterBlockName)
							->setLayer($this->getLayer())
							->init();  
		$starRateBlock = $this->getLayout()->createBlock($this->_starRatingFilterBlockName)
							->setLayer($this->getLayer())
							->init();    

	 	$this->setChild('special_filter', $specialBlock); 
	 	$this->setChild('starrating_filter', $starRateBlock);
	 	
	 	return parent::_prepareLayout();
	} 
	
	public function getFilters()
	{
		$filters = parent::getFilters();
		if(!$this->helper('productfilters')->isEnabled()) {
			return $filters;
		}
	 	if (($specialFilter = $this->_getSpecialFilter())) {
			// Insert special filter to the self::SPECIAL_FILTER_POSITION position
			$filters = array_merge(
					array_slice(
							$filters,
							0,
							self::SPECIAL_FILTER_POSITION - 1
					),
					array($specialFilter),
					array_slice(
							$filters,
							self::SPECIAL_FILTER_POSITION - 1,
							count($filters) - 1
					)
			);
		}  
		 
		 if (($starRateBlock = $this->_getStarFilter())) {
			// Insert special filter to the self::SPECIAL_FILTER_POSITION position
			$filters = array_merge(
					array_slice(
							$filters,
							0,
							self::STARRATE_FILTER_POSITION - 1
					),
					array($starRateBlock),
					array_slice(
							$filters,
							self::STARRATE_FILTER_POSITION - 1,
							count($filters) - 1
					)
			);
		} 
	 
		return $filters;
	}
	/**
	 * Get Special filter block
	 *
	 * @return Apptha_Productfilters_Block_Layer_Filter_Special
	 */
	  protected function _getSpecialFilter()
	{
		return $this->getChild('special_filter');
	}  
	
	/**
	 * Get Star Rate filter block
	 *
	 * @return Apptha_Productfilters_Block_Layer_Filter_Starrating
	 */
 	 protected function _getStarFilter()
	{
		return $this->getChild('starrating_filter');
	}
	  
}
?>
