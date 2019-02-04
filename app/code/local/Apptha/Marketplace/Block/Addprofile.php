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
 * Seller profile Update
 * This file contains add seller profile
 * Edit seller profile
 */
class Apptha_Marketplace_Block_Addprofile extends Mage_Core_Block_Template {
/**
     * Function to get save profile url
     * 
     * Return the save profile action url
     * @return string
     */
    function addprofile() {
        return Mage::getUrl('marketplace/seller/saveprofile');
    }
/**
     * Function to display the profile info for edit option
     * 
     * Passed seller id to get seller information
     * @param int @sellerId
     * 
     * Return seller data as array
     * @return array
     */
    function editprofile($sellerId) {
        $collection = Mage::getModel('marketplace/sellerprofile')->getCollection()
                ->addFieldToFilter('seller_id', $sellerId);
        /**
         * Increment Foreach loop
         */
        foreach ($collection as $data) {
            return $data;
        }
    }
}