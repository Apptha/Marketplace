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
class Apptha_Marketplace_Helper_Vieworder extends Mage_Core_Helper_Abstract {
    /**
     * Get order product data
     *
     * @param number $sellerId            
     * @param number $orderId            
     * @return array
     */
    public function getOrderProductIds($sellerId, $orderId) {
        /**
         * Load commission model
         */
        $products = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        /**
         * Filter by seller id and order id
         */
        $products->addFieldToFilter ( 'seller_id', $sellerId );
        $products->addFieldToFilter ( 'order_id', $orderId );
        /**
         * Return product ids
         */
        return array_unique ( $products->getColumnValues ( 'product_id' ) );
    }
    
    /**
     * Get cancel order product data
     *
     * @param number $sellerId            
     * @param number $orderId            
     * @return array
     */
    public function cancelOrderItemProductIds($sellerId, $orderId) {
        /**
         * Load commission data
         */
        $products = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        /**
         * Filter by order status and customer id
         */
        $products->addFieldToFilter ( 'order_status', 'canceled' );
        $products->addFieldToFilter ( 'customer_id', array('in' => array(0,3)) );
        /**
         * Filter by order id and seller id
         */
        $products->addFieldToFilter ( 'order_id', $orderId );
        $products->addFieldToFilter ( 'seller_id', $sellerId );
        
        /**
         * Return product ids
         */
        return array_unique ( $products->getColumnValues ( 'product_id' ) );
    }
    
    /**
     * Check order status for seller
     *
     * @param array $orderItem            
     * @return string $checkOrderStatus
     *        
     */
    public function checkOrderStatusForSeller($orderItem) {
        /**
         * Initilize order status
         */
        $checkOrderStatus = 0;
        /**
         * Checking for virtual product or not
         */
        if ($orderItem->getIsVirtual () == 1) {
            /**
             * Checking Refunded or not
             */
            if ($orderItem->getQtyRefunded () >= $orderItem->getQtyOrdered ()) {
                $checkOrderStatus = - 2;
            /**
             * Checking invoice or not
             */
            } elseif ($orderItem->getQtyInvoiced () < $orderItem->getQtyOrdered ()) {
                $checkOrderStatus = 0;
            } else {
                $checkOrderStatus = 2;
            }
        } else {
            /**
             * Checking for refunded or not
             */
            if ($orderItem->getQtyRefunded () >= $orderItem->getQtyOrdered ()) {
                $checkOrderStatus = - 2;
            /**
             * Checking for invoice or not
             */
            } elseif ($orderItem->getQtyInvoiced () < $orderItem->getQtyOrdered () && $orderItem->getQtyShipped () < $orderItem->getQtyOrdered ()) {
                $checkOrderStatus = 0;
            /**
             * Checking for shipment or not
             */
            } elseif ($orderItem->getQtyInvoiced () >= $orderItem->getQtyOrdered () && $orderItem->getQtyShipped () < $orderItem->getQtyOrdered ()) {
                $checkOrderStatus = 1;
            } elseif ($orderItem->getQtyInvoiced () < $orderItem->getQtyOrdered () && $orderItem->getQtyShipped () >= $orderItem->getQtyOrdered ()) {
                $checkOrderStatus = 3;
            } else {
                $checkOrderStatus = 2;
            }
        }
        /**
         * Return order status
         */
        return $checkOrderStatus;
    }
    /**
     * Get order status for seller
     */
    public function getOrderStatusForSeller($orderDetails, $checkOrderStatusArr) {
        /**
         * Initilize order status
         */
        $orderStatus = '';
        /**
         * Checking for order status array
         */
        if (in_array ( 3, $checkOrderStatusArr )) {
            $orderStatus = $this->__ ( 'Shipped' );
        /**
         * Checing or refunded or not
         */
        } elseif (in_array ( 1, $checkOrderStatusArr )) {
            $orderStatus = $this->__ ( 'Processing' );
        /**
         * Checing or refunded or not
         */
        } elseif (Mage::helper ( 'marketplace/vieworder' )->checkRefundedOrNot ( $checkOrderStatusArr )) {
            $orderStatus = $this->__ ( 'Refunded' );
        /**
         * checking or completed or not
         */
        } elseif (in_array ( 2, $checkOrderStatusArr ) && ! in_array ( 1, $checkOrderStatusArr ) && ! in_array ( 0, $checkOrderStatusArr )) {
            $orderStatus = $this->__ ( 'Completed' );
        } else {
            /**
             * Checking for pending or not
             */
            $orderStatus = $this->__ ( 'Pending' );
            $orderPrdouctIds = Mage::helper ( 'marketplace/vieworder' )->getOrderProductIds ( Mage::getSingleton ( 'customer/session' )->getId (), $orderDetails->getId () );
            /**
             * prepare for items
             */
            foreach ( $orderDetails->getAllItems () as $item ) {
                /**
                 * Assign product id
                 */
                $itemProductId = $item->getProductId ();
                $orderItem = $item;
                if (in_array ( $itemProductId, $orderPrdouctIds )) {
                    if ($orderItem->getQtyShipped () >= 1 || $orderItem->getQtyInvoiced () >= 1) {
                        /**
                         * Set order status processing
                         */
                        $orderStatus = $this->__ ( 'Processing' );
                    }
                    break;
                }
            }
        }
        /**
         * Return order status
         */
        return $orderStatus;
    }
    
