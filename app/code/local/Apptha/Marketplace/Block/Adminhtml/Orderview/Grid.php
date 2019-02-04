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
 * View order information
 */
class Apptha_Marketplace_Block_Adminhtml_Orderview_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of data to view order information
     *
     * @return array
     */
    public function __construct() {
        parent::__construct ();
        /**
         * Set Id
         */
        $this->setId ( 'orderviewGrid' );
        /**
         * Set Entity Id
         */
        $this->setDefaultSort ( 'entity_id' );
        /**
         * Set Default desc
         */
        $this->setDefaultDir ( 'DESC' );
        $this->setSaveParametersInSession ( true );
    }
    
    /**
     * Function to get order collection
     *
     * Return the seller product's order information
     * return array
     */
    protected function _prepareCollection() {
        /**
         * Commission Get Collection
         */
        $orders = Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToSelect ( '*' )->addFieldToFilter ( 'order_status', array (
                'eq' => 'complete' 
        ) )->addFieldToFilter ( 'status', array (
                'eq' => 1 
        ) )->setOrder ( 'order_id', 'desc' );
        /**
         * Set Collection
         */
        $this->setCollection ( $orders );
        return parent::_prepareCollection ();
    }
    
    /**
     * Function to create custom column
     *
     * @param string $id            
     * @return string colunm value
     */
    public function createCustomColumn($id, $store) {
        switch ($id) {
            /**
             * Get seller details.
             */
            case 'Seller detail' :
                $value = $this->getSellerDetail ();
                break;
            /**
             * Get product details.
             */
            case 'Product details' :
                $value = $this->getProductDetail ();
                break;
            /**
             * Get product price
             */
            case 'Product Price' :
                $value = $this->getProductPrice ( $store );
                break;
            /**
             * Delivery cost
             */
            case 'Delivery Cost' :
                $value = $this->getDeliveryCost ( $store );
                break;
            default :
                $value = '';
        }
        return $value;
    }
    
    /**
     * Function to display fields with data
     *
     * Display information about orders
     *
     * @return void
     */
    protected function _prepareColumns() {
        /**
         * Get Store
         */
        $store = Mage::app ()->getStore ();
        $this->createCustomColumn ( 'Seller detail', $store );
        /**
         * Increment id(Order id).
         */
        $incrementId = array (
                'header' => Mage::helper ( 'sales' )->__ ( 'Order #' ),
                'width' => '100px',
                'index' => 'increment_id' 
        );
        $this->addColumn ( 'increment_id', $incrementId );
        /**
         * Create Custom Column
         */
        $this->createCustomColumn ( 'Product details', $store );
        /**
         * Create Product Price
         */
        $this->createCustomColumn ( 'Product Price', $store );
        /**
         * Create delivery cost column if delivery schedule enabled
         */
        $deliveryScheduleEnable = Mage::getStoreConfig ( 'deliveryschedule/general/delivery_schedule_enabled' );
        if ($deliveryScheduleEnable) {
            $this->createCustomColumn ( 'Delivery Cost', $store );
        }
        $this->getFields ( $store );
        
        /**
         * View Action
         */
        $actions = array (
                'caption' => Mage::helper ( 'marketplace' )->__ ( 'View' ),
                'url' => array (
                        'base' => 'adminhtml/sales_order/view/' 
                ),
                'field' => 'order_id' 
        );
        /**
         * View
         */
        $this->addColumn ( 'view', array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'View' ),
                'type' => 'action',
                'getter' => 'getOrderId',
                'actions' => array (
                        $actions 
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true 
        ) );
        return parent::_prepareColumns ();
    }
    
    /**
     * Function for Mass action(credit payment to seller)
     *
     * Will change the credit order status of the seller
     * return void
     */
    protected function _prepareMassaction() {
        /**
         * set mass action id
         */
        $this->setMassactionIdField ( 'id' );
        $formFieldName = 'marketplace';
        /**
         * get Mass action block
         */
        $this->getMassactionBlock ()->setFormFieldName ( $formFieldName );
        $lable = Mage::helper ( 'marketplace' )->__ ( 'Credit' );
        $url = $this->getUrl ( '*/*/masscredit' );
        $this->getMassactionBlock ()->addItem ( 'credit', array (
                'label' => $lable,
                'url' => $url 
        ) );
        return $this;
    }
    
    /**
     * Function for link url
     *
     * Not redirected to any page
     * return void
     */
    public function getRowUrl($row) {
        $rowFlag = '';
        if (! empty ( $row )) {
            $rowFlag = false;
        }
        return $rowFlag;
    }
    /**
     * Function for adding seller detail column
     *
     * Not redirected to any page
     * return void
     */
    public function getSellerDetail() {
        $sellerEmail = array (
                'header' => Mage::helper ( 'sales' )->__ ( 'Seller detail' ),
                'width' => '150px',
                'index' => 'seller_id',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordersellerdetails' 
        );
        return $this->addColumn ( 'selleremail', $sellerEmail );
    }
    /**
     * prepare the column for delivery cost
     * 
     * @param
     *            $store
     * @return Ambigous <Mage_Adminhtml_Block_Widget_Grid, Apptha_Marketplace_Block_Adminhtml_Orderview_Grid>
     */
    public function getDeliveryCost($store) {
        $deliveryCost = array (
                'header' => Mage::helper ( 'deliveryschedule' )->__ ( 'Delivery Cost ' ),
                'width' => '150px',
                'index' => 'increment_id',
                'filter' => false,
                'sortable' => false,
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency ()->getCode (),
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Orderdeliverycost' 
        );
        /**
         * Add column for delivery cost
         */
        return $this->addColumn ( 'deliverycost', $deliveryCost );
    }
    /**
     * Function for adding product detail column
     *
     *
     * Not redirected to any page
     * return void
     */
    public function getProductDetail() {
        /**
         * $productDetails array hold an product details data
         */
        $productDetails = array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Product details' ),
                'width' => '150px',
                'index' => 'id',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_OrderProductdetails' 
        );
        /**
         * $productDetails column
         */
        return $this->addColumn ( 'productdetail', $productDetails );
    }
    /**
     * Function for getting product price
     *
     *
     * Not redirected to any page
     * return void
     */
    public function getProductPrice($store) {
        $productAmt = array (
                'header' => Mage::helper ( 'sales' )->__ ( 'Product Price' ),
                'align' => 'right',
                'index' => 'product_amt',
                'width' => '80px',
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency ()->getCode (),
                'currency' => 'order_currency_code' 
        );
        /**
         * Return column for product amount
         */
        return $this->addColumn ( 'product_amt', $productAmt );
    }
    public function getFields($store) {
        /**
         * $sellerAmount array hold a seller amount data
         */
        $sellerAmount = array (
                'header' => Mage::helper ( 'sales' )->__ ( 'Seller\'s Earned Amount' ),
                'align' => 'right',
                'index' => 'seller_amount',
                'width' => '80px',
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency ()->getCode (),
                'currency' => 'order_currency_code' 
        );
        /**
         * Add the column for seller amount
         */
        $this->addColumn ( 'seller_amount', $sellerAmount );
        /**
         * $commissionFee hold a Commission fee data
         */
        $commissionFee = array (
                'header' => Mage::helper ( 'sales' )->__ ( 'Commission Fee' ),
                'align' => 'right',
                'index' => 'commission_fee',
                'width' => '80px',
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency ()->getCode (),
                'currency' => 'order_currency_code' 
        );
        /**
         * Add the column for commission fee
         */
        $this->addColumn ( 'commission_fee', $commissionFee );
        /**
         * $orderCreatedAt hold an Order at data
         */
        $orderCreatedAt = array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Order At' ),
                'align' => 'center',
                'width' => '200px',
                'index' => 'order_id',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Orderdate' 
        );
        /**
         * Add the column which is for Order At
         */
        $this->addColumn ( 'order_created_at', $orderCreatedAt );
        /**
         * $action Store the Credit Action data
         */
        $action = array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Actions' ),
                'align' => 'center',
                'width' => '100px',
                'index' => 'id',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordercredit' 
        );
        /**
         * Add the column for action
         */
        $this->addColumn ( 'action', $action );
        /**
         * $paymentStatus hold the Payment status data
         */
        $paymentStatus = array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Ack Status' ),
                'align' => 'center',
                'width' => '100px',
                'index' => 'payment_status',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Receivedstatus' 
        );
        /**
         * Add column for payment status
         */
        $this->addColumn ( 'payment_status', $paymentStatus );
        /**
         * Create the column for Acknowledge Date
         * $acknowledgeDate hold on acknowledge column data
         */
        $acknowledgeDate = array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Ack On' ),
                'align' => 'center',
                'width' => '100px',
                'index' => 'acknowledge_date',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Acknowledgedate' 
        );
        $this->addColumn ( 'acknowledge_date', $acknowledgeDate );
    }
}

