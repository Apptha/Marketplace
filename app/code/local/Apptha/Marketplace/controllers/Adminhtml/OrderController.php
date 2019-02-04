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
 * Manage orders in admin section
 * This class has been used to manage the seller orders info like credit, mass credit in admin section
 */
class Apptha_Marketplace_Adminhtml_OrderController extends Mage_Adminhtml_Controller_Action {
    protected function _initAction() {
        $this->loadLayout ()->_setActiveMenu ( 'marketplace/items' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Items Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ) );
        return $this;
    }
    /**
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {
        /**
         * Retrieve the seller id from the posted information
         */
        $sellerId = Mage::app ()->getRequest ()->getParam ( 'id' );
        /**
         * Check the seller id is empty
         * if so redirect to order view section
         */
        if (empty ( $sellerId )) {
            $this->_redirect ( 'marketplaceadmin/adminhtml_orderview' );
            return;
        }
        $this->_initAction ()->renderLayout ();
    }
    /**
     * Credit amount to seller account
     *
     * @return void
     */
    public function creditAction() {
        /**
         * Get the passed sellerd
         * and check its greater than zero
         * if so assign the information like
         * seller id
         * admin commission
         * order id
         */
        $id = $this->getRequest ()->getParam ( 'id' );
        if ($id > 0) {
            try {
                $model = Mage::getModel ( 'marketplace/commission' )->load ( $id );
                $model->setCredited ( '1' )->save ();
                $sellerId = $model->getSellerId ();
                $adminCommission = $model->getCommissionFee ();
                $sellerCommission = $model->getSellerAmount ();
                $orderId = $model->getOrderId ();
                /**
                 * transaction collection
                 */
                $transaction = Mage::getModel ( 'marketplace/transaction' )->load ( $id, 'commission_id' );
                $transactionId = $transaction->getId ();
                /**
                 * check the transaction is empty
                 * if so assign the information like
                 * commission id
                 * seller id
                 * admin commission
                 * order id
                 * reveived status
                 */
                if (empty ( $transactionId )) {
                    $data = array (
                            'commission_id' => $id,
                            'seller_id' => $sellerId,
                            'seller_commission' => $sellerCommission,
                            'admin_commission' => $adminCommission,
                            'order_id' => $orderId,
                            'received_status' => 0 
                    );
                    Mage::getModel ( 'marketplace/transaction' )->setData ( $data )->save ();
                }
                /**
                 * Display success message on successfull amount credit
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Amount was successfully credited' ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Error message on credit amount failure
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/' );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    /**
     * Credit amount to multiple seller account
     *
     * @return void
     */
    public function masscreditAction() {
        /**
         * Get the posted marketplace information
         * and assign the values like
         * seller id, admin commission, order id
         */
        $marketplace = $this->getRequest ()->getPost ( 'marketplace' );
        foreach ( $marketplace as $value ) {
            $model = Mage::helper ( 'marketplace/common' )->updateCredit ( $value );
            $sellerId = $model->getSellerId ();
            $adminCommission = $model->getCommissionFee ();
            $sellerCommission = $model->getSellerAmount ();
            $orderId = $model->getOrderId ();
            /**
             * transaction collection
             */
            $transaction = Mage::helper ( 'marketplace/transaction' )->getTransactionInfo ( $value );
            $transactionId = $transaction->getId ();
            /**
             * Check the id of transction is empty
             * if then assign the values like
             * commision id, seller id, seller commision, admin commission, order id
             */
            if (empty ( $transactionId )) {
                $data = array (
                        'commission_id' => $value,
                        'seller_id' => $sellerId,
                        'seller_commission' => $sellerCommission,
                        'admin_commission' => $adminCommission,
                        'order_id' => $orderId 
                );
                /**
                 * Save transaction information
                 */
                Mage::helper ( 'marketplace/transaction' )->saveTransactionData ( $data );
            }
        }
        /**
         * Display success message after the mass credit successfully done to the sellers
         */
        Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Amount was successfully credited' ) );
        $this->_redirect ( '*/*/' );
    }
    /**
     * Resolved acl issue while adding secure batch or magento 1.9.2
     */
    protected function _isAllowed() {
        return true;
    }
} 