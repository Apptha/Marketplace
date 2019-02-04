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
 * Form to get the comments from admin after transfer the payment to seller
 */
class Apptha_Marketplace_Block_Adminhtml_Commission_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * Get the comments from admin after transfer the payment to seller
     *
     * @return void
     */
    protected function _prepareForm() {
    /**
     * Get Seller Id
     * @var Ambiguous $sellerId
     */ 
        $sellerId = $this->getRequest ()->getParam ( 'id' );
        /**
         *Load seller object
         */ 
        $collection = Mage::getModel('marketplace/transaction')->load($sellerId,'seller_id');
        $comment = Mage::getModel ( 'marketplace/transaction' )->getPaymentcommentOfSeller ( $sellerId );
        /**
         * Set Collection(transaction)
         */
        $this->setCollection ( $collection );
        $remainingAmount = $this->getAmountRemaining ( $sellerId );
        /**
         * Id:edit_form
         * Method:post
         *
         * @var Varien_Data_Form $form
         */ 
        $form = new Varien_Data_Form ( array (
                'id' => 'edit_form',
                'action' => $this->getUrl ( '*/*/pay', array ('id' => $this->getRequest ()->getParam ( 'id' ) ) ),
                'method' => 'post','enctype' => 'multipart/form-data') );
        $fieldset = $form->addFieldset ( 'add_comment', array ('legend' => Mage::helper ( 'marketplace' )->__ ( 'Payment Details' ) ) );
        /**
         * Field name:seller commission
         * name:seller_commission
         * required:true
         */
        $fieldset->addField ( 'seller_commission', 'text', array ('name' => 'seller_commission',
                'title' => Mage::helper ( 'marketplace' )->__ ( 'Amount to Send' ),
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Amount to Send' ),
                'required' => true,'class' => 'validate-number validate-number-range number-range-0-' . $remainingAmount . '',
                'value' => $remainingAmount ) );
        /**
         * Field name:detail
         * name:detail
         * required:true
         */
        $fieldset->addField ( 'detail', 'textarea', array (
                'name' => 'detail','title' => Mage::helper ( 'marketplace' )->__ ( 'Comments' ),
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Comments' ),
                'style' => 'height: 200px;','required' => true,
                'value' => $comment ) );
        $form->setUseContainer ( true );
        $this->setForm ( $form );
        return parent::_prepareForm ();
    }
    
    /**
     * Getting remaining amount
     *
     * @param int $sellerId            
     * @return double $return
     */
    public function getAmountRemaining($sellerId) {
        $return = "";
        /**
         * Getting seller transacation details
         */
        $getTransactionCollection = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToSelect ( 'seller_commission' )->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'paid', 0 );
        $getTransactionCollection->getSelect ()->columns ( 'SUM(seller_commission) AS seller_commission' )->group ( 'seller_id' );
       /**
        * Increment foreach loop
        */
        foreach ( $getTransactionCollection as $keyAmount ) {
            $return = $keyAmount->getSellerCommission ();
        }
        /**
         * Getting padi upto to data
         */
        $getCollectionPaidUpto = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToSelect ( 'paid_upto' )->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'paid', 0 );
        $getCollectionPaidUpto->getSelect ()->columns ( 'SUM(paid_upto) AS paid_upto' )->group ( 'seller_id' );
       /**
        * Increment foreach loop
        */
        foreach ( $getCollectionPaidUpto as $amounts ) {
            $setPaidUpto = $amounts->getPaidUpto ();
        }
        return $return - $setPaidUpto;
    }
}