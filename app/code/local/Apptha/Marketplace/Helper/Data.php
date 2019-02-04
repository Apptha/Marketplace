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
 * @version     1.9.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2016 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
/**
 * This class contains data manipulation files
 **
 */
class Apptha_Marketplace_Helper_Data extends Mage_Core_Helper_Abstract {
    /**
     * Function to get seller group id
     *
     * Return seller group id
     *
     * @return int
     */
    public function getGroupId() {
    /**
     * load customer model by group code
     *
     * @var unknown
     */
        $customerGroup = Mage::getModel ( 'customer/group' )->load ( 'marketseller', 'customer_group_code' );
        return $customerGroup->getCustomerGroupId ();
    }
    /**
     * Getting selected prdouct types
     *
     * Return selected product types
     *
     * @return int
     */
    public function getSelectedPrdouctType() {
    /**
     * Get product types
     */
        return Mage::getStoreConfig ( 'marketplace/product/producttype' );
    }
    /**
     * Getting product custom option configuration
     *
     * Return product custom option enable or not
     *
     * @return int
     */

    public function getPrdouctCustomOptions() {
    /**
     * Get Custom options enabled or not
     */
        return Mage::getStoreConfig ( 'marketplace/product/productcustomoptions' );
    }
    /**
     * Getting product approval option configuration
     *
     * Return product approval enable or not
     *
     * @return int
     */

    public function getProductApproval() {
    /**
     * Get Product approval needed or not
     */
        return Mage::getStoreConfig ( 'marketplace/product/productapproval' );
    }
    /**
     * Getting product types
     *
     * Return enabled product types
     *
     * @return int
     */

    public function getProductTypes() {
    /**
     * Get product type array
     */
        return array (
                "simple" => "Simple Product",
                "virtual" => "Virtual Product",
                "downloadable" => "Downloadable Product",
                "configurable" => "Configurable Product"
        );
    }
    /**
     * Function to get the domain key
     *
     * Return domain key
     *
     * @return string
     */

    public function domainKey($tkey) {
        $message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        $stringLength = strlen ( $tkey );
        for($i = 0; $i < $stringLength; $i ++) {
            $keyArray [] = $tkey [$i];
        }
        $encMessage = "";
        $kPos = 0;
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $strLen = strlen ( $charsStr );
        for($i = 0; $i < $strLen; $i ++) {
            $charsArray [] = $charsStr [$i];
        }
        $lenMessage = strlen ( $message );
        $count = count ( $keyArray );
        for($i = 0; $i < $lenMessage; $i ++) {
            $char = substr ( $message, $i, 1 );
            $offset = $this->getOffset ( $keyArray [$kPos], $char );
            $encMessage .= $charsArray [$offset];
            $kPos ++;

            if ($kPos >= $count) {
                $kPos = 0;
            }
        }
        return $encMessage;
    }
    /**
     *
     * Return offset key
     *
     * @return string
     */

    public function getOffset($start, $end) {
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $strLen = strlen ( $charsStr );
        /**
         * Increment for loop
         */
        for($i = 0; $i < $strLen; $i ++) {
            $charsArray [] = $charsStr [$i];
        }
        for($i = count ( $charsArray ) - 1; $i >= 0; $i --) {
            $lookupObj [ord ( $charsArray [$i] )] = $i;
        }
        $sNum = $lookupObj [ord ( $start )];
        $eNum = $lookupObj [ord ( $end )];
        $offset = $eNum - $sNum;
        if ($offset < 0) {
            $offset = count ( $charsArray ) + ($offset);
        }
        return $offset;
    }
    /**
     * Function to get order currency Symbol
     *
     * Return currency
     *
     * @return string
     */

    public function orderCurrencySymbol($orderId) {
        $currecyCode = Mage::getModel('sales/order')->load($orderId)->getOrderCurrencyCode();
        return Mage::app()->getLocale()->currency($currecyCode)->getSymbol();
    }

    /**
     * Function to get order currency code
     *
     * Return currency
     *
     * @return string
     */
    public function orderCurrencyCode($orderId) {
    /**
     * load order object by order id
      */
    return Mage::getModel('sales/order')->load($orderId)->getOrderCurrencyCode();
    }
    /**
     * Function to get seller products
     *
     * Return product collection for seller
     *
     * @return array
     */
    public function sellerProducts($sellerId) {
    /**
     * Get product collection
     */
    return Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('seller_id',$sellerId);
    }
}