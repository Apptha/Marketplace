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
class Apptha_Marketplace_Helper_Outofstock extends Mage_Core_Helper_Abstract {
    
    /**
     * Function to check whether parent product becomes out of stock
     * 
     * @param
     *            s product id
     * @return object
     */
    public function CheckOutofstock($productId) {
        /**
         * Get Product Collection
         * Filter by price,assign product id,visibilty,entity id
         * 
         * @var unknown
         */
        $productCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'is_assign_product', array (
                'eq' => 1 
        ) )->/**
         * Sort by Ascending order
         */
        addAttributeToSort ( 'price', Varien_Data_Collection::SORT_ORDER_ASC )->addFieldToFilter ( 'assign_product_id', array (
                'eq' => $productId 
        ) )->addFieldToFilter ( 'entity_id', array (
                'neq' => $productId 
        ) )->addAttributeToFilter ( 'visibility', array (
                'eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG 
        ) );
        /**
         * Query to exclude associated products
         */
        $productCollection->getSelect ()->joinLeft ( array (
                'link_table' => 'catalog_product_super_link' 
        ), 'link_table.product_id = e.entity_id', array (
                'product_id' 
        ) );
        $productCollection->getSelect ()->where ( 'link_table.product_id IS NULL' );
        $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
        if ($product->getTypeId () == 'configurable') {
            $productCollection->addAttributeToFilter ( 'type_id', array (
                    'eq' => 'configurable' 
            ) );
        }
        
