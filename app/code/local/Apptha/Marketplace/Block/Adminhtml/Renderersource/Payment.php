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
 */

/**
 * Renderer to change the payment status
 */
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Payment extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
    /**
     * Function to pay amount from admin to seller and changed the payment status
     *
     * Return the payment status as 'Paid' or 'Pay' or 'NIL'
     * 
     * @return string
     */
    public function render(Varien_Object $row) {
        $id = $row->getData ();
        foreach ( $id as $_id ) {
            $collection = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToSelect ( 'seller_commission' )->addFieldToFilter ( 'seller_id', $_id )->addFieldToFilter ( 'paid', 0 );
            $collection->getSelect ()->columns ( 'SUM(seller_commission) AS seller_commission' )->group ( 'seller_id' );
            foreach ( $collection as $amount ) {
                $total = $amount->getSellerCommission ();
            }
            $collectionPaid = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToSelect ( 'seller_commission' )->addFieldToFilter ( 'seller_id', $id )->addFieldToFilter ( 'paid', 1 );
            $collectionPaid->getSelect ()->columns ( 'SUM(seller_commission) AS seller_commission' )->group ( 'seller_id' );
            foreach ( $collectionPaid as $amountPaid ) {
                $totalPaid = $amountPaid->getSellerCommission ();
            }
            if (empty ( $total ) && ! empty ( $totalPaid )) {
                $result = Mage::helper ( 'marketplace' )->__ ( 'Paid' );
            } elseif (empty ( $total ) && empty ( $totalPaid )) {
                $result = Mage::helper ( 'marketplace' )->__ ( 'NIL' );
            } else {
                $result = "<a href='" . $this->getUrl ( '*/*/addcomment/', array (
                        'id' => $_id 
                ) ) . "' title='" . Mage::helper ( 'marketplace' )->__ ( 'click to Pay' ) . "'>" . Mage::helper ( 'marketplace' )->__ ( 'Pay' ) . "</a>";
            }
            return $result;
        }
    }
}

