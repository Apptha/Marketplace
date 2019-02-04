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
 * @version     1.9.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
class Apptha_Superdeals_Model_Order_Payment extends Mage_Sales_Model_Order_Payment {
    
    /**
     * Decide whether authorization transaction may close (if the amount to capture will cover entire order)
     *
     * @param float $amountToCapture            
     * @return bool
     */
    protected function _isCaptureFinal($amountToCapture) {
        /**
         * Declare repeted string variable
         */
        $coreResource = 'core/resource';
        $coreWrite = 'core_write';
        $processing = 'processing';
        $orderNo = 'order_no=?';
        $superdealsOrders = 'superdeals_orders';
        /**
         * load order item
         */
        foreach ( $this->getOrder ()->getAllItems () as $oOrderItems ) {
            /**
             * get order id from loop value
             */
            $ordrId = $this->getOrder ()->getIncrementId ();
            $connection = Mage::getSingleton ( $coreResource )->getConnection ( $coreWrite );
            $connection->beginTransaction ();
            
            $fields = array ();
            $fields = $this->statusForPayment ( $fields, $oOrderItems, $processing, $Processing );
            
            /**
             * check condition if order item is equal to pending paypal
             */
            if ($oOrderItems ()->getStatus () == 'pending_paypal') {
                $fields ['status'] = 'Pending PayPal';
            }
            
            /**
             * update and commit order status
             */
            $where = $connection->quoteInto ( $orderNo, $ordrId );
            $connection->update ( $superdealsOrders, $fields, $where );
            $connection->commit ();
        }
        return parent::_isCaptureFinal ( $amountToCapture );
    }
    
    /**
     * Set status for payment
     *
     * @param array $fields            
     * @param array $oOrderItems            
     * @param string $processing            
     * @param string $Processing            
     * @return array $fields
     */
    public function statusForPayment($fields, $oOrderItems, $processing, $Processing) {
        /**
         * check condition order item is equal to processing
         */
        $orderStatus = $oOrderItems ()->getStatus ();
        if ($orderStatus == $processing) {
            $fields ['status'] = $Processing;
        }
        /**
         * check condition if order item status is equal to complete
         */
        switch ($orderStatus) {
            case 'complete' :
                $fields ['status'] = 'Complete';
                break;
            
            case 'closed' :
                $fields ['status'] = 'Closed';
                break;
            
            case 'canceled' :
                $fields ['status'] = 'Canceled';
                break;
            
            case 'holded' :
                $fields ['status'] = 'On Hold';
                break;
            
            case 'fraud' :
                $fields ['status'] = 'Suspected Fraud';
                break;
            
            case 'payment_review' :
                $fields ['status'] = 'Payment Review';
                break;
            
            case 'pending' :
                $fields ['status'] = 'Pending';
                break;
            
            case 'pending_payment' :
                $fields ['status'] = 'Pending Payment';
                break;
            
            default :
                $fields ['status'] = 'Pending';
                break;
        }
        
        return $fields;
    }
}
