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
class Apptha_Marketplace_Model_Sales_Invoice extends Mage_Sales_Model_Service_Order {
    /**
     * Prepare order invoice based on order data and requested items qtys.
     * If $qtys is not empty - the function will
     * prepare only specified items, otherwise all containing in the order.
     *
     * @param array $qtys            
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function prepareInvoice($qtys = array()) {
        /**
         * Checking marketplace and manage order for seller status are enabled or not
         */
        $marketplaceStatusFlag = Mage::getStoreConfig ( 'marketplace/marketplace/activate' );
        $orderStatusFlag = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/order_manage' );
        $saveInvoiceActionOrNot = 0;
        if (Mage::app ()->getRequest ()->getControllerName () == 'sales_order_invoice' && Mage::app ()->getRequest ()->getActionName () == 'save') {
            $saveInvoiceActionOrNot = 1;
        }
        
        /**
         * Checking whether marketplace enable, seller order management enable and save invoice action
         */
        if ($marketplaceStatusFlag == 1 && $orderStatusFlag == 1 && count ( $qtys ) > 1 && $saveInvoiceActionOrNot == 1) {
            $this->customInvoicePrepareForSeller ( $qtys, $this->_order->getId () );
        }
        
        /**
         * prepare invoice for marketplace
         */
        return $this->prepareInvoiceForMarketplace ( $qtys );
    }
    /**
     * Prepare invoice for marketplace
     *
     * @param array $qtys            
     * @return array
     */
    public function prepareInvoiceForMarketplace($qtys = array()) {
        
        /**
         * Checking marketplace and manage order for seller status are enabled or not
         */
        $marketplaceStatusFlag = Mage::getStoreConfig ( 'marketplace/marketplace/activate' );
        $orderStatusFlag = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/order_manage' );
        $saveInvoiceActionOrNot = 0;
        if (Mage::app ()->getRequest ()->getControllerName () == 'sales_order_invoice' && Mage::app ()->getRequest ()->getActionName () == 'save') {
            $saveInvoiceActionOrNot = 1;
        }
        
        /**
         * Update locale numbers
         */
        $this->updateLocaleNumbers ( $qtys );
        $invoice = $this->_convertor->toInvoice ( $this->_order );
        $totalQty = 0;
        /**
         * Preparing order item for invoice
         */
        foreach ( $this->_order->getAllItems () as $orderItem ) {
            
            if (! $this->_canInvoiceItem ( $orderItem, array () )) {
                continue;
            }
            /**
             * Getting item to invoice
             */
            $item = $this->_convertor->itemToInvoiceItem ( $orderItem );
            if ($orderItem->isDummy ()) {
                $qty = $orderItem->getQtyOrdered () ? $orderItem->getQtyOrdered () : 1;
            } else if (! empty ( $qtys )) {
                if (isset ( $qtys [$orderItem->getId ()] )) {
                    $qty = ( float ) $qtys [$orderItem->getId ()];
                } else {
                    /**
                     * checking for marketplace status flag
                     */
                    if ($marketplaceStatusFlag == 1 && $orderStatusFlag == 1 && $saveInvoiceActionOrNot == 1) {
                        $qty = 0;
                    }
                }
            } else {
                $qty = $orderItem->getQtyToInvoice ();
            }
            $totalQty += $qty;
            $item->setQty ( $qty );
            /**
             * Adding item for invoice
             */
            $invoice->addItem ( $item );
        }
        $invoice->setTotalQty ( $totalQty );
        $invoice->collectTotals ();
        /**
         * Add invoice items
         */
        $this->_order->getInvoiceCollection ()->addItem ( $invoice );
        return $invoice;
    }
    
    /**
     * Create invoice based on seller product for order
     *
     * @param
     *            array @qtys
     * @param number $orderId            
     *
     * @return array $qtys
     */
    public function customInvoicePrepareForSeller($qtys, $orderId) {
        /**
         * Initilize seller item and invlicee items for seller
         */
        $sellerItems = array ();
        $qtysCount = $qtys;
        /**
         * Checking qty and order id for create invoice
         */
        if ($qtysCount > 1 && ! empty ( $orderId )) {
            $orderDetails = Mage::getModel ( 'sales/order' )->load ( $orderId );
            /**
             * Prepare invoice data
             */
            foreach ( $orderDetails->getAllItems () as $item ) {
                $itemId = $item->getItemId ();
                if (array_key_exists ( $itemId, $qtys )) {
                    $itemProductId = $item->getProductId ();
                    /**
                     * Get seller items
                     */
                    $sellerItems = $this->getSellerItems ( $sellerItems, $itemProductId, $qtys, $itemId );
                }
            }
            $sellerItemsCount = count ( $sellerItems );
            /**
             * To create custom invoice
             */
            foreach ( $sellerItems as $sellerItem ) {
                $marketplaceInvoice = Mage::getModel ( 'sales/service_order', $orderDetails )->prepareInvoiceForMarketplace ( $sellerItem );
                /**
                 * Save marketplace invoice action
                 */
                $marketplaceInvoice->register ();
                $marketplaceInvoice->setEmailSent ( true );
                $marketplaceInvoice->getOrder ()->setCustomerNoteNotify ( true );
                Mage::getModel ( 'core/resource_transaction' )->addObject ( $marketplaceInvoice )->addObject ( $marketplaceInvoice->getOrder () )->save ();
                $sellerItemsCount = $sellerItemsCount - 1;
            }
        } else {
            /**
             * Return qtys
             */
            return $qtys;
        }
    }
    
    /**
     * Prepare seller invoice items
     *
     * @param array $sellerItems            
     * @param number $itemProductId            
     * @param array $qtys            
     * @param number $itemId            
     * @return array $sellerItems
     */
    public function getSellerItems($sellerItems, $itemProductId, $qtys, $itemId) {
        if (! empty ( $qtys [$itemId] )) {
            $sellerId = Mage::getModel ( 'catalog/product' )->load ( $itemProductId )->getSellerId ();
            /**
             * Assign items qtys
             */
            if (! empty ( $sellerId )) {
                $sellerItems [$sellerId] [$itemId] = $qtys [$itemId];
            } else {
                $sellerItems [0] [$itemId] = $qtys [$itemId];
            }
        }
        /**
         * Return seller items for invoice
         */
        return $sellerItems;
    }
} 