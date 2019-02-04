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
 * Deal Statistics
 * This class is used to Display Deal statistics in admin admin grid
 */
class Apptha_Superdeals_Block_Adminhtml_Report_DealStatistics_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
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
        $this->setId ( 'gridDealStatisticsReport' );
        $this->setDefaultSort ( 'serial_id' );
        $this->setDefaultDir ( 'DESC' );
    }
    
    /**
     * Get collection from Deal Statistics table
     *
     * Return array of data to with sold deal products information
     *
     * @return array
     */
    protected function _prepareCollection() {
        $superdealsCollection = Mage::getModel ( 'superdeals/dealstatistics' )->getCollection ();
        $this->setCollection ( $superdealsCollection );
        return parent::_prepareCollection ();
    }
    
    /**
     * Mass action for delete the deals
     *
     * Will delete the selected deal products
     *
     * @return void
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField ( 'serial_id' );
        $this->getMassactionBlock ()->setFormFieldName ( 'superdeals' );
        $this->getMassactionBlock ()->addItem ( 'delete', array (
                'label' => Mage::helper ( 'superdeals' )->__ ( 'Delete' ),
                'url' => $this->getUrl ( '*/*/massDelete' ),
                'confirm' => Mage::helper ( 'superdeals' )->__ ( 'Are you sure?' ) 
        ) );
        return $this;
    }
    
    /**
     * Display the Deal Statistics in grid
     *
     * Display information about deal products
     *
     * @return void
     */
    protected function _prepareColumns() {
        $sym = Mage::app ()->getStore ()->getBaseCurrencyCode ();
        $now = Mage::getModel ( 'core/date' )->timestamp ( time () );
        $now = date ( 'Y-m-d' . ' 00:00:00', $now );
        /**
         * Add serial id.
         */
        $this->addColumn ( 'serial_id', array (
                'header' => $this->__ ( 'Deal ID' ),
                'type' => 'number',
                'sortable' => true,
                'index' => 'serial_id',
                'width' => 1 
        ) );
        /**
         * Add deal id.
         */
        $this->addColumn ( 'deal_id', array (
                'header' => $this->__ ( 'Product Name' ),
                'sortable' => true,
                'index' => 'deal_id' 
        ) );
        /**
         * Add sku.
         */
        $this->addColumn ( 'sku', array (
                'header' => $this->__ ( 'SKU' ),
                'sortable' => true,
                'index' => 'sku' 
        ) );
        /**
         * Add actual price.
         */
        $this->addColumn ( 'actual_price', array (
                'header' => $this->__ ( 'Original Price' ),
                'sortable' => true,
                'index' => 'actual_price',
                'type' => 'currency',
                'currency_code' => $sym 
        ) );
        /**
         * Add deal price.
         */
        $this->addColumn ( 'deal_price', array (
                'header' => $this->__ ( 'Deal Price ' ),
                'sortable' => true,
                'index' => 'deal_price',
                'type' => 'currency',
                'currency_code' => $sym 
        ) );
        /**
         * Add quantity.
         */
        $this->addColumn ( 'quantity', array (
                'header' => $this->__ ( 'Total Quantity Sold' ),
                'sortable' => true,
                'index' => 'quantity',
                'default' => $this->__ ( '0' ),
                'align' => 'right',
                'type' => 'number',
                'width' => 1 
        ) );
        /**
         * Add total sales.
         */
        $this->addColumn ( 'total_sales', array (
                'header' => $this->__ ( 'Sub total' ),
                'sortable' => true,
                'index' => 'total_sales',
                'default' => $this->__ ( '0' ),
                'type' => 'currency',
                'currency_code' => $sym 
        ) );
        /**
         * Add save amount.
         */
        $this->addColumn ( 'save_amount', array (
                'header' => $this->__ ( 'Discount Offered' ),
                'sortable' => true,
                'index' => 'save_amount',
                'default' => $this->__ ( '0' ),
                'type' => 'currency',
                'currency_code' => $sym 
        ) );
        /**
         * Add deal start date.
         */
        $this->addColumn ( 'deal_start_date', array (
                'header' => $this->__ ( 'Deal Start Date ' ),
                'sortable' => true,'index' => 'deal_start_date',
                'width' => 1,'type' => 'date',
                'align' => 'center',
                'default' => $this->__ ( 'N/A' ),'html_decorators' => array ('nobr')) );
        /**
         * Add deal end date.
         */
        $this->addColumn ( 'deal_end_date', array (
                'header' => $this->__ ( 'Deal End Date ' ),
                'sortable' => true,
                'index' => 'deal_end_date',
                'width' => 1,'type' => 'date',
                'align' => 'center',
                'default' => $this->__ ( 'N/A' ),
                'html_decorators' => array ('nobr') 
        ) );
        /**
         * Add status.
         */
        $this->addColumn ( 'status', array ('header' => $this->__ ( 'Status' ),
                'sortable' => true, 'index' => 'status',
                'width' => 1,'align' => 'center',
                'type' => 'options','options' => array (
                        'Active' => 'Active','Inactive' => 'Inactive' ) ) );
        $this->addExportType ( '*/*/exportDealstatisticsCsv', Mage::helper ( 'superdeals' )->__ ( 'CSV' ) );
        $this->addExportType ( '*/*/exportDealstatisticsExcel', Mage::helper ( 'superdeals' )->__ ( 'Excel XML' ) );
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