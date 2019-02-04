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
 * @version     1.9.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
/**
 * Deals Grid Management
 * This class is used to Display Deals Grid in admin admin grid
 */
class Apptha_Superdeals_Block_Adminhtml_Report_Dealz_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of data to with sold deal products information
     *
     * @return array
     */
    public function __construct() {
        
        /**
         * Loading parent constructor
         */
        parent::__construct ();
        $this->setId ( 'gridDealzReport' );
        $this->setDefaultSort ( 'order_no' );
        $this->setDefaultDir ( 'DESC' );
    }
    
    /**
     * Get collection from Deal products
     *
     * Return array of data to with deal products information
     *
     * @return array
     */
    protected function _prepareCollection() {
        $dealzCollection = Mage::getModel ( 'superdeals/dealz' )->getCollection ();
        $this->setCollection ( $dealzCollection );
        return parent::_prepareCollection ();
    }
    
    /**
     * Mass action for delete the deals products
     * Will delete the selected deal products
     * 
     * @return void
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField ( 'serial_id' );
        $this->getMassactionBlock ()->setFormFieldName ( 'superdeals' );
        $this->getMassactionBlock ()->addItem ( 'delete', array (
                'label' => Mage::helper ( 'superdeals' )->__ ( 'Delete' ),
                'url' => $this->getUrl ( '*/*/massDelete' ),'confirm' => Mage::helper ( 'superdeals' )->__ ( 'Are you sure ?' )) );
        return $this;
    }
    /**
     * Display the Deals products in grid
     * Display information about deals products
     * 
     * @return void
     */
    protected function _prepareColumns() {
        $currencyCode = Mage::app ()->getStore ()->getBaseCurrencyCode ();
        /**
         * Order id
         */
        $this->addColumn ( 'order_no', array ('header' => $this->__ ( 'Order #' ),
                'sortable' => true,'default' => 'desc',
                'index' => 'order_no','width' => 1) );
        /**
         * Purchase date.
         */
        $this->addColumn ( 'purchase_date', array ('header' => $this->__ ( 'Purchased On' ),
                'sortable' => true,'index' => 'purchase_date',
                'width' => 0.5,'type' => 'datetime',
                'align' => 'left', 'default' => $this->__ ( 'N/A' ),
                'html_decorators' => array (  'nobr' 
                ) 
        ) );
        /**
         * Display Customer name.
         */
        $this->addColumn ( 'customer_id', array (
                'header' => $this->__ ( 'Customer Name' ),
                'sortable' => true,'index' => 'customer_id') );
        /**
         * Customer email id.
         */
        $this->addColumn ( 'customer_mail_id', array (
                'header' => $this->__ ( 'Email' ),
                'sortable' => true,'index' => 'customer_mail_id' 
        ) );
        /**
         * Product name.
         */
        $this->addColumn ( 'deal_id', array (
                'header' => $this->__ ( 'Product Name' ),
                'sortable' => true,'index' => 'deal_id' 
        ) );
        /**
         * Product quantity.
         */
        $this->addColumn ( 'quantity', array (
                'header' => $this->__ ( 'Quantity' ),
                'sortable' => true, 'index' => 'quantity',
                'type' => 'number', 'align' => 'right' 
        ) );
        /**
         * Actual price.
         */
        $this->addColumn ( 'actual_price', array (
                'header' => $this->__ ( 'Original Price' ),
                'sortable' => true, 'index' => 'actual_price',
                'align' => 'right', 'type' => 'currency', 'currency_code' => $currencyCode 
        ) );
        /**
         * Deal price.
         */
        $this->addColumn ( 'deal_price', array (
                'header' => $this->__ ( 'Deal Price' ),'sortable' => true,
                'index' => 'deal_price','align' => 'right',
                'type' => 'currency','currency_code' => $currencyCode ) );
        /**
         * Stauts.
         */
        $this->addColumn ( 'status', array ( 'header' => $this->__ ( 'Status' ), 'sortable' => true,
                'index' => 'status','align' => 'left', 'width' => 1 ) );
        $this->addExportType ( '*/*/exportDealzCsv', Mage::helper ( 'superdeals' )->__ ( 'CSV' ) );
        $this->addExportType ( '*/*/exportDealzExcel', Mage::helper ( 'superdeals' )->__ ( 'Excel XML' ) );
        return parent::_prepareColumns ();
    }
    
    /**
     * Provide the link url of the data displayed
     *
     * Link url is set as "NUll"
     *
     * @return void
     */
    public function getRowUrl() {
        return $this->null;
    }
}