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
 * Function written in this file are used for seller order management
 */
class Apptha_Marketplace_Model_Order extends Mage_Core_Model_Abstract {
    
    /**
     * Set order item cancel/refund request status for customer
     *
     * @param number $itemProductId            
     * @param number $orderId            
     * @param number $loggedInCustomerId            
     * @param number $sellerId            
     * @param
     *            number status
     * @return void
     */
    public function updateSellerRequest($itemProductId, $orderId, $loggedInCustomerId, $sellerId, $value) {
        
        /**
         * Checking for product id , order id and customer d
         */
        if (! empty ( $itemProductId ) && ! empty ( $orderId ) && ! empty ( $loggedInCustomerId )) {
            /**
             * Get product from commission model
             */
            $products = Mage::getModel ( 'marketplace/commission' )->getCollection ();
            $products->addFieldToSelect ( '*' );
            /**
             * Filter by value
             */
            if ($value == 2) {
                $products->addFieldToFilter ( 'seller_id', $loggedInCustomerId );
            } else {
                $products->addFieldToFilter ( 'seller_id', $sellerId );
            }
            $products->addFieldToFilter ( 'order_id', $orderId );
            $products->addFieldToFilter ( 'product_id', $itemProductId );
            /**
             * Getting first data id
             */
            $collectionId = $products->getFirstItem ()->getId ();
            /**
             * Checking for first data exist or not
             */
            if (! empty ( $collectionId )) {
                $data = array ();
                if ($value == 2) {
                    $data = array (
                            'refund_request_seller' => 1 
                    );
                } elseif ($value == 1) {
                    $data = array (
                            'refund_request_customer' => 1 
                    );
                } else {
                    $data = array (
                            'cancel_request_customer' => 1 
                    );
                }
                /**
                 * Update date for commission model
                 */
                $model = Mage::getModel ( 'marketplace/commission' )->load ( $collectionId )->addData ( $data );
                /**
                 * Save model
                 */
                $model->setId ( $collectionId )->save ();
            }
        }
    }
    
    /**
     * Getting request status for customer order item
     *
     * @param number $itemProductId            
     * @param number $orderId            
     * @param number $loggedInCustomerId            
     * @param number $value            
     * @return boolean $status
     */
    public function getItemRequestStatus($itemProductId, $orderId, $loggedInCustomerId, $value) {
        /**
         * Load commission model
         */
        $products = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        /**
         * Filter by order id and product id
         */
        $products->addFieldToFilter ( 'order_id', $orderId );
        $products->addFieldToFilter ( 'product_id', $itemProductId );
        
        /**
         * Checking for value
         */
        if ($value == 2 || $value == 3 || $value == 4) {
            $products->addFieldToFilter ( 'seller_id', $loggedInCustomerId );
        }
        /**
         * Checking for value
         */
        $statusFlagValue = 0;
        if ($value == 4) {
            $status = $products->getFirstItem ()->getRefundRequestSeller ();
        } elseif ($value == 3) {
            $status = $products->getFirstItem ()->getCancelRequestCustomer ();
        } elseif ($value == 1 || $value == 2) {
            $status = $products->getFirstItem ()
                               ->getRefundRequestCustomer ();
        } else { 
        $statusFlagValue = 1;          
        }
        if($statusFlagValue == 1){
        $status = $products->getFirstItem ()->getCancelRequestCustomer ();
        }  

        /**
         * Return status
         */
        return $status;
    }
    
    /**
     * Update order status for seller items
     *
     * @param array $itemsArr            
     * @param number $orderId            
     * @return void
     */
    public function updateOrderStatusForSellerItems($itemsArr, $orderId) {
        foreach ( $itemsArr as $item ) {
            /**
             * Get status based on order item
             */
            $status = Mage::helper ( 'marketplace/vieworder' )->getOrderStatusForSellerItemsBased ( $item );
            /**
             * Inilize product id
             */
            $itemProductId = $item->getProductId ();
            /**
             * Load commission collection
             */
            $products = Mage::getModel ( 'marketplace/commission' )->getCollection ();
            $products->addFieldToSelect ( '*' );
            /**
             * Filter by order id and product id
             */
            $products->addFieldToFilter ( 'order_id', $orderId );
            $products->addFieldToFilter ( 'product_id', $itemProductId );
            $getCollectionId = $products->getFirstItem ()->getId ();
            /**
             * Checking for collection id exist or not
             */
            if (! empty ( $getCollectionId )) {
                $data = array ();
                /**
                 * Initilize order item status
                 */
                $data = array ('item_order_status' => $status);
                if($status == 'completed'){
                $data['order_status'] = 'complete';
                }
                $model = Mage::getModel ( 'marketplace/commission' )->load ( $getCollectionId )->addData ( $data );
                /**
                 * Save model
                 */
                $model->setId ( $getCollectionId )->save ();
            }
        }
    }
    /**
     * Update cancel order status for seller items
     *
     * @param number $productId            
     * @param number $orderId            
     * @return void
     */
    public function updateCancelOrderStatusForSellerItems($productId, $orderId) {
        /**
         * Update canceled seller order items
         */
        $status = 'canceled';
        /**
         * Load commission model for seller item statu update
         */
        $products = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        /**
         * Filter by order id and product id
         */
        $products->addFieldToFilter ( 'order_id', $orderId );
        $products->addFieldToFilter ( 'product_id', $productId );
        /**
         * Get first item from model
         */
        $collectionId = $products->getFirstItem ()->getId ();
        if (! empty ( $collectionId )) {
            $data = array ();
            /**
             * Update seller order item status canceled
             */
            $data = array (
                    'item_order_status' => $status 
            );
            if($status == 'completed'){
            $data['order_status'] = 'complete';
            }
            $model = Mage::getModel ( 'marketplace/commission' )->load ( $collectionId )->addData ( $data );
            /**
             * Save commisstion model
             */
            $model->setId ( $collectionId )->save ();
        }
    }
    
    /**
     * Update seller order items based on shipping
     *
     * @param array $savedQtys            
     * @param array $order            
     * @return void
     */
    public function updateSellerOrderItemsBasedOnSellerItems($savedQtys, $orderId, $value) {
        /**
         * Load order by order id
         */
        $order = Mage::getModel ( 'sales/order' )->load ( $orderId );
        $itemsArr = array ();
        /**
         * Getting order items
         */
        foreach ( $order->getAllItems () as $item ) {
            $itemId = $item->getItemId ();
            /**
             * Checking for seller order items
             */
            if (array_key_exists ( $itemId, $savedQtys ) && $value != 1) {
                $itemsArr [] = $item;
            }
            /**
             * Prepare items for seller order status
             */
            if (in_array ( $itemId, $savedQtys ) && $value == 1) {
                $itemsArr [] = $item;
            }
        }
        /**
         * Update seller order item status
         */
        Mage::getModel ( 'marketplace/order' )->updateOrderStatusForSellerItems ( $itemsArr, $orderId );
    }
} 