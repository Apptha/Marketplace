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
class Apptha_Marketplace_Helper_Marketplace extends Mage_Core_Helper_Abstract {
    
    /**
     * Function to load particular seller information
     *
     * In this function seller id is passed to get particular seller data
     *
     * @param int $_id
     *            This function will return the particular seller information as array
     * @return array
     */
    public function getSellerCollection($_id) {
    /** load collection based on seller id */
        return Mage::getModel ( 'marketplace/sellerprofile' )->load ( $_id, 'seller_id' );
    }
    
    /**
     * Function to delete product
     *
     * Product entity id are passed to delete the product
     *
     * @param int $entityIds
     *            This function will return true or false
     * @return bool
     */
    public function deleteProduct($entityIds) {
        $productSellerId = Mage::getModel ( 'catalog/product' )->load ( $entityIds )->getSellerId ();
        if ($productSellerId == Mage::getSingleton ( 'customer/session' )->getCustomerId ()) {
            Mage::helper ( 'marketplace/general' )->changeAssignProductId ( $entityIds );
            Mage::getModel ( 'catalog/product' )->setId ( $entityIds )->delete ();
        }
        return true;
    }
    
    /** * Function to get the Resized category image */ 
    function getResizeImage($image, $width, $height) {
        $mediaPath = Mage::getBaseDir ( 'media' ) . "/catalog/category/resized/";
        if (! is_dir ( $mediaPath )) {
            mkdir ( $mediaPath, 0777 );
        }
        if ($image != '') {
            $_imageUrl = Mage::getBaseDir ( 'media' ) . "/catalog/category/" . $image;
            $_imageUrl = Mage::getBaseDir ( 'media' ) . "/catalog/category/" . $image;
            $imageResized = Mage::getBaseDir ( 'media' ) . "/catalog/category/resized/" . $image;
            $imageObj = new Varien_Image ( $_imageUrl );
            $imageObj->constrainOnly ( TRUE );
            $imageObj->keepAspectRatio ( TRUE );
            $imageObj->keepFrame ( FALSE );
            $imageObj->resize ( $width, $height );
            $imageObj->save ( $imageResized );
            $mediaUrl = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_MEDIA );
            $imageResized = $mediaUrl . "/catalog/category/resized/" . $image;
        }
        return $imageResized;
    }
    /**
     * Function to get product collection
     *
     * Product id is passed to get the particular product information
     *
     * @param int $getProductId
     *            This function will display the particular product information as array
     * @return array
     */
    public function getProductInfo($getProductId) {
        return Mage::getModel ( 'catalog/product' )->load ( $getProductId );
    }
    
    /**
     * Function to load email template
     *
     * Passed the template id to load the email template
     *
     * @param int $templateId
     *            This function will return the email template
     * @return string
     */
    public function loadEmailTemplate($templateId) {
        return Mage::getModel ( 'core/email_template' )->load ( $templateId );
    }
    
    /**
     * Function to set product instock
     *
     * Passed the Product is instock or not value
     *
     * @param int $isInStock
     *            This function will return 0 or 1
     * @return bool
     */
    public function productInStock($isInStock) {
        if (isset ( $isInStock )) {
            return $stock_data ['is_in_stock'] = $isInStock;
        } else {
            return $stock_data ['is_in_stock'] = 1;
        }
    }

    
    /**
     * Retrieve attribute id for seller shipping
     *
     * This function will return the seller shipping id
     *
     * @return int
     */
    public function getSellerShipping() {
        return Mage::getResourceModel ( 'eav/entity_attribute' )->getIdByCode ( 'catalog_product', 'seller_shipping_option' );
    }
    /**
     * Load particular product info
     *
     * @param Mage_Catalog_Model_Product $product            
     */
    protected function _loadProduct(Mage_Catalog_Model_Product $product) {
        $product->load ( $product->getId () );
    }
    /**
     * Get the New and Sale Label for a particular product
     *
     * @param Mage_Catalog_Model_Product $product            
     * @return string
     */
    public function getLabel(Mage_Catalog_Model_Product $product) {
        $html = '';
        $this->_loadProduct ( $product );
        if ($this->_isNew ( $product )) {
            $html .= '<div class="new-label new-right' . '">New</div>';
        }
        if ($this->_isOnSale ( $product )) {
            $html .= '<div class="sale-label sale-left">Sale</div>';
        }
        return $html;
    }
    /**
     * Checking the from and to date for new and sale product
     *
     * @param unknown $from            
     * @param unknown $to            
     * @return boolean
     */
    protected function _checkDate($from, $to) {
        $return = true;
        $date = date( 'Y-m-d');
        $today = strtotime ($date);
        if ($from && $today < $from) {
            $return = false;
        }
        if ($to && $today > $to) {
            $return = false;
        }
        if (! $to && ! $from) {
            $return = false;
        }
        return $return;
    }
    /**
     * Check whether a product is set as new
     *
     * @param unknown $product            
     */
    protected function _isNew($product) {
        $from = strtotime ( $product->getData ( 'news_from_date' ) );
        $to = strtotime ( $product->getData ( 'news_to_date' ) );
        return $this->_checkDate ( $from, $to );
    }
    /**
     * check whether a product is set for sale
     *
     * @param unknown $product            
     */
    protected function _isOnSale($product) {
        $from = strtotime ( $product->getData ( 'special_from_date' ) );
        $to = strtotime ( $product->getData ( 'special_to_date' ) );
        return $this->_checkDate ( $from, $to );
    }
 
    /**
     * Resize category images to display
     *
     * Return image url
     *
     * @return string
     */
    public function getResizedImage($imagePath, $width, $height = null, $quality = 100) {
        
        $return = '';
        $imageUrl = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . "category" . DS . $imagePath;
        
        if (! $imagePath || ! is_file ( $imageUrl )) {
            $return = false;
        } else {
            /**
             * Because clean Image cache function works in this folder only
             */
            $imageResized = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . 'product' . DS . "cache" . DS . "cat_resized" . DS . $width . $imagePath;
            if (! file_exists ( $imageResized ) && file_exists ( $imageUrl ) || file_exists ( $imageUrl ) && filemtime ( $imageUrl ) > filemtime ( $imageResized )) :
                $imageObj = new Varien_Image ( $imageUrl );
                $imageObj->constrainOnly ( true );
                $imageObj->keepAspectRatio ( false );
                $imageObj->keepFrame ( false );
                $imageObj->quality ( $quality );
                $imageObj->resize ( $width, $height );
                $imageObj->save ( $imageResized );
            
   
endif;
            
            if (file_exists ( $imageResized )) {
                $return = Mage::getBaseUrl ( 'media' ) . "catalog/product/cache/cat_resized/" . $width . $imagePath;
            } else {
                $return = $imagePath;
            }
        }
        return $return;
    
    }
    
    /**
     * Function to get the dashboard url
     *
     * This Function will return the redirect url to dashboard
     *
     * @return string
     */
    public function dashboardUrl() {
    return Mage::getUrl ( 'marketplace/seller/dashboard' );
    } 
    
    /**
     * Function to get the received amount of seller
     *
     * This funtion will return the Total amount received by the seller from admin
     * @return int
     */
    public function getAmountReceived() {
        $returnContent = "";
        /**
         * Get Customer Session
         * @var unknown
         */
        $getSellerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $transactionCollection = Mage::getModel('marketplace/transaction')->getCollection()
        ->addFieldToSelect('seller_commission')->addFieldToFilter('seller_id', $getSellerId)->addFieldToFilter('paid', 1);
        $transactionCollection->getSelect()->columns('SUM(seller_commission) AS seller_commission')->group('seller_id');
        foreach ($transactionCollection as $amounts) {
        $returnContent = $amounts->getSellerCommission();
        }
        $collectionPaidUpto = Mage::getModel('marketplace/transaction')->getCollection()->addFieldToSelect('paid_upto')->addFieldToFilter('seller_id', $getSellerId)->addFieldToFilter('paid', 0);
        $collectionPaidUpto->getSelect()->columns('SUM(paid_upto) AS paid_upto')->group('seller_id');
         foreach ($collectionPaidUpto as $amountKey)  {
        $returnContent = $returnContent + $amountKey->getPaidUpto();
        }
        return Mage::helper('core')->currency($returnContent, true, false);
    }
    
    /**
     * Function to get the remaining amount of seller
     *
     * This funtion will return the Total remaining amount by admin to seller
     * @return int
     */
    public function getAmountRemaining() {
        $paidUpto = null;
         $return = '';
         $sellerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
         /**
          * Get Transaction Collection
          * @var unknown
          */
        $_collection = Mage::getModel('marketplace/transaction')->getCollection()
        ->addFieldToSelect('seller_commission')
        ->addFieldToFilter('seller_id', $sellerId)
        ->addFieldToFilter('paid', 0);
        $_collection->getSelect()
        ->columns('SUM(seller_commission) AS seller_commission')
        ->group('seller_id');
        foreach ($_collection as $amount) {
         $return = $amount->getSellerCommission();
         }
    
         $collectionPaidUpto = Mage::getModel('marketplace/transaction')->getCollection()
         ->addFieldToSelect('paid_upto')
         ->addFieldToFilter('seller_id', $sellerId)
         ->addFieldToFilter('paid', 0);
        $collectionPaidUpto->getSelect()
         ->columns('SUM(paid_upto) AS paid_upto')
         ->group('seller_id');
         foreach ($collectionPaidUpto as $amount) {
         $paidUpto = $amount->getPaidUpto();
         }
         $return = $return - $paidUpto;
            return Mage::helper('core')->currency($return, true, false);
    }
    
    
    
    /**
     * Get subcribed date
     * Passed seller id to get the seller customer registered date
     *
     * @param $sellerId Return
     *        subscribed date
     * @return date
     */
    public function subscribedDate($sellerId) {
    /**
     * get seller/designer subscribed date
     */
    $collection = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $sellerId, 'seller_id' );
    if ($collection) {
    return date ( "jS F Y", strtotime ( $collection->getPaidDate () ) );
    }
    }
    /**
     * Get registered expire date
     * Passed seller id to get the seller customer registered date
     *
     * @param $sellerId Return
     * registered trial expire date
     * @return date
     */
    public function subscriptionEndDate($sellerId) {
    $subscribed = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $sellerId, 'seller_id' );
    if ($subscribed->getId ()) {
    $subscriptionStartDate = $subscribed->getPaidDate ();
    $planId = $subscribed->getPlanId ();
    /**
     * load by subscription id
     * @var id
     */
    $getPlanInfo = Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId, 'plan_id' );
    $subscriptionPeriod = $getPlanInfo->getSubscriptionPeriod ();
    $validityPeriod = $getPlanInfo->getValidityPeriod ();
    if ($getPlanInfo->getOfferPeriod () != '' && $getPlanInfo->getOfferValidityPeriod () != '') {
    $offerPeriod = $getPlanInfo->getOfferPeriod ();
    $offerValidityPeriod = $getPlanInfo->getOfferValidityPeriod ();
    }
    if ($subscriptionPeriod == 1 && $offerPeriod == 1) {
    $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $validityPeriod + $offerValidityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
    } elseif ($subscriptionPeriod == 2 && $offerPeriod == 2) {
    $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $validityPeriod + $offerValidityPeriod . ' years', strtotime ( $subscriptionStartDate ) ) );
    } elseif ($subscriptionPeriod == 1 && $offerPeriod == 2) {
    $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $offerValidityPeriod . ' years ' . $validityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
    } elseif ($subscriptionPeriod == 2 && $offerPeriod == 1) {
    $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $validityPeriod . ' years ' . $offerValidityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
    } elseif ($subscriptionPeriod == 2 && $offerPeriod == 0) {
    $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $validityPeriod . ' years ', strtotime ( $subscriptionStartDate ) ) );
    } elseif ($subscriptionPeriod == 1 && $offerPeriod == 0) {
    $subscriptionEndDate = date ( 'Y-m-d', strtotime ( '+' . $validityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
    }
    }
    return $subscriptionEndDate;
    }
    
    public function outofstock($productId){
    /**
     * Get Product Collection
     * @var unknown
     */
    $assignProducts = Mage::getResourceModel('catalog/product_collection')
    ->addAttributeToSelect(array('name'))
    ->addAttributeToFilter('assign_product_id', $productId)
    ->load();
    
    return count($assignProducts);
    }
    
    /**
     * Seller added product count
     *
     *  Passed seller id to the product collection
     *  @param $sellerId
     *
     *  return the number of products
     *  @return int;
     */
    public function getProductCount($sellerId){
    $product = Mage::getModel('catalog/product')->getCollection()
    ->addAttributeToFilter('seller_id',$sellerId);
    return count($product);
    }
}