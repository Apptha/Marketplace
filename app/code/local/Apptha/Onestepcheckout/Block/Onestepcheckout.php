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
 * @package     Apptha_Marketplace
 * @version     1.9.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
class Apptha_Onestepcheckout_Block_Onestepcheckout extends Mage_Checkout_Block_Onepage_Abstract {
    /**
     * get default country and set estimate rates
     *
     * @return void
     */
    public function _construct() {
        parent::_construct ();
        $defaut_country = Mage::getStoreConfig ( 'onestepcheckout/general/default_country_id' );
        /**
         * check condition default country is not empty
         */
        if (! $defaut_country) {
            $defaut_country = 'US';
        }
    }
    /**
     * Get all shipping rates
     *
     * @return int
     */
    public function getEstimateRates() {
        /**
         * check condition rates is empty
         */
        if (empty ( $this->_rates )) {
            $groups = $this->getQuote ()->getShippingAddress ()->getGroupedAllShippingRates ();
            $this->_rates = $groups;
        }
        return $this->_rates;
    }
    public function _prepareLayout() {
        $title = Mage::getStoreConfig ( 'onestepcheckout/general/checkout_title' );
        /**
         * check codndition title is not empty
         */
        if ($title) {
            $checkoutTitle = $title;
        } else {
            $checkoutTitle = "Onestep Checkout";
        }
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $checkoutTitle );
        return parent::_prepareLayout ();
    }
    /**
     * Get shipping methods
     *
     * @return bool
     */
    public function shippingmethods($shipping, $methods) {
        /**
         * check condition shipping method and method is not empty
         */
        if (($shipping) && ($methods)) {
            return true;
        }
    }
    /**
     * Getting steps based on the product
     *
     * @return int
     */
    public function getSteps() {
        $steps = array ();
        
        /**
         * steps for virtual product
         */
        if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
            $stepCodes = array (
                    'billing',
                    'payment',
                    'review' 
            );
        } /**
         * steps for other product
         */
        else {
            $stepCodes = array (
                    'billing',
                    'shipping',
                    'shipping_method',
                    'payment',
                    'review' 
            );
        }
        
        foreach ( $stepCodes as $step ) {
            
            $steps [$step] = $this->getCheckout ()->getStepData ( $step );
        }
        
        return $steps;
    }
    
    /**
     * check the active step
     *
     * @return string
     */
    public function getActiveStep() {
        return $this->isCustomerLoggedIn () ? 'billing' : 'login';
    }
    /**
     * Get the checkout url
     *
     * @return string
     */
    public function getOnepage() {
        return Mage::getSingleton ( 'checkout/type_onepage' );
    }
    
    /**
     * Get product is virtual product or not
     *
     * @return bool
     */
    public function getVirtual() {
        $returnValue = '';
        /**
         * check condition virtual value is not empty
         */
        if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
            $returnValue = true;
        } else {
            $returnValue = false;
        }
        return $returnValue;
    }
}