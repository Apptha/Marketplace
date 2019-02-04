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
class Apptha_Marketplace_Helper_Url extends Mage_Core_Helper_Abstract {
    /**
     * Function to get new product url
     *
     * This Function will return the redirect url of new product form
     *
     * @return string
     */
    public function getNewProductUrl() {
        return Mage::getUrl ( 'marketplace/product/new' );
    }
    /**
     * Functionto get the manage product url
     *
     * This Function will return the redirect url of manage products
     *
     * @return string
     */
    public function getManageProductUrl() {
        return Mage::getUrl ( 'marketplace/product/manage' );
    }
    /**
     * Function to get the manage order url
     *
     * This Function will return the redirect url of manage orders
     *
     * @return string
     */
    public function getManageOrderUrl() {
        return Mage::getUrl ( 'marketplace/order/manage' );
    }
    
    /**
     * Function to get the add profile url
     *
     * This Function will return the redirect url of add profile
     *
     * @return string
     */
    public function addprofileUrl() {
        return Mage::getUrl ( 'marketplace/seller/addprofile' );
    }
    
    /**
     * Function to get the become a merchant url
     *
     * This Function will return the redirect url to become a merchant
     *
     * @return string
     */
    public function becomemerchantUrl() {
        return Mage::getUrl ( 'marketplace/general/changebuyer' );
    }
    
    /**
     * Function to get link profile url
     *
     * Passed the seller id in url to get the seller store name
     *
     * @param int $sellerId
     *            This Function will return the redirect url link to seller profile
     * @return string
     */
    public function linkprofileUrl($sellerId) {
        return Mage::getUrl ( 'marketplace/seller/displayseller', array (
                'id' => $sellerId 
        ) );
    }
    
    /**
     * Function to get link product url
     *
     * Passed the seller id in url to get the seller store name
     *
     * @param int $sellerId
     *            This Function will return the redirect url
     * @return string
     */
    public function linkproductUrl($sellerId) {
        return Mage::getUrl ( 'marketplace/seller/sellerproduct', array (
                'id' => $sellerId 
        ) );
    }
    
    /**
     * Function to get seller registration url
     *
     * This Function will return the redirect url to seller registration
     *
     * @return string
     */
    public function getregisterUrl() {
        return Mage::getUrl ( 'marketplace/seller/create' );
    }
    
  
    /**
     * Function to get all seller information
     *
     * This Function will return the redirect url to view all seller page
     *
     * @return string
     */
    public function getviewallsellerUrl() {
        return Mage::getUrl ( 'marketplace/seller/allseller' );
    }
    
    /**
     * Function to get create product url
     *
     * This Function will return the redirect url of create product form
     *
     * @return string
     */
    public function getCreateProductUrl() {
        return Mage::getUrl ( 'marketplace/sellerproduct/create' );
    }
    
    /**
     * Function to get product type url
     *
     * This Function will return the redirect url of create product form
     *
     * @return string
     */
    public function getProductTypeUrl() {
        return Mage::getUrl ( 'marketplace/sellerproduct/producttype' );
    }
    
    /**
     * Function to get add configurable product url
     *
     * This Function will return the redirect url of create product form
     *
     * @return string
     */
    public function getAddConfigurableProductUrl() {
        return Mage::getUrl ( 'marketplace/sellerproduct/configurable' );
    }
    /**
     * Function to get assing product url
     *
     * This Function will return the redirect url of assign product form
     *
     * @return string
     */
    public function getAssignNewProductUrl() {
        return Mage::getUrl ( 'marketplace/sellerproduct/assignproduct' );
    }
    
    /**
     * Function to get manage assign product url
     *
     * This Function will return the redirect url of assign product form
     *
     * @return string
     */
    public function getAssignProductUrl() {
        return Mage::getUrl ( 'marketplace/sellerproduct/manageassignproduct' );
    }
    
    /**
     * Function to get vacation mode url
     *
     * This Function will return the redirect url of vacation mode form
     *
     * @return string
     */
    public function getVacationModeUrl() {
        return Mage::getUrl ( 'marketplace/general/vacationmode' );
    }
    /**
     * Function to get manage deals url
     *
     * This Function will return the redirect url to view deals
     *
     * @return string
     */
    public function getManageDealsUrl() {
        return Mage::getUrl ( 'marketplace/product/managedeals' );
    }
    
    /**
     * Function to get view all compare price products url
     *
     * This Function will return the redirect url of view all compare price products
     *
     * @return string
     */
    public function getComparePriceUrl($productId) {
        return Mage::getUrl ( 'marketplace/product/comparesellerprice', array (
                'id' => $productId 
        ) );
    }
    
    /**
     * Function to get invoice order url
     *
     * Passed the order id in url to get the order details
     *
     * @param int $orderId
     *            Passed the product id in url to get the product details
     * @param int $productId
     *            This Function will return the redirect url to view order details
     * @return string
     */
    public function getInvoiceUrl($orderId, $productId) {
        return Mage::getUrl ( 'marketplace/order/invoice', array (
                'orderid' => $orderId,
                'productid' => $productId 
        ) );
    }
    /**
     * Function to get quick create simple product url
     *
     * This Function will return the redirect url of create product form
     *
     * @return string
     */
    public function getQuickCreateSimpleProductUrl() {
        return Mage::getUrl ( 'marketplace/sellerproduct/quickcreate' );
    }
    
    /**
     * Function to get quick create simple product url
     *
     * This Function will return the redirect url of create product form
     *
     * @return string
     */
    public function getUpdateSimpleProductUrl() {
        return Mage::getUrl ( 'marketplace/sellerproduct/updatesimpleproduct' );
    }
   
        
  }