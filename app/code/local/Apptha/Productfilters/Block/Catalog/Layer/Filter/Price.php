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
 */
//require_once 'Mobile_Detect.php';
class Apptha_Productfilters_Block_Catalog_Layer_Filter_Price extends Mage_Catalog_Block_Layer_Filter_Price
{

    /**
     * Class constructor
     * 
     * Set correct template depending on module state
     */
    public function __construct()
    {
        parent::__construct();
        
    	if(!$this->helper('productfilters')->isEnabled()) {
        	return;
        } 
        if ($this->helper('productfilters')->isPriceSliderEnabled()) {
        	
        	if ($this->helper('productfilters/mobile')->isMobile()) {
        		$this->setTemplate('productfilters/catalog/layer/custom.phtml');
        	}
        	else{
        	$this->setTemplate('productfilters/catalog/layer/price.phtml');
        	}
        }
         else {
        	$this->setTemplate('productfilters/catalog/layer/custom.phtml');
        	 
        } 
    }

    /**
     * Prepare filter process
     *
     * @return Mage_Catalog_Block_Layer_Filter_Price
     */
   
    /**
     * Get maximum price from layer products set
     *
     * @return float
     */
    public function getMaxPriceFloat()
    {
        return $this->_filter->getMaxPriceFloat();
    }

    /**
     * Get minimum price from layer products set
     *
     * @return float
     */
    public function getMinPriceFloat()
    {
        return $this->_filter->getMinPriceFloat();
    }

    /**
     * Get current minimum price filter
     * 
     * @return float
     */
    public function getCurrentMinPriceFilter()
    {
        list($from, $to) = $this->_filter->getInterval();
        
        $from = floor((float) $from);

        if ($from < $this->getMinPriceFloat()) {
            return $this->getMinPriceFloat();
        }

        return $from;
    }
    
    public function getFilterVar(){
    	
    	return $this->_filter->getRequestVar();
    }

    /**
     * Get current maximum price filter
     * 
     * @return float
     */
    public function getCurrentMaxPriceFilter()
    {
        list($from, $to) = $this->_filter->getInterval();
        $to = floor((float) $to);

        if ($to == 0 || $to > $this->getMaxPriceFloat()) {
            return $this->getMaxPriceFloat();
        }

        return $to;
        
    }

    /**
     * URL Pattern used in javascript for price filtering
     * 
     * @return string
     */
    public function getUrlPattern()
    {
        $item = Mage::getModel('catalog/layer_filter_item')
            ->setFilter($this->_filter)
            ->setValue('__PRICE_VALUE__')
            ->setCount(0);

        return $item->getUrl();
        
    }

    /**
     * Check if price slider can be rendered with a button
     * 
     * @return boolean
     */
    public function isSubmitTypeButton()
    {
        return false;
    }

    /**
     * Retrieve filter items count
     *
     * @return int
     */
    public function getItemsCount()
    {
    	if ($this->helper('productfilters')->isEnabled() && !$this->helper('productfilters/mobile')->isMobile() && $this->helper('productfilters')->isPriceSliderEnabled()){
    	return 1; // Keep price filter ON
    	
    	}
    	
        return parent::getItemsCount();
    }

}
