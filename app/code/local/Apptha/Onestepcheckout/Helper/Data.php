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
class Apptha_Onestepcheckout_Helper_Data extends Mage_Core_Helper_Abstract {
    
    /**
     * get the Onestepcheckout activated settings and return the boolean
     */
    public function isOnepageCheckoutLinksEnabled() {
        /**
         * return store config which is onestep checkout extension enable or not
         */
        return Mage::getStoreConfig ( 'onestepcheckout/general/Activate_apptha_onestepcheckout' );
    }    
    /**
     * DomainKey function for return the encrypted message
     * return @string
     */
    public function domainKey($tkey) {
        $message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        $lenkey = strlen ( $tkey );
        for($i = 0; $i < $lenkey; $i ++) {
            $key_array [] = $tkey [$i];
        }
        /**
         * Assign the encrypted message to empty
         */
        $enc_message = "";
        /**
         * Position value set as zero
         */
        $kPos = 0;
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $lenCharStr = strlen ( $chars_str );
        for($i = 0; $i < $lenCharStr; $i ++) {
            $chars_array [] = $chars_str [$i];
        }
        $messagelen = strlen ( $message );
        $countKeyArray = count ( $key_array );
        for($i = 0; $i < $messagelen; $i ++) {
            $char = substr ( $message, $i, 1 );
            
            $offset = $this->getOffset ( $key_array [$kPos], $char );
            $enc_message .= $chars_array [$offset];
            /**
             * Increment the position value
             */
            $kPos ++;
            if ($kPos >= $countKeyArray) {
                $kPos = 0;
            }
        }
        /**
         * Rerturn encrypted message
         */
        return $enc_message;
    }
    /**
     * Licence() - function return teh text with "licence"
     * Return $string
     */
    public function license() {
        return 'license';
    }
    /**
     * getOffset - Function which for is string offset
     * Return @string
     */
    public function getOffset($start, $end) {
        /**
         * character string
         */
        $chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $charlenstr = strlen ( $chars_str );
        for($i = 0; $i < $charlenstr; $i ++) {
            $chars_array [] = $chars_str [$i];
        }
        $countCharsArr = count ( $chars_array );
        for($i = $countCharsArr - 1; $i >= 0; $i --) {
            $lookupObj [ord ( $chars_array [$i] )] = $i;
        }
        /**
         * getting start
         */
        $sNum = $lookupObj [ord ( $start )];
        /**
         * getting end
         */
        $eNum = $lookupObj [ord ( $end )];
        /**
         * offset
         */
        $offset = $eNum - $sNum;
        
        if ($offset < 0) {
            $offset = count ( $chars_array ) + ($offset);
        }
        /**
         * Return $offset as string
         */
        return $offset;
    }
    /**
     * Function to generate Domain
     * return response
     */
    public function genenrateOscdomain() {
        /**
         * getting domain name
         */
        $strDomainName = Mage::app ()->getFrontController ()->getRequest ()->getHttpHost ();
        preg_match ( "/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/^(https:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
        preg_match ( "/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder [2], $matches );
        if (isset ( $matches ['domain'] )) {
            /**
             * cusomer domain Url
             */
            $customerDomainUrl = $matches ['domain'];
        } else {
            $customerDomainUrl = "";
        }
        /**
         * replacing www with empty character
         */
        $customerDomainUrl = str_replace ( "www.", "", $customerDomainUrl );
        /**
         * replacing with dot
         */
        $customerDomainUrl = str_replace ( ".", "D", $customerDomainUrl );
        $customerDomainUrl = strtoupper ( $customerDomainUrl );
        if (isset ( $matches ['domain'] )) {
            /**
             * domain key
             */
            $response = $this->domainKey ( $customerDomainUrl );
        } else {
            $response = "";
        }
        /**
         * return response
         */
        return $response;
    }
}