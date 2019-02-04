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
 * Shipping info management
 * This class in used to manage the shipping information of one step checkout
 */
class Apptha_Onestepcheckout_Block_Onestep_Shipping extends Mage_Checkout_Block_Onepage_Shipping {
    /**
     * Sales Quote Shipping Address instance
     *
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address = null;
    
    /**
     * Initialize shipping address step
     */
    protected function _construct() {
        $this->getCheckout ()->setStepData ( 'shipping', array (
                'label' => Mage::helper ( 'checkout' )->__ ( 'Shipping Information' ),
                'is_show' => $this->isShow () 
        ) );
        $this->settings = Mage::helper ( 'onestepcheckout/checkout' )->loadSettings ();
        parent::_construct ();
    }
    
    /**
     * Return checkout method
     *
     * @return string
     */
    public function getMethod() {
        return $this->getQuote ()->getCheckoutMethod ();
    }
    
    /**
     * Return Sales Quote Address model (shipping address)
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress() {
        /**
         * Check whether shipping address is null
         */
        if (is_null ( $this->_address )) {
            /**
             * Check the customer is logged in
             */
            if ($this->isCustomerLoggedIn ()) {
                $this->_address = $this->getQuote ()->getShippingAddress ();
            } else {
                $this->_address = Mage::getModel ( 'sales/quote_address' );
            }
        }
        
        return $this->_address;
    }
    
    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow() {
        return ! $this->getQuote ()->isVirtual ();
    }
    public function getCountryHtmlSelect($type) {
        $helper = Mage::helper ( 'onestepcheckout/checkout' );
        /**
         * Check default country id has been set up settings
         */
        if (isset ( $this->settings ['default_country_id'] )) {
            /**
             * Check geoip has been enabled in settings
             */
            if ($this->settings ['enable_geoip'] == 1) {
                /**
                 * Check Customer logged in status is equal to 1
                 */
                if (Mage::helper ( 'customer' )->isLoggedIn () == 1) {
                    $countryIdData = $this->getCountryCodeData ();
                } else {
                    $countryIdData = $helper->getGeoIp ()->countryCode;
                }
            } else {
                /**
                 * Checking customer is logged in already
                 */
                if (Mage::helper ( 'customer' )->isLoggedIn () == 1) {
                    $countryIdData = $this->getCountryDetail ();
                } else {
                    $countryIdData = $this->settings ['default_country_id'];
                }
            }
        }
        /**
         * Check country id is null
         */
        if (is_null ( $countryIdData )) {
            $countryIdData = $this->settings ['default_country_id'];
        }
        $select = $this->getLayout ()->createBlock ( 'core/html_select' )->setName ( $type . '[country_id]' )->setId ( $type . ':country_id' )->setTitle ( Mage::helper ( 'checkout' )->__ ( 'Country' ) )->setClass ( 'validate-select' )->setValue ( $countryIdData )->setOptions ( $this->getCountryOptions () );
        /**
         * Checking the type is equal to shipping
         */
        if ($type === 'shipping') {
            $select->setExtraParams ( 'onchange="shipping.setSameAsBilling(false);"' );
        }
        
        return $select->getHtml ();
    }
    /**
     * get country data
     */
    public function getCountryDetail() {
        $customerAddressId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getDefaultShipping ();
        if ($customerAddressId) {
            $addressData = Mage::getModel ( 'customer/address' )->load ( $customerAddressId );
            return $addressData ['country_id'];
        } else {
            return $this->settings ['default_country_id'];
        }
    }
    
    /**
     * function to get country code
     */
    public function getCountryCodeData() {
        $customeraddressId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getDefaultBilling ();
        $helper = Mage::helper ( 'onestepcheckout/checkout' );
        /**
         * check condition customer address id is equal to not empty
         */
        if ($customeraddressId) {
            $address = Mage::getModel ( 'customer/address' )->load ( $customeraddressId );
            return $address ['country_id'];
        } else {
            return $helper->getGeoIp ()->countryCode;
        }
    }
}