    /**
     * Check refunded or not
     *
     * @param
     *            $checkOrderStatusArr
     * @return string
     */
    public function checkRefundedOrNot($checkOrderStatusArr) {
        /**
         * Initilize status
         */
        $status = 0;
        /**
         * checking for status
         */
        if (in_array ( - 2, $checkOrderStatusArr ) && ! in_array ( 2, $checkOrderStatusArr ) && ! in_array ( 1, $checkOrderStatusArr ) && ! in_array ( 0, $checkOrderStatusArr )) {
            $status = 1;
        }
        /**
         * Return status
         */
        return $status;
    }
    /**
     * Get order status
     *
     * @param number $orderId            
     * @param number $productId            
     * @return string
     */
    public function getOrderStatus($orderId, $productId) {
        /**
         * Load commission model
         */
        $products = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        /**
         * Filter model by order id and product id
         */
        $products->addFieldToFilter ( 'order_id', $orderId );
        $products->addFieldToFilter ( 'product_id', $productId );
        /**
         * Return order status
         */
        return $products->getFirstItem ()->getOrderStatus ();
    }
    
    /**
     * Get seller shipping products by order id
     *
     * @param number $getOrderId            
     * @param number $getSellerId            
     */
    public function getShippingProductDetails($getOrderId, $getSellerId) {
        /**
         * Load commission model
         */
        $products = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        /**
         * Filter by seller id and order id
         */
        $products->addFieldToFilter ( 'seller_id', $getSellerId );
        $products->addFieldToFilter ( 'order_id', $getOrderId );
        
        /**
         * Get product ids
         */
        $productIds = array_unique ( $products->getColumnValues ( 'product_id' ) );
        
        /**
         * Initilize type ids
         */
        $typeIds = array (
                'simple',
                'configurable' 
        );
        $productsCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( array (
                'name' 
        ) )->addAttributeToFilter ( 'entity_id', array (
                'in' => $productIds 
        ) )->addAttributeToFilter ( 'type_id', array (
                'in' => $typeIds 
        ) );
        /**
         * Getting name array
         */
        $productNames = array_unique ( $productsCollection->getColumnValues ( 'name' ) );
        /**
         * Return product names
         */
        return implode ( ',', $productNames );
    }
    
    /**
     * Getting order status based on seller items for the particular order
     *
     * @param array $item            
     * @return string $status
     */
    public function getOrderStatusForSellerItemsBased($item) {
        /**
         * Initilize status
         */
        $status = '';
        $orderItem = $item;
        $completedMsg = $this->__ ( 'completed' );
        
        /**
         * Checking for refunded or not
         */
        if ($orderItem->getQtyRefunded () >= $orderItem->getQtyOrdered ()) {
            $status = $this->__ ( 'refunded' );
        /**
         * Checking for completed or not
         */
        } elseif ($orderItem->getIsVirtual () == 1 && $orderItem->getQtyInvoiced () >= $orderItem->getQtyOrdered ()) {
            $status = $completedMsg;
        /**
         * Checking for completed or not
         */
        } elseif ($orderItem->getIsVirtual () != 1 && $orderItem->getQtyInvoiced () >= $orderItem->getQtyOrdered () && $orderItem->getQtyShipped () >= $orderItem->getQtyOrdered ()) {
            $status = $this->__ ( 'completed' );
        /**
         * Checking for processing or not
         */
        } elseif ($orderItem->getIsVirtual () != 1 && $orderItem->getQtyInvoiced () >= $orderItem->getQtyOrdered () || $orderItem->getIsVirtual () != 1 && $orderItem->getQtyShipped () >= $orderItem->getQtyOrdered ()) {
            $status = $this->__ ( 'processing' );
        } else {
            /**
             * Checking for pending or not
             */
            $status = $this->__ ( 'pending' );
        }
        /**
         * Return status
         */
        return $status;
    }
}