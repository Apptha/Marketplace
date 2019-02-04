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
class Apptha_Onestepcheckout_Helper_Checkout extends Mage_Core_Helper_Abstract {
    public $methods = array ();
    
    /**
     * Save payment detail
     */
    public function savePayment($data) {
        /**
         * check condition if data is empty
         */
        if (empty ( $data )) {
            return array (
                    'error' => - 1,
                    'message' => Mage::helper ( 'checkout' )->__ ( 'Invalid data' ) 
            );
        }
        /**
         * check condition virtual data is not empty
         */
        if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
            $this->getOnepage ()->getQuote ()->getBillingAddress ()->setPaymentMethod ( isset ( $data ['method'] ) ? $data ['method'] : null );
        } else {
            $this->getOnepage ()->getQuote ()->getShippingAddress ()->setPaymentMethod ( isset ( $data ['method'] ) ? $data ['method'] : null );
        }
        
        $payment = $this->getOnepage ()->getQuote ()->getPayment ();
        $payment->importData ( $data );
        
        $this->getOnepage ()->getQuote ()->save ();
        
        return array ();
    }
    
    /**
     * Save shipping method detail
     *
     * @param unknown $shippingMethod            
     * @return multitype:number Ambigous <string, string, multitype:> |multitype:
     */
    public function saveShippingMethod($shippingMethod) {
        
        /**
         * check condition
         * if shipping method is empty means return error message
         */
        if (empty ( $shippingMethod )) {
            return array (
                    'error' => - 1,
                    'message' => Mage::helper ( 'checkout' )->__ ( 'Invalid shipping method.' ) 
            );
        }
        $rate = $this->getOnepage ()->getQuote ()->getShippingAddress ()->getShippingRateByCode ( $shippingMethod );
        
        /**
         * check condition
         * if rate is empty means return error message
         */
        if (! $rate) {
            return array (
                    'error' => - 1,
                    'message' => Mage::helper ( 'checkout' )->__ ( 'Invalid shipping method.' ) 
            );
        }
        $this->getOnepage ()->getQuote ()->getShippingAddress ()->setShippingMethod ( $shippingMethod );
        
        return array ();
    }
    
    /**
     * Save shipping date into database table
     *
     * @param array $data            
     * @param
     *            $customerAddressId
     * @return multitype:number Ambigous <string, string, multitype:> |multitype:number unknown |multitype:
     */
    public function saveShipping($data, $customerAddressId) {
        
        /**
         * check condition
         * if data is empty means return error message to customer
         */
        if (empty ( $data )) {
            return array (
                    'error' => - 1,
                    'message' => Mage::helper ( 'checkout' )->__ ( 'Invalid data' ) 
            );
        }
        $address = $this->getOnepage ()->getQuote ()->getShippingAddress ();
        /**
         * check condition customer address data is not empty
         */
        if (! empty ( $customerAddressId )) {
            $customerAddress = Mage::getModel ( 'customer/address' )->load ( $customerAddressId );
            /**
             * check condition
             * customer address id is not empty
             */
            if ($customerAddress->getId ()) {
                /**
                 * check condition
                 * current customer id is not equal to onepage customer id
                 */
                if ($customerAddress->getCustomerId () != $this->getOnepage ()->getQuote ()->getCustomerId ()) {
                    return array (
                            'error' => 1,
                            'message' => Mage::helper ( 'checkout' )->__ ( 'Customer Address is not valid.' ) 
                    );
                }
                $address->importCustomerAddress ( $customerAddress );
            }
        } else {
            unset ( $data ['address_id'] );
            $address->addData ( $data );
        }
        
        $address->implodeStreetAddress ();
        $address->setCollectShippingRates ( true );
        /**
         * check condition address validate is false
         */
        
        if (($validateRes = $address->validate ()) !== true) {
            return array (
                    'error' => 1,
                    'message' => $validateRes 
            );
        }
        
        $this->getOnepage ()->getQuote ()->save ();
    }
    
    /**
     * default class constructior function
     */
    function __construct() {
        $this->settings = $this->loadSettings ();
    }
    
    /**
     * get the Onestepcheckout settings
     *
     * @return multitype:string Ambigous <mixed, string, NULL, multitype:, multitype:Ambigous <string, multitype:, NULL> >
     */
    public function loadSettings() {
        $settings = array ();
        $items = array ();
        $items = Mage::getStoreConfig ( 'onestepcheckout' );
        foreach ( $items as $config ) {
            foreach ( $config as $key => $value ) {
                $settings [$key] = $value;
            }
        }
        /**
         * check condition default country is empty
         */
        if (empty ( $settings ['default_country_id'] )) {
            $settings ['default_country_id'] = 'US';
        }
        return $settings;
    }
    
    /**
     * check the exluded fields and assign - to that values
     */
    public function load_exclude_data(&$data) {
        /**
         * check condition display city is not empty
         */
        if ($this->settings ['display_city']) {
            $data ['city'] = '-';
        }
        /**
         * check condition display country is not empty
         */
        $getDisplayCountry = $this->settings ['display_country'];
        if ($getDisplayCountry) {
            /**
             * chcek condition str enable geoip is equal to 1
             */
            $geiIPEnableStatus = $this->settings ['enable_geoip'];
            if ($geiIPEnableStatus == 1) {
                /**
                 * check condition customer is logged in
                 */
                $userLoggedStatus = Mage::helper ( 'customer' )->isLoggedIn ();
                if ($userLoggedStatus == 1) {
                    $countryId = $this->getCountryCode ();
                } else {
                    $countryId = $this->getGeoIp ()->countryCode;
                }
            } else {
                /**
                 * check condition customer is loggedin
                 */
                $userLoggedinStatus = Mage::helper ( 'customer' )->isLoggedIn ();
                if ($userLoggedinStatus == 1) {
                    $countryId = $this->getCountryData ();
                } else {
                    $countryId = $this->settings ['default_country_id'];
                }
            }
        }
        return $this->CheckCondition ( $data );
    }
    
    /**
     * get country data
     */
    public function getCountryData() {
        $customerAddressIdData = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getDefaultBilling ();
        /**
         * check condition address id is not empty
         */
        if ($customerAddressIdData) {
            $address = Mage::getModel ( 'customer/address' )->load ( $customerAddressIdData );
            return $address ['country_id'];
        } else {
            return $this->settings ['default_country_id'];
        }
    }
    
    /**
     * function to get country code
     */
    public function getCountryCode() {
        $customerAddressIdData = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getDefaultBilling ();
        /**
         * check condition customer id is not empty
         */
        if ($customerAddressIdData) {
            $address = Mage::getModel ( 'customer/address' )->load ( $customerAddressIdData );
            return $address ['country_id'];
        } else {
            return $this->getGeoIp ()->countryCode;
        }
    }
    /**
     * check the exclude fields and assign - to that values when ajax updates trigger
     */
    public function load_add_data($data) {
        /**
         * check condition cite is not empty
         */
        if (isset ( $data ['city'] )) {
            /**
             * check condition enable geiopis equal to 1
             */
            if ($this->settings ['enable_geoip'] == 1) {
                
                $data ['city'] = $this->getGeoIp ()->city;
            } else {
                $data ['city'] = '-';
            }
        }
        /**
         * check condition country id is not empty
         */
        if (empty ( $data ['country_id'] )) {
            /**
             * check codnition enable geiop is equal to 1
             */
            if ($this->settings ['enable_geoip'] == 1) {
                /**
                 * check condition customer logged in id is equal to 1
                 */
                if (Mage::helper ( 'customer' )->isLoggedIn () == 1) {
                    $countryId = $this->getCountryCode ();
                } else {
                    $countryId = $this->getGeoIp ()->countryCode;
                }
            } else {
                /**
                 * check condition customer loggedin value is equal to 1
                 */
                if (Mage::helper ( 'customer' )->isLoggedIn () == 1) {
                    $countryId = $this->getCountryData ();
                } else {
                    $countryId = $this->settings ['default_country_id'];
                }
            }
        }
        return $this->CheckCondition ( $data );
    }
    
    /**
     * checking condition is empty
     */
    public function CheckCondition($data) {
        /**
         * check condition telephone is empty
         */
        if (empty ( $data ['telephone'] )) {
            $data ['telephone'] = '-';
        }
        /**
         * chekc condition region id is empty
         */
        if (empty ( $data ['region_id'] )) {
            $data ['region_id'] = '-';
            $data ['region'] = '-';
        }
        /**
         * chekc condition post code is empty
         */
        if (empty ( $data ['postcode'] )) {
            $data ['postcode'] = '-';
        }
        /**
         * chekc condition company is empty
         */
        if (empty ( $data ['company'] )) {
            $data ['company'] = '-';
        }
        /**
         * chekc condition fax is empty
         */
        if (empty ( $data ['fax'] )) {
            $data ['fax'] = '-';
        }
        /**
         * chekc condition street is empty
         */
        if (empty ( $data ['street'] [0] )) {
            $data ['street'] [0] = '-';
        }
        return $data;
    }
    
    /**
     * get location detail using geoip
     */
    public function getGeoIp() {
        $enableGeoIp = Mage::getStoreConfig ( 'onestepcheckout/general/enable_geoip' );
        $database = Mage::getStoreConfig ( 'onestepcheckout/general/geoip_database' );
        
        try {
            require_once ('Net/GeoIP.php');
            $ipaddress = Mage::helper ( 'core/http' )->getRemoteAddr ();
            $geoip = Net_GeoIP::getInstance ( $database );
            $location = $geoip->lookupLocation ( $ipaddress );
            /**
             * IF PEAR NET_GEOIP IS INSTALLED AND PHP CAN ACCESS THIS THEN YOU WILL SEE YOUR COUNTRY CODE DETECTED IF NOT THEN YOU SEE ERRORS INSTEAD
             */
            if ($enableGeoIp == 1) {
                return $location;
            }
        } catch ( Exception $e ) {
            return $e->getMessage ();
        }
    }
    
    /**
     * get checkout onepage detail from database
     *
     * @return Ambigous <Mage_Core_Model_Abstract, mixed, NULL, multitype:>
     */
    public function getOnepage() {
        return Mage::getSingleton ( 'checkout/type_onepage' );
    }
    
    /**
     * get virtual value
     *
     * @return boolean
     */
    public function getVirtual() {
        $returnValue = '';
        /**
         * check condition onepage checkout is not empty
         */
        if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
            $returnValue = true;
        } else {
            $returnValue = false;
        }
        return $returnValue;
    }
    
    /**
     * check condition passes email is exist in customer table
     *
     * @param customer $email            
     * @return Ambigous <mixed, NULL, multitype:>|boolean
     */
    public function IscustomerEmailExists($email) {
        $websiteId = null;
        $websiteId = Mage::app ()->getWebsite ()->getId ();
        $customer = Mage::getModel ( 'customer/customer' );
        /**
         * chcek condition website id is not empty
         */
        if ($websiteId) {
            $customer->setWebsiteId ( $websiteId );
        }
        $customer->loadByEmail ( $email );
        /**
         * chcek condition customer id is not empty
         */
        if ($customer->getId ()) {
            return $customer->getId ();
        }
        return false;
    }
}