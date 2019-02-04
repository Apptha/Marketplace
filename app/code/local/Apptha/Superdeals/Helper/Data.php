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
class Apptha_Superdeals_Helper_Data extends Mage_Core_Helper_Abstract {
    const STORE_CONFIG_BESTSELLING_SIDEBAR_COUNT = 'superdeals/topsellingsidebar/product_count';
    const STORE_CONFIG_MOSTVIEWED_SIDEBAR_COUNT = 'superdeals/mostviewedsidebar/product_count';
    const STORE_CONFIG_OFFER_SLIDER_COUNT = 'superdeals/slider/enable_slide';
    
    /**
     * function to check if superdeals enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isDealzEnabled() {
        return Mage::getStoreConfig ( 'superdeals/superdeals_group/deals_enable' );
    }
    
    /**
     * function to check if Mail is enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isMailEnabled() {
        return Mage::getStoreConfig ( 'superdeals/general/send_email_statistics' );
    }
    
    /**
     * function to check if Timer is enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isTimerEnabled() {
        return Mage::getStoreConfig ( 'superdeals/timer/show_timer' );
    }
    
    /**
     * function to check if Timer is enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isCustomColorEnabled() {
        return Mage::getStoreConfig ( 'superdeals/timer/custom_color_enable' );
    }
    
    /**
     * function to check if Top Selling left sidebar is enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isBestSellingLeftEnabled() {
        return Mage::getStoreConfig ( 'superdeals/topsellingsidebar/enable_left' );
    }
    
    /**
     * function to check if Top Selling right sidebar is enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isBestSellingRightEnabled() {
        return Mage::getStoreConfig ( 'superdeals/topsellingsidebar/enable_right' );
    }
    
    /**
     * function to check if Most Viewed left sidebar is enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isMostViewedLeftEnabled() {
        return Mage::getStoreConfig ( 'superdeals/mostviewedsidebar/enable_left' );
    }
    
    /**
     * function to check if Most Viewed right sidebar is enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isMostViewedRightEnabled() {
        return Mage::getStoreConfig ( 'superdeals/mostviewedsidebar/enable_right' );
    }
    
    /**
     * function to check if Deal of the day left sidebar is enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isDealOfDayLeftEnabled() {
        return Mage::getStoreConfig ( 'superdeals/dealofthedaysidebar/enable_left' );
    }
    
    /**
     * function to check if Deal of the day right sidebar is enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isDealOfDayRightEnabled() {
        return Mage::getStoreConfig ( 'superdeals/dealofthedaysidebar/enable_right' );
    }
    
    /**
     * function to get number of products to display in bestselling sidebar
     *
     * This Function will return product count
     *
     * @return int
     */
    public function getBestsellingSidebar() {
        $num = ( int ) Mage::getStoreConfig ( static::STORE_CONFIG_BESTSELLING_SIDEBAR_COUNT );
        return $num >= 0 ? $num : 3;
    }
    
    /**
     * function to get number of products to display in mostviewed sidebar
     *
     * This Function will return product count
     *
     * @return int
     */
    public function getMostviewedSidebar() {
        $num = ( int ) Mage::getStoreConfig ( static::STORE_CONFIG_MOSTVIEWED_SIDEBAR_COUNT );
        return $num >= 0 ? $num : 3;
    }
    
    /**
     * function to get offset slider
     *
     * This Function will return product count
     *
     * @return int
     */
    public function getOfferSlider() {
        $num = ( int ) Mage::getStoreConfig ( 'superdeals/slider/slide_count' );
        if ($num) {
            return $num;
        }
        return 5;
    }
    /**
     * function to check if slider is enabled
     *
     * This Function will return the 0 or 1
     *
     * @return int
     */
    public function isSliderEnabled() {
        return Mage::getStoreConfig ( 'superdeals/slider/enable_slide' );
    }
    /**
     * function to generate domain key
     *
     * This Function will return the domain key
     *
     * @return varchar
     */
    public function domainKey($tkey) {
        /**
         * Validate the domain key from the secret string
         */
        $message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        for($i = 0; $i < strlen ( $tkey ); $i ++) {
            $keyArray [] = $tkey [$i];
        }
        /**
         * Set the encript message variable
         */
        $encMessage = "";
        /**
         * Set position value zero
         */
        $kPos = 0;
        $charStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        for($i = 0; $i < strlen ( $charStr ); $i ++) {
            $charsArray [] = $charStr [$i];
        }
        for($i = 0; $i < strlen ( $message ); $i ++) {
            $char = substr ( $message, $i, 1 );
            $offset = $this->getOffset ( $keyArray [$kPos], $char );
            $encMessage .= $charsArray [$offset];
            /**
             * Increment the position value
             */
            $kPos ++;
            if ($kPos >= count ( $keyArray )) {
                $kPos = 0;
            }
        }
        /**
         * return the ecncrypted string
         */
        return $encMessage;
    }
    /**
     * function to get offset value
     *
     * This Function will return the offset key
     *
     * @return varchar
     */
    public function getOffset($start, $end) {
        /**
         * Asiign the secret string
         */
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        for($i = 0; $i < strlen ( $chars_str ); $i ++) {
            $chars_array [] = $chars_str [$i];
        }
        /**
         * Verify the string which is comes from licence key
         */
        for($i = count ( $chars_array ) - 1; $i >= 0; $i --) {
            $lookupObj [ord ( $chars_array [$i] )] = $i;
        }
        
        $sNum = $lookupObj [ord ( $start )];
        $eNum = $lookupObj [ord ( $end )];
        /**
         * Assign the offset numbers
         */
        $offset = $eNum - $sNum;
        
        if ($offset < 0) {
            $offset = count ( $chars_array ) + ($offset);
        }
        
        return $offset;
    }
    /**
     * function to get domain key
     *
     * This Function will return the domain key
     *
     * @return varchar
     */
    public function genenrateOscdomain() {
        /**
         * get server name
         */
        $strDomainName = $_SERVER ['SERVER_NAME'];
        preg_match ( "/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder [2], $matches );
        if (isset ( $matches ['domain'] )) {
            $customerurl = $matches ['domain'];
        } else {
            $customerurl = "";
        }
        /**
         * Replace the string from customer url
         */
        $customerurl = str_replace ( "www.", "", $customerurl );
        $customerurl = str_replace ( ".", "D", $customerurl );
        $customerurl = strtoupper ( $customerurl );
        /**
         * check the domain key matches or not
         */
        if (isset ( $matches ['domain'] )) {
            $response = $this->domainKey ( $customerurl );
        } else {
            $response = "";
        }
        return $response;
    }
    /**
     * Function to get the deal url
     *
     * This Function will return the redirect url of deal page
     *
     * @return string
     */
    public function getDealUrl() {
        /**
         * return the deals url
         */
        return Mage::getBaseUrl () . 'deals';
    }
}

