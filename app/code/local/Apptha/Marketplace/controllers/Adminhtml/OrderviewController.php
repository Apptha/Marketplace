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
 * Order view management
 * This class has been used to manange the order view in admin section like
 * crdit, mass crdit, transaction actions
 */
class Apptha_Marketplace_Adminhtml_OrderviewController extends Mage_Adminhtml_Controller_Action {
    /**
     * Load Layout
     *
     * @return void
     */
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
         * To Render Layout
         */
        $this->_initAction ()->renderLayout ();
    }
    /**
     * Credit amount to seller account
     *
     * @return void
     */
    public function creditAction() {
        /**
         * Get Id
         */
        $id = $this->getRequest ()->getParam ( 'id' );
        /**
         * Check the posted id is greater than zero
         * if so get the information like
         * seller id
         * admin commission
         * seller commission
         * order id
         */
        if ($id > 0) {
            try {
                /**
                 * Get Commission Details
                 */
                $model = Mage::getModel ( 'marketplace/commission' )->load ( $id );
                /**
                 * Save
                 */
                $model->setCredited ( '1' )->save ();
                /**
                 * Get Seller Id
                 */
                $seller_id = $model->getSellerId ();
                /**
                 * Get Commission Fee
                 */
                $admin_commission = $model->getCommissionFee ();
                /**
                 * Get Seller Amount
                 */
                $seller_commission = $model->getSellerAmount ();
                /**
                 * Get Order Id
                 */
                $order_id = $model->getOrderId ();
                /**
                 * transaction collection
                 */
                $transaction = Mage::getModel ( 'marketplace/transaction' )->load ( $id, 'commission_id' );
                /**
                 * Get Transaction Id
                 */
                $transaction_id = $transaction->getId ();
                /**
                 * Check the transaction id es empty
                 * if it is then assign values to an array like
                 * commission id, seller id, seller commission, admin commission, order id
                 */
                $now = Mage::getModel ( 'core/date' )->date ( 'Y-m-d H:i:s', time () );
                if (empty ( $transaction_id )) {
                    $data = array (
                            'commission_id' => $id,
                            'seller_id' => $seller_id,
                            'seller_commission' => $seller_commission,
                            'admin_commission' => $admin_commission,
                            'order_id' => $order_id,
                    'paid_date'=>$now 
                    );
                    /**
                     * Save Data
                     */
                    Mage::getModel ( 'marketplace/transaction' )->setData ( $data )->save ();
                }
                /**
                 * Success message upon credit success
                 */
                $successMsg = Mage::helper ( 'marketplace' )->__ ( 'Amount was successfully credited' );
                /**
                 * Add Sucess Message
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( $successMsg );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Error message on credit failure
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/' );
            }
        }
        /**
         * Redirect Url
         */
        $this->_redirect ( '*/*/' );
    }
    /**
     * Credit amount to multiple seller account
     *
     * @return void
     */
    public function masscreditAction() {
        /**
         * Get Values for mass action
         */
        $marketplaceVal = $this->getRequest ()->getPost ( 'marketplace' );
        foreach ( $marketplaceVal as $value ) {
            $model = Mage::helper ( 'marketplace/common' )->updateCredit ( $value );
            $seller_id = $model->getSellerId ();
            /**
             * get commission fee
             */
            $admin_commission = $model->getCommissionFee ();
            /**
             * get Seller Amount
             */
            $seller_commission = $model->getSellerAmount ();
            /**
             * Get Order Id
             */
            $order_id = $model->getOrderId ();
            /**
             * transaction collection
             */
            $transaction = Mage::helper ( 'marketplace/transaction' )->getTransactionInfo ( $value );
            /**
             * Get Transaction Id
             */
            $transaction_id = $transaction->getId ();
            if (empty ( $transaction_id )) {
                $data = array (
                        'commission_id' => $value,
                        'seller_id' => $seller_id,
                        'seller_commission' => $seller_commission,
                        'admin_commission' => $admin_commission,
                        'order_id' => $order_id 
                );
                /**
                 * Saving transaction for multiple seller
                 */
                Mage::helper ( 'marketplace/transaction' )->saveTransactionData ( $data );
            }
        }
        /**
         * Success messsage after multiple seller credit amount has been done successfully
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