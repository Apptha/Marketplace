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
class Apptha_Marketplace_Helper_Common extends Mage_Core_Helper_Abstract {
    /**
     * Function to get customer review url
     *
     * This Function will return the redirect url to customer review
     *
     * @return string
     */
    public function customerreviewUrl() {
        /**
         * Get customer review url
         */
        return Mage::getUrl ( 'marketplace/sellerreview/customerreview' );
    }
    /**
     * Function to get all review data
     *
     * Passed the seller id in url to get all review
     *
     * @param int $id
     *            This Function will return all reviews as array format for a particular seller
     * @return array
     */
    function getallreviewdata($id) {
        $storeId = Mage::app ()->getStore ()->getId ();
        return Mage::getModel ( 'marketplace/sellerreview' )->getCollection ()->addFieldToFilter ( 'status', 1 )->addFieldToFilter ( 'store_id', $storeId )->addFieldToFilter ( 'seller_id', $id );
    }
    /**
     * Function to get order collection
     *
     * Filter the order collection by customer id
     *
     * @param int $customerId
     *            This function will return only the order details of particular customer
     * @return array
     */
    function allowReview($customerId) {
        /**
         * Get allow review
         */
        return Mage::getResourceModel ( 'sales/order_collection' )->addFieldToSelect ( '*' )->addFieldToFilter ( 'customer_id', $customerId )->addAttributeToSort ( 'created_at', 'DESC' )->setPageSize ( 5 );
    }
    /**
     * Function to get contact form url
     *
     * This function will return the contact form url
     *
     * @return string
     */
    public function getContactFormUrl() {
        /**
         * Get contact form url
         */
        return Mage::getUrl ( 'marketplace/contact/form' );
    }
    
    /**
     * Function to get the seller rewrite url
     *
     * Passed the seller id to rewrite the particular seller url
     *
     * @param int $sellerId
     *            This function will return the rewrited url for a particular seller
     * @return string
     */
    public function getSellerRewriteUrl($sellerId) {
        /**
         * Get target path
         */
        $targetPath = 'marketplace/seller/displayseller/id/' . $sellerId;
        $mainUrlRewrite = Mage::getModel ( 'core/url_rewrite' )->load ( $targetPath, 'target_path' );
        $getRequestPath = $mainUrlRewrite->getRequestPath ();
        return Mage::getUrl ( $getRequestPath );
    }
    /**
     * Function to update comment from admin
     *
     * Passed the comment provided by admin before pay amount to seller
     *
     * @param int $comment
     *            Passed the transaction id to update the comment for that particular transaction
     * @param int $transactionId
     *            This function will return true or false
     * @return bool
     */
    public function updateComment($comment, $transactionId) {
        /**
         * Get current time
         */
        $now = Mage::getModel ( 'core/date' )->date ( 'Y-m-d H:i:s', time () );
        if (! empty ( $transactionId )) {
            Mage::getModel ( 'marketplace/transaction' )->setPaid ( 1 )->setPaidDate ( $now )->setComment ( $comment )->setId ( $transactionId )->save ();
            return true;
        }
    }
    
    /**
     * Function to get quick create simple product url
     *
     * This Function will return the redirect url of create product form
     *
     * @return string
     */
    public function getUpdateSimpleProductUrl() {
        /**
         * Return update shimple product url
         */
        return Mage::getUrl ( 'marketplace/sellerproduct/updatesimpleproduct' );
    }
    /**
     * Function to credit amount to seller
     *
     * Passed the Commission Id to update the amount credited details
     *
     * @param int $commissionId
     *            This function will return true or false
     * @return bool
     */
    public function updateCredit($commissionId) {
        /**
         * Set credit is one
         */
        $collection = Mage::getModel ( 'marketplace/commission' )->load ( $commissionId, 'id' );
        $collection->setCredited ( '1' )->save ();
        return $collection;
    }
    
    /**
     * Function to delete a seller review
     *
     * Seller id is passed to delete the seller review
     *
     * @param int $marketplaceId
     *            This function will return true or false
     * @return bool
     */
    public function deleteReview($marketplaceId) {
        /**
         * Delete seller reivew by id
         */
        $model = Mage::getModel ( 'marketplace/sellerreview' );
        $model->setId ( $marketplaceId )->delete ();
        return true;
    }
    
