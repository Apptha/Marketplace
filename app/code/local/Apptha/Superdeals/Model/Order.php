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
class Apptha_Superdeals_Model_Order extends Mage_Sales_Model_Order {
    protected function _setState($state, $status = false, $comment = '', $isCustomerNotified = null, $shouldProtectState = false) {
        
        /**
         * dispatch an event before we attempt to do anything
         */
        Mage::dispatchEvent ( 'sales_order_status_before', array (
                'order' => $this,
                'state' => $state,
                'status' => $status,
                'comment' => $comment,
                'isCustomerNotified' => $isCustomerNotified,
                'shouldProtectState' => $shouldProtectState 
        ) );
        
        /**
         * attempt to set the specified state
         */
        if (($shouldProtectState) && ($this->isStateProtected ( $state ))) {
            Mage::throwException ( Mage::helper ( 'sales' )->__ ( 'The Order State "%s" must not be set manually.', $state ) );
        }
        $this->setData ( 'state', $state );
        
        /**
         * add status history
         */
        if ($status) {
            /**
             * check condition status is equal to true
             */
            if ($status === true) {
                $status = $this->getConfig ()->getStateDefaultStatus ( $state );
            }
            $this->setStatus ( $status );
            /**
             * no sense to set $status again
             */
            $history = $this->addStatusHistoryComment ( $comment, false );
            /**
             * for backwards compatibility
             */
            $history->setIsCustomerNotified ( $isCustomerNotified );
        }
        
        /**
         * dispatch an event after status has changed
         */
        Mage::dispatchEvent ( 'sales_order_status_after', array (
                'order' => $this,
                'state' => $state,
                'status' => $status,
                'comment' => $comment,
                'isCustomerNotified' => $isCustomerNotified,
                'shouldProtectState' => $shouldProtectState 
        ) );
        Mage::dispatchEvent ( 'sales_order_payment_capture', array (
                'order' => $this,
                'state' => $state,
                'capturestatus' => $status,
                'comment' => $comment,
                'isCustomerNotified' => $isCustomerNotified,
                'shouldProtectState' => $shouldProtectState 
        ) );
        return $this;
    }
}
