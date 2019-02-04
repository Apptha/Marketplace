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
 * This file is used to display seller dashboard with functionalities like Total sales, Average orders,
 * Last five orders, Most viewed products and Sales report
 */
class Apptha_Marketplace_Block_Seller_Dashboard_Dashboard extends Mage_Core_Block_Template {
    
    /**
     * Function to get profile url
     *
     * Return the seller profile url
     *
     * @return string
     */
    function profileUrl() {
        return Mage::getUrl ( 'marketplace/seller/addprofile' );
    }
    /**
     * Function to get most viewed product information
     *
     * Return the Most viewed products as array
     *
     * @return array
     */
    public function mostViewed($id) {
        
        /**
         * get store id
         */
        $storeId = Mage::app ()->getStore ()->getId ();
        /**
         * get reports based on the seller
         */
        /**
         * This collection is taked for compatible with magento flat catalog data.
         */
        $_productCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->setDisableFlat ( true )->addAttributeToSelect ( '*' )->addFieldToFilter ( "seller_id", $id )->load ();
        $productIdArray = array ();
        foreach ( $_productCollection as $keyColletion ) {
            $productIdArray [] = $keyColletion ['entity_id'];
        }
        /**
         * get reports colletion for Most viewed product
         */
        $productsCollection = Mage::getResourceModel ( 'reports/product_collection' )->addAttributeToSelect ( '*' )->addAttributeToSelect ( array (
                'name',
                'price' 
        ) )->setStoreId ( $storeId )->addStoreFilter ( $storeId )->addViewsCount ()->addAttributeToFilter ( "entity_id", array (
                'in' => $productIdArray 
        ) );
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
        $tablePrefix = Mage::getConfig ()->getTablePrefix ();
        $productsCollection->getSelect ()->joinInner ( array (
                'e2' => $tablePrefix . 'catalog_product_flat_' . $storeId 
        ), 'e2.entity_id = e.entity_id' );
        }         
        Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $productsCollection );
        Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $productsCollection );
        $productsCollection->setPageSize ( 5 )->setCurPage ( 1 );
        return $productsCollection;
    }
    /**
     * Getting sales report collection
     * Passed the From date as $dbFrom to sort the sales orders
     * 
     * @param int $dbFrom
     *            Passed the To date as $dbTo to sort the sales orders
     * @param int $dbTo
     *            Passed the seller id as $id to get particular seller orders
     * @param int $id
     *            Return commission collection as array
     * @return array
     *
     */
    public function advancedSalesReportCollection($dbFrom, $dbTo, $id) {
        /**
         * get Commission collection
         */
        return Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'seller_id', $id )->addFieldToFilter ( 'created_at', array (
                'from' => $dbFrom,
                'to' => $dbTo 
        ) );
    }
    /**
     * Total Orders for a seller
     *
     * @param number $id            
     * @return number
     */
    public function getTotalOrders($id) {
        /**
         * get Commission collection
         */
        $collection = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $collection->addFieldToSelect ( '*' );
        $collection->addFieldToFilter ( 'seller_id', $id );
        $collection->getSelect ()->group ( 'order_id' );
        return count ( $collection );
    }
    /**
     * Complete status order
     *
     * @param number $id            
     * @return number
     */
    public function getCompleteStatus($id) {
        /**
         * Getting for completed order status based on seller items
         */
        return $this->getOrderCount ( $id, 'completed' );
    }
    /**
     * Pending status order
     *
     * @param number $id            
     * @return number
     */
    public function getPendingStatus($id) {
        /**
         * Getting order count for pending
         */
        return $this->getOrderCountForPendingProcessing ( $id, 'pending' );
    }
    
    /**
     * Getting order count for processing
     *
     * @param number $id            
     * @return number
     */
    public function getProcessingStatus($id) {
        /**
         * get Commission collection
         */
        return $this->getOrderCountForPendingProcessing ( $id, 'processing' );
    }
    
    /**
     * Getting order count for pending and processing
     *
     * @param number $id            
     * @param string $status            
     * @return number
     */
    public function getOrderCountForPendingProcessing($id, $status) {
        $collection = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $collection->addFieldToSelect ( '*' );
        $collection->addFieldToFilter ( 'seller_id', $id );
        $collection->addFieldToFilter ( 'item_order_status', $status );
        $orderIds = array_unique ( $collection->getColumnValues ( 'order_id' ) );
        return count ( $orderIds );
    }
    
    /**
     * Cancelled status order
     *
     * @param number $id            
     * @return number
     */
    public function getCancelledStatus($id) {
        /**
         * Getting for canceled order status based on seller items
         */
        return $this->getOrderCount ( $id, 'canceled' );
    }
    /**
     * Refunded status order
     *
     * @param number $id            
     * @return number
     */
    public function getRefundedStatus($id) {
        /**
         * Getting for refunded order status based on seller items
         */
        return $this->getOrderCount ( $id, 'refunded' );
    }
    
    /**
     * Get order count for completed, refunded and canceled orders
     *
     * @param number $id            
     * @param string $status            
     * @return number
     */
    public function getOrderCount($id, $status) {
        /**
         * Getting for completed,canceled and refunded order status based on seller items
         */
        
        /**
         * Initilize status array for checking
         */
        if ($status == 'completed') {
            $statusArr = array (
                    'pending',
                    'processing',
                    'onhold' 
            );
        } elseif ($status == 'canceled') {
            $statusArr = array (
                    'pending',
                    'processing',
                    'onhold',
                    'completed' 
            );
        } else {
            $statusArr = array ('onhold','completed','pending','processing');
        }
        
        /**
         * Getting order collection based on seller items
         */
        $collection = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $collection->addFieldToSelect ( '*' );
        $collection->addFieldToFilter ( 'seller_id', $id );
        $collection->addFieldToFilter ( 'item_order_status', $status );
        $orderIds = array_unique ( $collection->getColumnValues ( 'order_id' ) );
        /**
         * Return order items count zero
         */
        if (count ( $orderIds ) <= 0) {
            return 0;
        }
        $statusCollection = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $statusCollection->addFieldToSelect ( '*' );
        $statusCollection->addFieldToFilter ( 'seller_id', $id );
        $statusCollection->addFieldToFilter ( 'order_id', array (
                'in' => $orderIds 
        ) );
        $statusCollection->addFieldToFilter ( 'item_order_status', array (
                'in' => $statusArr 
        ) );
        $statusOrderIds = array_unique ( $statusCollection->getColumnValues ( 'order_id' ) );
        /**
         * Getting order count for seller items
         */
        $diffArr = array_diff ( $orderIds, $statusOrderIds );
        return count ( $diffArr );
    }
    
    /**
     * Onhold status order
     *
     * @param number $id            
     * @return number
     */
    public function getOnholdStatus($id) {
        /**
         * get Commission collection
         */
        $collection = Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'seller_id', $id )->addFieldToFilter ( 'order_status', 'holded' );
        return count ( $collection );
    }
    
    /**
     * Getting last week date
     */
    public function getLastWeekDate() {
        $lastWeekDate = array ();
        /**
         * getting to
         */
        $to = date ( 'd-m-Y' );
        /**
         * getting today
         */
        $toDay = date ( 'l', strtotime ( $to ) );
        if ($toDay == 'Monday') {
            /**
             * start day
             */
            $startDay = strtotime ( "-1 monday midnight" );
            /**
             * endday
             */
            $endDay = strtotime ( "yesterday" );
        } else {
            $startDay = strtotime ( "-2 monday midnight" );
            $endDay = strtotime ( "-1 sunday midnight" );
        }
        /**
         * last week dates
         */
        $lastWeekDate [] = $startDay;
        $lastWeekDate [] = $endDay;
        
        return $lastWeekDate;
    }
} 



