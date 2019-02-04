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
class Apptha_Marketplace_Helper_Transaction extends Mage_Core_Helper_Abstract {
    /**
     * Function to get view transaction url
     *
     * This Function will return the redirect url to view transaction
     *
     * @return string
     */
    public function getViewtransaction() {
        return Mage::getUrl ( 'marketplace/order/viewtransaction' );
    }
    /**
     * Function to get the received amount of seller
     *
     * This funtion will return the Total amount received by the seller from admin
     *
     * @return int
     */
    public function getAmountReceived() {
        $return = '';
        /**
         * Get Transaction Collection
         * Filter by
         * seller id and paid
         */
        $sellerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        $_collection = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToSelect ( 'seller_commission' )->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'paid', 1 );
        $_collection->getSelect ()->columns ( 'SUM(seller_commission) AS seller_commission' )->group ( 'seller_id' );
        /**
         * Increment foreach loop
         */
        foreach ( $_collection as $amount ) {
            $return = $amount->getSellerCommission ();
        }
        /**
         * return amount
         */
        return Mage::helper ( 'core' )->currency ( $return, true, false );
    }
    
    /**
     * Function to get the remaining amount of seller
     *
     * This funtion will return the Total remaining amount by admin to seller
     *
     * @return int
     */
    public function getAmountRemaining() {
        $return = '';
        /**
         * Get Seller Id
         * @var int
         */
        $sellerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * Get Transaction Collection
         * @var unknown
         */
        $_collection = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToSelect ( 'seller_commission' )->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'paid', 0 );
        $_collection->getSelect ()->columns ( 'SUM(seller_commission) AS seller_commission' )->group ( 'seller_id' );
        /**
         * Increment Foreach loop
         */
        foreach ( $_collection as $amount ) {
            $return = $amount->getSellerCommission ();
        }
        return Mage::helper ( 'core' )->currency ( $return, true, false );
    }
    
    /**
     * Function to get view order url
     *
     * Passed the order id in url to get the order details
     *
     * @param int $getOrderId
     *            Passed the product id in url to get the product details
     * @param int $getProductId
     *            This Function will return the redirect url to view order details
     * @return string
     */
    public function getVieworder($getOrderId, $getProductId) {
        return Mage::getUrl ( 'marketplace/order/vieworder', array (
                'orderid' => $getOrderId,
                'productid' => $getProductId 
        ) );
    }
    
    /**
     * Function to get Commission data
     *
     * Commission Id is passed to get the particular commission id's data
     *
     * @param int $commissionId
     *            This function will return the commission information as array
     * @return array
     */
    public function getCommissionInfo($commissionId) {
    /**
     * load commission details
     */
        return Mage::getModel ( 'marketplace/commission' )->load ( $commissionId, 'id' );
    }
    
    /**
     * Function to get Transaction data
     *
     * Commission id is passed to get the transaction details
     *
     * @param int $commissionId
     *            This function will return the transaction details as array
     * @return array
     */
    public function getTransactionInfo($commissionId) {
    /**
     * load transaction details
     */
        return Mage::getModel ( 'marketplace/transaction' )->load ( $commissionId, 'commission_id' );
    }
    
    /**
     * Function to save transaction data
     *
     * Transaction data is passed as array
     *
     * @param array $data
     *            This function will return true or false
     * @return bool
     */
    public function saveTransactionData($data) {
        Mage::getModel ( 'marketplace/transaction' )->setData ( $data )->save ();
        return true;
    }
    
    /**
     * Function to save transaction data
     *
     * Transaction Id is passed to update the transaction information
     *
     * @param int $transactionId
     *            Update the database table and will return void
     * @return void
     */
    public function updateTransactionData($transactionId) {
    /**
     * Get Current date and time
     * @var date
     */
        $now = Mage::getModel ( 'core/date' )->date ( 'Y-m-d H:i:s', time () );
        /**
         * Check whether transaction id is empty or not
         */
        if (! empty ( $transactionId )) {
            Mage::getModel ( 'marketplace/transaction' )->setPaid ( 1 )->setPaidDate ( $now )->setComment ( 'Paypal Adaptive Payment' )->setId ( $transactionId )->save ();
        }
    }
    
    /**
     * Function to update commission data
     *
     * Passed the order status to update in database table
     *
     * @param int $statusOrder
     *            Passed the commission id to update the data in database
     * @param int $commissionId
     *            This function will return true or false
     * @return bool
     */
    public function updateCommissionData($statusOrder, $commissionId) {
    /**
     * Check whether commission id is empty or not
     */
        if (! empty ( $commissionId )) {
        /**
         * load commission details and set order status
         */
            Mage::getModel ( 'marketplace/commission' )->setOrderStatus ( $statusOrder )->setId ( $commissionId )->save ();
        }
        return true;
    }
    /**
     * Function to save commission data
     *
     * Passed the commission data as array
     *
     * @param array $data
     *            Passed the commission id to save the commission data
     * @param int $commissionId
     *            This function will return true or false
     * @return bool
     */
    public function saveCommissionData($data, $commissionId) {
    /**
     * Set data and commission id
     */
        Mage::getModel ( 'marketplace/commission' )->setData ( $data )->setId ( $commissionId )->save ();
        return true;
    }

}