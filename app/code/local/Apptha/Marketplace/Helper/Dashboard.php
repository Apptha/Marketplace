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
 * Function written in this file are globally accessed
 */
class Apptha_Marketplace_Helper_Dashboard extends Mage_Core_Helper_Abstract {
    /**
     * Function to get seller registration url and login url
     *
     * This Function will return the redirect url seller registration and login
     *
     * @return string
     */
    public function getregister() {
        /**
         * Return seller login url
         */
        return Mage::getUrl ( 'marketplace/seller/login' );
    }
    /**
     * Function to get the dashboard url
     *
     * This Function will return the redirect url to dashboard
     *
     * @return string
     */
    public function dashboardUrl() {
        /**
         * Return seller dashbaord url
         */
        return Mage::getUrl ( 'marketplace/seller/dashboard' );
    }
}