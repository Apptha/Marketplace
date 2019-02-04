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
 * This file is used to get twitter url and license key functionality
 *
 * In this class, get twitter url and license key operations are included.
 */
class Apptha_Sociallogin_Helper_Data extends Mage_Core_Helper_Abstract {
    
    /**
     * Get Twitter authendication URL
     *
     * @return string Twitter authendication URL
     */
    public function getTwitterUrl($data) {
        require 'sociallogin/twitter/twitteroauth.php';
        require 'sociallogin/config/twconfig.php';
        
        $twitteroauth = new TwitterOAuth ( YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET );
        
        /**
         * Request to authendicate token, the @param string URL redirects the authorize page
         */
        if ($data == 1) {
            $requestToken = $twitteroauth->getRequestToken ( Mage::getBaseUrl () . 'sociallogin/index/twitterlogin?fb=1' );
        } else {
            $requestToken = $twitteroauth->getRequestToken ( Mage::getBaseUrl () . 'sociallogin/index/twitterlogin' );
        }
        /**
         * check condition http code is equal to 200
         */
        if ($twitteroauth->http_code == 200) {
            Mage::getSingleton ( 'customer/session' )->setTwToken ( $requestToken ['oauth_token'] );
            Mage::getSingleton ( 'customer/session' )->setTwSecret ( $requestToken ['oauth_token_secret'] );
            return $twitteroauth->getAuthorizeURL ( $requestToken ['oauth_token'] );
        }
    }
    /**
     * Retrieve customer session from core customer session
     *
     * @return array
     */
    public function _getCustomerSession() {
        return Mage::getSingleton ( 'customer/session' );
    }
    /**
     * Generates the Domain Key
     *
     * @return string $enc_message
     */
    public function domainKey($tkey) {
        $message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        $lentkey = strlen ( $tkey );
        for($i = 0; $i < $lentkey; $i ++) {
            $key_array [] = $tkey [$i];
        }
        /**
         * assign encrypted message variable is empty string
         */
        $enc_message = "";
        $kPos = 0;
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $lenstrchnars = strlen ( $chars_str );
        for($i = 0; $i < $lenstrchnars; $i ++) {
            $chars_array [] = $chars_str [$i];
        }
        $lenmessage = strlen ( $message );
        $countKeyArray = count ( $key_array );
        for($i = 0; $i < $lenmessage; $i ++) {
            $char = substr ( $message, $i, 1 );
            
            $offset = $this->getOffset ( $key_array [$kPos], $char );
            /**
             * assign encrypted message to customer
             */
            $enc_message .= $chars_array [$offset];
            $kPos ++;
            /**
             * check condition country key array equal to kpos
             */
            if ($kPos >= $countKeyArray) {
                $kPos = 0;
            }
        }
        /**
         * @return encrypted message to customer
         */
        return $enc_message;
    }
    /**
     * Get offset from character string
     *
     * @return integer $offset
     */
    public function getOffset($start, $end) {
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        /**
         * get string length
         */
        $countcharstr = strlen ( $charsStr );
        /**
         * load string length for get country array value
         */
        for($i = 0; $i < $countcharstr; $i ++) {
            $charsArray [] = $charsStr [$i];
        }
        $countcharsArray = count ( $charsArray );
        /**
         * load country array items
         */
        for($i = $countcharsArray - 1; $i >= 0; $i --) {
            $lookupObj [ord ( $charsArray [$i] )] = $i;
        }
        
        $sNum = $lookupObj [ord ( $start )];
        $eNum = $lookupObj [ord ( $end )];
        
        $offset = $eNum - $sNum;
        /**
         * check condition offset value is equal lessthan 0
         */
        if ($offset < 0) {
            $counrArray = count ( $charsArray );
            $offsetData = $counrArray + ($offset);
        }
        
        return $offsetData;
    }
    /**
     * Generates the Domain
     *
     * @return string $response
     */
    public function genenrateOscdomain() {
        /**
         * Get Controller Name
         */
        $strDomainName = Mage::app ()->getFrontController ()->getRequest ()->getHttpHost ();
        preg_match ( "/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/^(https:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder [2], $matches );
        /**
         * check condition strdomain is not empty
         */
        if (isset ( $matches ['domain'] )) {
            $customerUrl = $matches ['domain'];
        } else {
            $customerUrl = "";
        }
        /**
         * Replace Customer URL
         */
        $customerUrl = str_replace ( "www.", "", $customerUrl );
        $customerUrl = str_replace ( ".", "D", $customerUrl );
        /**
         * Convert to upper case
         */
        $customerUrl = strtoupper ( $customerUrl );
        /**
         * check condition strdomain value is not empty
         */
        if (isset ( $matches ['domain'] )) {
            /**
             * Domain key customer url
             */
            $response = $this->domainKey ( $customerUrl );
        } else {
            $response = "";
        }
        return $response;
    }
    
    /**
     * Get auth url
     *
     * @param array $session            
     * @param string $sellerLogin            
     * @return string $session
     */
    public function getAuthUrl($session, $sellerLogin) {
        if ($sellerLogin == 1) {
            /**
             * set Auth url
             */
            $session->setBeforeAuthUrl ( Mage::helper ( 'marketplace/marketplace' )->dashboardUrl () );
        } else {
            /**
             * set Auth url
             */
            $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getAccountUrl () );
        }
        return $session;
    }
    /**
     * Check auth url
     *
     * @param array $session            
     * @return number $checkAuthUrl
     */
    public function checkAuthUrl($session) {
        $checkAuthUrl = 0;
        if (! $session->getBeforeAuthUrl () || $session->getBeforeAuthUrl () == Mage::getBaseUrl ()) {
            $checkAuthUrl = 1;
        }
        return $checkAuthUrl;
    }
}