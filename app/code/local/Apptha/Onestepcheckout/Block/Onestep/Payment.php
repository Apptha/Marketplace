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
/**
 * Manage one step checkout payment information
 */
class Apptha_Onestepcheckout_Block_Onestep_Payment extends Mage_Checkout_Block_Onepage_Payment {
    
    /**
     * default class construct function
     *
     * @see Mage_Checkout_Block_Onepage_Payment::_construct()
     */
    protected function _construct() {
        $this->getCheckout ()->setStepData ( 'payment', array (
                'label' => $this->__ ( 'Payment Information' ),
                'is_show' => $this->isShow () 
        ) );
        parent::_construct ();
    }
    
    /**
     * Get base grand total of the cart
     *
     * @return float
     */
    public function getQuoteBaseGrandTotal() {
        return ( float ) $this->getQuote ()->getBaseGrandTotal ();
    }
    /**
     * get onepage detail function
     *
     * @return Ambigous <Mage_Core_Model_Abstract, mixed, NULL, multitype:>
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
