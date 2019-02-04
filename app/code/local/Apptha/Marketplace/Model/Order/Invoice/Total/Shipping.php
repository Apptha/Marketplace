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
 * This class contains funcationality for shipping amount split based on seller.
 * 
 **/
class Apptha_Marketplace_Model_Order_Invoice_Total_Shipping extends Mage_Sales_Model_Order_Invoice_Total_Abstract {
    /**
     * Function to split shipping amount
     * {@inheritDoc}
     * @see Mage_Sales_Model_Order_Invoice_Total_Abstract::collect()
     */
public function collect(Mage_Sales_Model_Order_Invoice $invoice) {
/**
 * Set Shipping Amount
 */
        $invoice->setShippingAmount ( 0 );
        $invoice->setBaseShippingAmount ( 0 );
        /**
         * Collect invoice details.
         */
        $orderShippingAmount = $invoice->getOrder ()->getShippingAmount ();
        $baseOrderShippingAmount = $invoice->getOrder ()->getBaseShippingAmount ();
        $shippingInclTax = $invoice->getOrder ()->getShippingInclTax ();
        $baseShippingInclTax = $invoice->getOrder ()->getBaseShippingInclTax ();
        $entityId = $invoice->getOrder ()->getEntityId ();
        /**
         * Get customer id
         * @var id
         */
        $sellerId = Mage::getSingleton ( 'customer/session' )->getId ();
        $shippingMethod = $invoice->getOrder ()->getShippingMethod ();
        /**
         * Get order product id.
         */
        $orderPrdouctIds = Mage::helper ( 'marketplace/vieworder' )->getOrderProductIds ( $sellerId, $entityId );
        /**
         * Get order item product id.
         */
        $cancelOrderItemProductIds = Mage::helper ( 'marketplace/vieworder' )->cancelOrderItemProductIds ( $sellerId, $entityId );
        $orderStatusFlag = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/order_manage' );
        /**
         * Condition to check whether the shipping method is carrier title(Split shipping) shipping method.
         */
        if (count ( $orderPrdouctIds ) >= 1 && $orderStatusFlag == 1 && $shippingMethod == "apptha_apptha" && $sellerId) {
            $order = Mage::getModel ( 'sales/order' )->load ( $entityId );
           $shippingCountryId = $order->getShippingAddress ()->getCountryId ();
            $itemsarray = $itemsArr = array ();
            /**
             * prepare invoice items
             */
            foreach ( $order->getAllItems () as $item ) {
                $qty = 0;
                /**
                 * Prepare invoice qtys
                 */
                $itemProductId = $item->getProductId ();
                /**
                 * Check whether item is in array or not
                 */
                if (in_array ( $itemProductId, $orderPrdouctIds ) && ! in_array ( $itemProductId, $cancelOrderItemProductIds )) {
                    $itemsArr [] = $itemProductId;
                    /**
                     * Qty ordered for that item
                     */
                    $qty = $item->getQtyOrdered () - $item->getQtyInvoiced ();
                }
                $itemsarray [$itemProductId] = $qty;
            }
            $productIdQuantity = array_filter ( $itemsarray );
            /**
             * Initialize ordershippingAmount to zero since the whole shipping amount is assigned to this variable.
             */
            $orderShippingAmount = 0;
            /**
             * Increment foreach loop
             */
            foreach ( $productIdQuantity as $productId => $qty ) {
                $nationalShippingPrice = 0;
                $internationalShippingPrice = 0;
                /**
                 * load product object
                 * @var unknown
                 */
                $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
                /**
                 * Condition to check seller based shipping
                 */
                if (! Mage::getStoreConfigFlag ( 'marketplace/shipping/shippingcost' )) {
                /**
                 * Get Seller Id
                 * @var seller id
                 */
                    $sellerId = $product->getSellerId ();
                    /**
                     * Get Seller profile collection
                     * @var unknown
                     */
                    $collection = Mage::getModel ( 'marketplace/sellerprofile' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId );
                    /**
                     * Increment foreach loop
                     */
                    foreach ( $collection as $data ) {
                        $nationalShippingPrice = $data->getData ( 'national_shipping_cost' );
                        $internationalShippingPrice = $data->getData ( 'international_shipping_cost' );
                    }
                /**
                 * Product based shipping
                 */
                } else {
                    $nationalShippingPrice = $product->getNationalShippingPrice ();
                    $internationalShippingPrice = $product->getInternationalShippingPrice ();
                }
                $product->getSellerShippingOption ();
                $sellerDefaultCountry = $product->getDefaultCountry ();
                /**
                 * Condition to check shipping method is national or international based on the country id
                 */
                if ($nationalShippingPrice != '' && $internationalShippingPrice != '' && $shippingCountryId != '' && $sellerDefaultCountry == $shippingCountryId) {
                    $orderShippingAmount += ($nationalShippingPrice * $qty);
                } else {
                    $orderShippingAmount += ($internationalShippingPrice * $qty);
                }
            }
        }
        if ($orderShippingAmount) {
            /**
             * Set shipping amount
             */
            $invoice->setShippingAmount ( $orderShippingAmount );
            /**
             * Set base shipping amount
             */
            $invoice->setBaseShippingAmount ( $baseOrderShippingAmount );
            /**
             * Set Shipping including tax
             */
            $invoice->setShippingInclTax ( $shippingInclTax );
            /**
             * Set base shipping including tax
             */
            $invoice->setBaseShippingInclTax ( $baseShippingInclTax );
            /**
             * Add shipping amount with the grand total to show in invoice.
             */
            $invoice->setGrandTotal ( $invoice->getGrandTotal () + $orderShippingAmount );
            $invoice->setBaseGrandTotal ( $invoice->getBaseGrandTotal () + $baseOrderShippingAmount );
        }
        return $this;
    }
}