        Mage::getSingleton ( 'cataloginventory/stock' )->addInStockFilterToCollection ( $productCollection );
        return $productCollection;
    }
    /**
     * Function to get Current handler
     * 
     * @return string
     */
    public function getCurrentHandler() {
        /**
         * get current router name
         * 
         * @var string
         */
        $routeName = Mage::app ()->getRequest ()->getRouteName ();
        /**
         * get controller name
         * 
         * @var unknown
         */
        $controllerName = Mage::app ()->getRequest ()->getControllerName ();
        /**
         * get action name
         * 
         * @var string
         */
        $actionName = Mage::app ()->getRequest ()->getActionName ();
        
        return $routeName . "_" . $controllerName . "_" . $actionName;
    }
    /**
     * Function to get Product object
     * 
     * @return object
     */
    public function getProductObject($productId) {
        /**
         * load product object by product id
         */
        return Mage::getModel ( 'catalog/product' )->load ( $productId );
    }
    
    /**
     * Function to get facebook store config save url
     *
     * This Function will return the redirect url of facebook store config save action
     *
     * @return string
     */
    public function getfacebookstoreconfigSaveUrl() {
        return Mage::getUrl ( 'marketplace/general/facebookstoreconfig' );
    }
    
    /**
     * Get subcribed date
     * Passed seller id to get the seller customer registered date
     *
     * @param $sellerId Return
     *         subscribed date
     * @return date
     */
    public function subscribtionInvoiceId($sellerId) {
        /**
         * get seller/designer subscribed date
         */
        $collection = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $sellerId, 'seller_id' );
        if ($collection) {
            return $collection->getInvoiceId ();
        }
    }
    
    /**
     * Function to update comment from admin
     *
     * Passed the comment provided by admin before pay amount to seller
     * @param int $comment
     *
     * Passed the transaction id to update the comment for that particular transaction
     * @param int $transactionId
     *
     * This function will return true or false
     * @return bool
     */
    public function updateComment($comment, $transactionId,$amount,$paid) {
        /**
         * Get current date and time
         * @var unknown
         */
        $now = Mage::getModel('core/date')->date('Y-m-d H:i:s', time());
        if (!empty($transactionId)) {
    
            Mage::getModel('marketplace/transaction')
            ->setComment($comment)
            ->setPaidDate($now)
            ->setPaidUpto($amount)
            ->setPaid($paid)
            ->setId($transactionId)->save();
            return true;
        }
    }
    
    /**
     -    * Function to get facebook store config url
     -     *
     -     * This Function will return the redirect url of facebook store config form
     -     *
     -     * @return string
     -     */
    public function getFacebookStoreUrl() {
        return Mage::getUrl ( 'marketplace/general/facebookstore' );
    }
    /**
     * Function to get the selected category
     *
     * @param array $key
     * @param array $categoryId
     */
    public function checkSelectedCategory($key, $categoryid) {
        $catChecked = '';
        if (in_array($key,$categoryid)) {
            $catChecked = 'checked';
        }
        return $catChecked;
    }
    
    
    /**
     * Funtion to edit post action
     * 
     * @return object
     */
    public function editpostProduct($productData,$_eachStoreId,$productId,$sellerDefaultLangId){
        /**
         * getting each store code
         */
        /**
         * getting each store id
         */
        $_storeId = Mage::app()->getStore($_eachStoreId)->getId();
        /**
         * getting each store lang
         */
        $storeName = $storeDesc = $storeShortDesc = null;
        
        
       if($_storeId != $sellerDefaultLangId):
        /**
         *  update store view product data
        */
        $catelogProduct = Mage::getSingleton( 'catalog/product' )->setStoreId ( $_storeId )->load ( $productId );
        /**
         * update store view data such as
         * Name
         * Description
         * ShortDescription
         * and save it into product
         */
        $specialFromDate = $productData ['special_from_date'];
        $specialToDate = $productData ['special_to_date'];
        /**
         * update product data in storeview
         */
        if($storeName = $productData['store_name'][$_storeId]){
            $catelogProduct->setName($storeName);
        }
        if($storeDesc = $productData['store_description'][$_storeId]){
            $catelogProduct->setDescription($storeDesc);
        }
        if($storeShortDesc = $productData['store_short_description'][$_storeId]){
            $catelogProduct->setShortDescription($storeShortDesc);
        }
        /**
         * save special price date to all store view
         */
        if (! empty ( $specialFromDate )) {
            $catelogProduct->setSpecialFromDate ( $specialFromDate );
        } else {
            $catelogProduct->setSpecialFromDate ( '' );
        }
        if (! empty ( $specialToDate )) {
            $catelogProduct->setSpecialToDate ( $specialToDate );
        } else {
            $catelogProduct->setSpecialToDate ( '' );
        }
        
        $catelogProduct->save ();
        /**
         * checking store view metatitle
         */
        if($productData['store_meta_title'][$_storeId]){
            /**
             * update store view data metaTitle
             */
            $catelogProduct->setMetaTitle($productData['store_meta_title'][$_storeId])->save();
        }
        /**
         * checking store view metakeyword
         */
        if($productData['store_meta_keyword'][$_storeId]){
            /**
             * update store view data metakeyword
             */
            $catelogProduct->setMetaKeyword($productData['store_meta_keyword'][$_storeId])->save();
        }
        /**
         * checking store view metadescription
         */
        if($productData['store_meta_description'][$_storeId]){
            /**
             * update store view data metadescription
             */
            $catelogProduct->setMetaDescription($productData['store_meta_description'][$_storeId])->save();
        }
        
        endif;
        
    }
    /**
     * Function to get new product params
     * @return void
     */
    public function newproductPost($productData,$_eachStoreId,$productId,$sellerDefaultLangId){

        /**
         * define value to variable "$_storeId"
         */
        $_storeId = Mage::app ()->getStore ( $_eachStoreId )->getId ();
        /**
         * load product
         * based on store id
         */
        /**
         * getting each store lang
         */
        if ($_storeId != $sellerDefaultLangId) :
        $catalogProduct = Mage::getSingleton ( 'catalog/product' )->setStoreId ( $_storeId )->load ( $productId );
        /**
         * update product data in storeview
         */
        if ($storeName = $productData ['store_name'] [$_storeId]) {
            $catalogProduct->setName ( $storeName );
        }
        if ($storeDesc = $productData ['store_description'] [$_storeId]) {
            $catalogProduct->setDescription ( $storeDesc );
        }
        if ($storeShortDesc = $productData ['store_short_description'] [$_storeId]) {
            $catalogProduct->setShortDescription ( $storeShortDesc );
        }
        $catalogProduct->save ();
        $specialFromDate = $productData ['special_from_date'];
        $specialToDate = $productData ['special_to_date'];
        /**
         * save special price date to all store view
         */
        if (! empty ( $specialFromDate )) {
            $catalogProduct->setSpecialFromDate ( $specialFromDate );
        } else {
            $catalogProduct->setSpecialFromDate ( '' );
        }
        if (! empty ( $specialToDate )) {
            $catalogProduct->setSpecialToDate ( $specialToDate );
        } else {
            $catalogProduct->setSpecialToDate ( '' );
        }
        $catalogProduct->save ();
        /**
         * checking store value of meta title
         */
        if ($productData ['store_meta_title'] [$_storeId]) {
            /**
             * update store value of meta title
             */
            $catalogProduct->setMetaTitle ( $productData ['store_meta_title'] [$_storeId] )->save ();
        }
        /**
         * checking store value of meta keyword
         */
        if ($productData ['store_meta_keyword'] [$_storeId]) {
            /**
             * update store value of meta keyword
             */
            $catalogProduct->setMetaKeyword ( $productData ['store_meta_keyword'] [$_storeId] )->save ();
        }
        /**
         * checking store value of meta description
         */
        if ($productData ['store_meta_description'] [$_storeId]) {
            /**
             * update store value of meta description
             */
            $catalogProduct->setMetaDescription ( $productData ['store_meta_description'] [$_storeId] )->save ();
        }
        
        endif;
        
    }
    /**
     * Function to get Shipping Options
     * @return array
     */
    public function getShippingOptionForProduct($productInfo, $shippingOption , $nationalShippingPrice , $internationalShippingPrice , $defaultCountry){
        if (isset ( $productInfo ['seller_shipping_option'] )) {
            $shippingOption = $productInfo ['seller_shipping_option'];
        }
        if (isset ( $productInfo ['national_shipping_price'] )) {
            $nationalShippingPrice = $productInfo ['national_shipping_price'];
        } else {
            $nationalShippingPrice = 0;
        }
        if (isset ( $productInfo ['international_shipping_price'] )) {
            $internationalShippingPrice = $productInfo ['international_shipping_price'];
        } else {
            $internationalShippingPrice = 0;
        }
        if (isset ( $productInfo ['default_country'] )) {
            $defaultCountry = $productInfo ['default_country'];
        }
        
        return array('shipping_option'=>$shippingOption,'national_price'=>$nationalShippingPrice,'international_price'=>$internationalShippingPrice,'default_country'=>$defaultCountry);
    }
    
    /**
     * Function to get Url for disapproved seller
     * @return url
     */
    public function sellerUrl() {
    	return Mage::getUrl ( 'marketplace/general/disapprovedseller' );
    }
    /**
     * Function to get Url for adding Zipcode 
     * @return url
     */
    public function getZipcodeUrl() {
    	return Mage::getUrl ( 'marketplace/zipcode/zipcodeforseller' );
    }
    /**
     * Function to get Url for Zipcode Management
     * @return url
     */
    public function getZipcodeManageUrl() {
    	return Mage::getUrl ( 'marketplace/zipcode/manage' );
    }
    
}