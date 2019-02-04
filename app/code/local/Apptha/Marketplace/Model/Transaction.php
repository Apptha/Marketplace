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
 * This class initiates the seller tranasction model
 *
 */
class Apptha_Marketplace_Model_Transaction extends Mage_Core_Model_Abstract {
    public function _construct() {
        parent::_construct ();
        $this->_init ( 'marketplace/transaction' );
    }
    /**
     * Function to change the commission status
     *
     * Passed the commission id of the seller
     * 
     * @param int $commissionId            
     *
     * @return void
     */
    public function changeStatus($commissionId) {
        if ($commissionId != '') {
            $now = Mage::getModel ( 'core/date' )->date ( 'Y-m-d H:i:s', time () );
            $collection = Mage::getModel ( 'marketplace/transaction' )->load ( $commissionId, 'commission_id' )->setReceivedStatus ( '1' )->setAcknowledgeDate ( $now );
            $collection->save ();
            return true;
        }
    }
    /**
     * Function to get the payment status from seller
     *
     * Passed the commission id of the seller
     * 
     * @param int $id            
     *
     * @return void
     */
    public function getPaymentstatus($id) {
        return Mage::getModel ( 'marketplace/transaction' )->load ( $id, 'commission_id' );
    }
    /**
     * Function to get the payment comment from admin
     *
     * Passed the commission id of the seller
     * 
     * @param int $id
     *            Return the transaction info of a seller
     * @return array
     */
    public function getPaymentcomment($_id) {
        return Mage::getModel ( 'marketplace/transaction' )->load ( $_id, 'seller_id' );
    }
    public function getPaymentcommentOfSeller($_id) {
        $collection = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToFilter ( 'seller_id', array (
                'eq' => $_id 
        ) )->setOrder ( 'paid_date', 'DESC' );
        
        foreach ( $collection as $_paymentStatus ) {
            if (isset ( $_paymentStatus ['comment'] )) {
                return $_paymentStatus ['comment'];
            }
        }
    }
} 