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
 * Render the Amount remaining from admin to seller
 */
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Amountremaining extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    
    /**
     * Function to get data of remaining amount from admin
     *
     * Return the remaining amount
     * 
     * @return float
     */
    public function render(Varien_Object $row) {
        $return = $paidUpto = '';
        $value = $row->getData ( $this->getColumn ()->getIndex () );
        $collection = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToSelect ( 'seller_commission' )->addFieldToFilter ( 'seller_id', $value )->addFieldToFilter ( 'paid', 0 );
        $collection->getSelect ()->columns ( 'SUM(seller_commission) AS seller_commission' )->group ( 'seller_id' );
        foreach ( $collection as $amount ) {
            $return = $amount->getSellerCommission ();
        }
        
        $collectionPaidUpto = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToSelect ( 'paid_upto' )->addFieldToFilter ( 'seller_id', $value )->addFieldToFilter ( 'paid', 0 );
        $collectionPaidUpto->getSelect ()->columns ( 'SUM(paid_upto) AS paid_upto' )->group ( 'seller_id' );
        foreach ( $collectionPaidUpto as $amount ) {
            $paidUpto = $amount->getPaidUpto ();
        }
        $return = $return - $paidUpto;
        return Mage::helper ( 'core' )->currency ( $return, true, false );
    }
}