    /**
     * Function to approve review
     *
     * Seller id is passed to approve the seller review
     *
     * @param int $marketplaceId
     *            This function will return true or false
     * @return bool
     */
    public function approveReview($marketplaceId) {
        /**
         * Load seller review by seller id
         */
        $model = Mage::getModel ( 'marketplace/sellerreview' )->load ( $marketplaceId );
        $model->setStatus ( '1' )->save ();
        /**
         * Return model
         */
        return $model;
    }
    /**
     * Function to delete a seller account
     *
     * Seller id is passed to delete the seller
     *
     * @param int $marketplaceId
     *            This function will return true or false
     * @return bool
     */
    public function deleteSeller($marketplaceId) {
        /**
         * Load customer by customer id
         */
        $marketplace = Mage::getModel ( 'customer/customer' )->load ( $marketplaceId );
        /**
         * Set group id
         */
        $marketplace->setGroupId ( 1 );
        /**
         * Save model
         */
        $marketplace->save ();
        return true;
    }
    /**
     * Function to update approve seller status
     *
     * Seller id is passed to approve the seller
     *
     * @param int $marketplaceId
     *            This function will return true or false
     * @return bool
     */
    public function approveSellerStatus($marketplaceId) {
        /**
         * Load customer model
         */
        $model = Mage::getModel ( 'customer/customer' )->load ( $marketplaceId );
        /**
         * Save customer status
         */
        $model->setCustomerstatus ( '1' )->save ();
        
        $sellerProdCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('seller_id',$marketplaceId);
        $sellerProdDatas=$sellerProdCollection->getData();
        foreach($sellerProdDatas as $sellerProd){
        $prodId=$sellerProd['entity_id'];
        Mage::getModel('catalog/product')->load ( $prodId )->setStatus(1)->save ();
        }
        return true;
    }
    
    /**
     * Function to update disapprove seller status
     *
     * Seller id is passed to disapprove the seller
     *
     * @param int $marketplaceId
     *            This function will return true or false
     * @return bool
     */
    public function disapproveSellerStatus($marketplaceId) {
        /**
         * Load customer model
         */
        $model = Mage::getModel ( 'customer/customer' )->load ( $marketplaceId );
        /**
         * Save customer model
         */
        $model->setCustomerstatus ( '2' )->save ();
        
        $sellerProductCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('seller_id',$marketplaceId);
        $sellerProductDatas=$sellerProductCollection->getData();
        foreach($sellerProductDatas as $sellerProducts){
        $productId=$sellerProducts['entity_id'];
        Mage::getModel('catalog/product')->load ( $productId )->setStatus(2)->save ();
        }
        return true;
    }
    
    /**
     * Function to display store country in new product add section
     *
     * Passed seller id to get store country
     *
     * @param
     *            int @sellerId
     *            
     *            Return store country data
     * @return array
     */
    function getStoreCountry($sellerId) {
        $storeCountry = '';
        $storeCollection = Mage::getModel ( 'marketplace/sellerprofile' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId );
        foreach ( $storeCollection as $store ) {
            $storeCountry = $store ['country'];
        }
        return $storeCountry;
    }
    
    /**
     * Function to load particular category information
     *
     * Passed Category Id to get the category information
     *
     * @param int $catId
     *            This function will return the Category information as array
     * @return array
     */
    public function getCategoryData($catId) {
    /** load category based on category id */
    return Mage::getModel ( 'catalog/category' )->load ( $catId );
    }
    /**
     * Function to storing downloadable product link data
     *
     * Downloadable file data are passed as array
     *
     * @param array $linkModel
     *            This function will return true or false
     * @return bool
     */
    public function saveDownLoadLink($linkModel) {
    /** to save download link */
    $linkModel->save ();
    return true;
    }
    

    /**
     * Function to load customer data
     *
     * Passed the selle id to load a particular seller details
     *
     * @param int $sellerId
     *            This function will return the seller details as array
     * @return array
     */
    public function loadCustomerData($sellerId) {
    /** To load customer based on seller id */
    return Mage::getModel ( 'customer/customer' )->load ( $sellerId );
    }
    
    /**
     * Function to check products related to customer
     *
     * @return boolean
     */
    public function checkSellerProduct() {
    
    /**
          * Assign the customer id as empty
          */
  $customerId = '';
  $arrayProductId  = array();
        /**
          * Check the customer is currently logged in
          * if so then get the customer data
          */
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
          $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
            $customerId = $customerData->getId ();
        }
        $cart = Mage::getModel('checkout/cart')->getQuote();
        /**
         * Increment foreach loop
         */
        foreach ($cart->getAllItems() as $item) {
        $arrayProductId[] = $item->getProductId();
        }
        /**
         * Get Product Collection
         * @var array
         */
        $products = Mage::getModel('catalog/product')->getCollection()
        ->addAttributeToSelect('*')
        ->addAttributeToFilter('entity_id', array('in' => $arrayProductId));
       /**
        * Increment foreach loop
        */
        foreach($products as $product)
        {
        /**
         * Check the product id is not set
         * or cutomer id is empty
         * if so return
         */
         if (empty ( $customerId )) {
         return true;
         }
         $sellerId = $product->getSellerId ();
         /**
          * check the the current customer id is equal to the seller id
          */
          if ($sellerId == $customerId) {
             return false;
          }
        }
        return true;
    }
}