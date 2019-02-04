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
 * Manage order information
 * Manage order information with seller details and also with pagination
 */
class Apptha_Marketplace_Block_Order_Manage extends Mage_Core_Block_Template {
    
    /**
     * Collection for manage orders
     *
     * @return \Apptha_Marketplace_Block_Order_Manage
     */
    protected function _prepareLayout() {
        parent::_prepareLayout ();
        /**
         * Get Seller Orders
         */
        $manageCollection = $this->getsellerOrders ();
        $this->setCollection ( $manageCollection );
        /**
         * Get Layout
         */
        $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $manageCollection );
        $pager->setAvailableLimit ( array (
                10 => 10,
                20 => 20,
                50 => 50 
        ) );
        /**
         * Set pager for manage order page
         */
        $this->setChild ( 'pager', $pager );
        return $this;
    }
    
    /**
     * Function to get pagination
     *
     * Return pagination for collection
     *
     * @return array
     */
    public function getPagerHtml() {
        /**
         * Get Child Html
         */
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Function to get seller order details
     *
     * Return seller orders information
     *
     * @return array
     */
    public function getsellerOrders() {
        /**
         * Convert local date to magento db date.
         */
    /**
     * Get Commission Collection
     * Filter by seller id,order id,seller amount
     * @var obj
     */
        $orders = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $orders->addFieldToSelect ( '*' );
        $orders->addFieldToFilter ( 'seller_id', Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId () );
        $orders->getSelect ()->columns ( 'SUM(seller_amount) as seller_amount' )->group ( 'order_id' );
        /**
         * Set order for manage order
         */
        $orders->setOrder ( 'order_id', 'desc' );
        /**
         * Return orders
         */
        return $orders;
    }
    
    /**
     * Get seller products by order id
     *
     * @param number $getOrderId            
     * @param number $getSellerId            
     */
    public function getProductDetails($getOrderId, $getSellerId) {
        /**
         * Getting seller product ids from order
         */
    /**
     * Get commission collection
     * Filter by order id,seller id
     * product id
     */
        $products = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        $products->addFieldToFilter ( 'order_id', $getOrderId );
        $products->addFieldToFilter ( 'seller_id', $getSellerId );
        $productIds = array_unique ( $products->getColumnValues ( 'product_id' ) );
        /**
         * Load order object using order id
         * @var unknown
         */
        $orderDet = Mage::getModel("sales/order")->load($getOrderId);
        /**
         * Get order items collection
         * @var unknown
         */
        $orderItems = $orderDet->getItemsCollection();
        
        $productNames = array();
        /**
         * Increment foreach loop
         */
        foreach ($orderItems as $item){
        if(in_array($item->product_id , $productIds)){
        $productNames[] = $item->name;
        }
        }
        /**
         * Return seller product names in particualr order
         */
        return implode ( ',', $productNames );
    }
    
    /**
     * Get seller shipping amount by order id
     *
     * @param number $getOrderId
     * @param number $getSellerId
     */
    public function getOrderShipment($getOrderId, $getSellerId) {
    /**
     * Getting seller product ids from order
     */
    /**
     * Get commission collection
     * Filter by
     * Order id,seller id
     */
    $orderSellerCollection = Mage::getModel ( 'marketplace/commission' )->getCollection ();
    $orderSellerCollection->addFieldToSelect ( '*' );
    $orderSellerCollection->addFieldToFilter ( 'order_id', $getOrderId );
    $orderSellerCollection->addFieldToFilter ( 'seller_id', $getSellerId );
    
    $sellerShippingAmount  = 0;
    /**
     * Increment foreach loop
     */
    foreach ($orderSellerCollection as $amount){
    $sellerShippingAmount = $sellerShippingAmount + $amount->getSellerShippingAmount();
    }
    /**
     * Return seller product names in particualr order
     */
    return $sellerShippingAmount;
    }
    /**
     * Function to get Seller shipping details
     * @param unknown $getOrderId
     * @param unknown $getSellerId
     */
    public function getSellerShippingDetails($getOrderId, $getSellerId) {
        /**
         * Getting seller product ids from order
         */
        $productDetails = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $productDetails->addFieldToSelect ( '*' );
        $productDetails->addFieldToFilter ( 'order_id', $getOrderId );
        $productDetails->addFieldToFilter ( 'seller_id', $getSellerId );
        $productIds = array_unique ( $productDetails->getColumnValues ( 'product_id' ) );
    
        $orderDetail = Mage::getModel("sales/order")->load($getOrderId);
    
        $orderItems = $orderDetail->getItemsCollection();
    
        $productNames = array();
        foreach ($orderItems as $item){
            if(in_array($item->product_id , $productIds)){
                $productNames[] = $item->name;
            }
        }
        $count = count($productNames);
        return $count;
    }


}