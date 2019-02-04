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
 * This file is used to rewrite can view order function
 */
require_once Mage::getModuleDir ( 'controllers', 'Mage_Sales' ) . DS . 'OrderController.php';
class Apptha_Marketplace_Sales_OrderController extends Mage_Sales_OrderController {
    
    /**
     * Check order view availability
     *
     * @param Mage_Sales_Model_Order $order            
     * @return bool
     */
    protected function _canViewOrder($order) {
        /**
         * Getting action name
         */
        $actionName = $this->getRequest ()->getActionName ();
        /**
         * Getting route name
         */
        $routeName = $this->getRequest ()->getRouteName ();
        /**
         * Getting module name
         */
        $moduleName = $this->getRequest ()->getModuleName ();
        /**
         * Checking handler details
         */
        if ($actionName == 'printInvoice' && $routeName == 'sales' && $moduleName == 'sales') {
            /**
             * Getting invoice id and customer id
             */
            $invoiceId = $this->getRequest ()->getParam ( 'invoice_id' );
            $customerId = Mage::getSingleton ( 'customer/session' )->getCustomerId ();
            /**
             * Load invoice data by invoice id
             */
            $invoice = Mage::getModel ( 'sales/order_invoice' )->load($invoiceId);
            /**
             * Get order details by invoice
             */
            $order = $invoice->getOrder ();
            $oderId = $order->getId ();
            /**
             * Getting order product ids
             */
            $orderPrdouctIds = Mage::helper('marketplace/vieworder')->getOrderProductIds($customerId,$oderId );
            /**
             * Checking for invoice item by seller product
             */
            foreach($invoice->getAllItems () as $item){
            /**
             * Get product id
             * @var int
             */
                $itemProductId = $item->getProductId();
                /**
                 * Check whether product id is in array
                 */
                if (in_array($itemProductId,$orderPrdouctIds)){
                    return true;
                }
            }
        }
        /**
         * Checking handler details
         */
        if ($actionName == 'printShipment' && $routeName == 'sales' && $moduleName == 'sales') {
            /**
             * Getting shipment id and customer id
             */
            $shipmentId = $this->getRequest ()->getParam ( 'shipment_id' );
            $customerId = Mage::getSingleton ( 'customer/session' )->getCustomerId ();
            /**
             * Load shipment details by shipping id
             */
            $shipment = Mage::getModel ( 'sales/order_shipment' )->load ( $shipmentId );
            /**
             * get order
             * @var order id
             */
            $order = $shipment->getOrder ();
            $oderId = $order->getId ();
            /**
             * Getting order product ids
             */
            $orderPrdouctIds = Mage::helper ( 'marketplace/vieworder' )->getOrderProductIds ( $customerId, $oderId );
            /**
             * Increment foreach loop
             */
            foreach ( $shipment->getAllItems () as $item ) {
            /**
             * get product id
             * @var unknown
             */
                $itemProductId = $item->getProductId ();
                /**
                 * check product is in array
                 */
                if (in_array ( $itemProductId, $orderPrdouctIds )) {
                    return true;
                }
            }
        }
        /**
         * Getting customer id
         */
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomerId ();
        /**
         * Inilize avaiable states
         */
        $availableStates = Mage::getSingleton ( 'sales/order_config' )->getVisibleOnFrontStates ();
        /**
         * Checking for order and customer and state
         */
        $returnFlag = false;
        if ($order->getId () && $order->getCustomerId () && ($order->getCustomerId () == $customerId) && in_array ( $order->getState (), $availableStates, true )) {
            $returnFlag = true;
        }
        return $returnFlag;
    }
} 