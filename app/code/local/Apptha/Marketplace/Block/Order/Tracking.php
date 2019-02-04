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
 * This class contains order tracking functionality
 * 
 * */
class Apptha_Marketplace_Block_Order_Tracking extends Mage_Adminhtml_Block_Template {
    /**
     * Prepares layout of block
     *
     * @return Mage_Adminhtml_Block_Sales_Order_View_Giftmessage
     */
    protected function _prepareLayout() {
        /**
         * Add button for tracking no
         */
        $this->setChild ( 'add_button', $this->getLayout ()->createBlock ( 'adminhtml/widget_button' )->setData ( array (
                'label' => Mage::helper ( 'sales' )->__ ( 'Add Tracking Number' ),
                'class' => 'button',
                'onclick' => 'trackingControl.add()' 
        ) ) )

        ;
        /**
         * Add submit button
         */
        $this->setChild ( 'submit_button', $this->getLayout ()->createBlock ( 'adminhtml/widget_button' )->setData ( array (
                'label' => Mage::helper ( 'sales' )->__ ( 'Submit' ),
                'class' => 'button',
                'type' => 'submit' 
        ) ) )

        ;
    }
    
    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    public function getShipment() {
        /**
         * Prepare shipment
         */
        $orderId = $this->getRequest ()->getParam ( 'orderid' );
        /**
         * Load order
         */
        $order = Mage::getModel ( 'sales/order' )->load ( $orderId );
        /**
         * Prepare shipment
         */
        return Mage::getModel ( 'sales/service_order', $order )->prepareShipment ( array () );
    }
    
    /**
     * Retrieve
     *
     * @return unknown
     */
    public function getCarriers() {
        /**
         * Initilize carriers
         */
        $carriers = array ();
        $carrierInstances = Mage::getSingleton ( 'shipping/config' )->getAllCarriers ( $this->getShipment ()->getStoreId () );
        /**
         * Set carriers value
         */
        $carriers ['custom'] = Mage::helper ( 'sales' )->__ ( 'Custom Value' );
        /**
         * Load carrier instance
         */
        foreach ( $carrierInstances as $code => $carrier ) {
            if ($carrier->isTrackingAvailable ()) {
                $carriers [$code] = $carrier->getConfigData ( 'title' );
            }
        }
        return $carriers;
    }
}
