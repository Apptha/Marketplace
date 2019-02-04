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
?>
<?php

/**
 * This class contains grid details
 */
class Apptha_Bannerslider_Block_Adminhtml_Bannerslider_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * Set grid id and sort order
     */
    public function __construct() {
        parent::__construct ();
        $getThis = $this;
        $getThis->setId ( 'bannersliderGrid' );
        $getThis->setDefaultSort ( 'banner_id' );
        $getThis->setDefaultDir ( 'ASC' );
        $getThis->setSaveParametersInSession ( true );
    }
    /**
     * Get banner sliders collection
     */
    protected function _prepareCollection() {
        $bannerSlideCollection = Mage::getModel ( 'bannerslider/bannerslider' )->getCollection ();
        $this->setCollection ( $bannerSlideCollection );
        return parent::_prepareCollection ();
    }
    /**
     * Prepare banner slider columns
     */
    protected function _prepareColumns() {
        $this->addColumn ( 'banner_id', array (
                'header' => Mage::helper ( 'bannerslider' )->__ ( 'ID' ),
                'align' => 'right',
                'width' => '50px',
                'index' => 'banner_id' 
        ) );
        /**
         * Add banner title
         */
        $this->addColumn ( 'title', array (
                'header' => Mage::helper ( 'bannerslider' )->__ ( 'Banner Title' ),
                'align' => 'left',
                'index' => 'title' 
        ) );
        /**
         * Add banner image.
         */
        $this->addColumn ( 'image', array (
                'header' => Mage::helper ( 'bannerslider' )->__ ( 'Banner Image' ),
                'align' => 'center',
                'index' => 'image',
                'filter' => false,
                'sortable' => false,
                'width' => '150',
                'renderer' => 'bannerslider/adminhtml_renderer_image' 
        ) );
        /**
         * Add sort order.
         */
        $this->addColumn ( 'sort', array (
                'header' => Mage::helper ( 'bannerslider' )->__ ( 'Sort Order' ),
                'align' => 'left',
                'width' => '80px',
                'index' => 'sort' 
        ) );
        /**
         * Add status.
         */
        $this->addColumn ( 'status', array (
                'header' => Mage::helper ( 'bannerslider' )->__ ( 'Status' ),
                'align' => 'left','width' => '80px',
                'index' => 'status','type' => 'options','options' => array (1 => 'Enabled',
                        2 => 'Disabled') ) );
        /**
         * Add banner action.
         */
        $this->addColumn ( 'action', array ('header' => Mage::helper ( 'bannerslider' )->__('Action'),
                'width' => '100', 'type' => 'action','getter' => 'getId',
                'actions' => array (array ( 'caption' => Mage::helper ( 'bannerslider' )->__ ( 'Edit' ),
                                'url' => array ( 'base' => '*/*/edit'  ),
                                'field' => 'id')),
                'filter' => false,'sortable' => false,'index' => 'stores','is_system' => true) );
        return parent::_prepareColumns ();
    }
    /**
     * Prepare banner slide mass action
     */
    protected function _prepareMassaction() {
        $this->setMassactionIdField ( 'banner_id' );
        $this->getMassactionBlock ()->setFormFieldName ( 'bannerslider' );
        /**
         * Add delete option.
         */
        $this->getMassactionBlock ()->addItem ( 'delete', array ('label' => Mage::helper ( 'bannerslider' )->__ ( 'Delete' ),                'url' => $this->getUrl ( '*/*/massDelete' ),
                'confirm' => Mage::helper ( 'bannerslider' )->__ ( 'Are you sure?' )) );
        $statuses = Mage::getSingleton ( 'bannerslider/status' )->getStatusArray ();
        array_unshift ( $statuses, array ('label' => '','value' => '') );
        /**
         * Add change status option.
         */
        $this->getMassactionBlock ()->addItem ( 'status', array ('label' => Mage::helper ( 'bannerslider' )->__ ( 'Change status' ),
                'url' => $this->getUrl ( '*/*/massStatus', array ( '_current' => true ) ),
                'additional' => array ('visibility' => array (
                                'name' => 'status','type' => 'select',
                                'class' => 'required-entry','label' => Mage::helper ( 'bannerslider' )->__ ( 'Status' ),
                                'values' => $statuses ) )) );
        return $this;
    }
    
    /**
     * Getting row url
     */
    public function getRowUrl($row) {
        /**
         * Return edit url link.
         */
        return $this->getUrl ( '*/*/edit', array (
                'id' => $row->getId () 
        ) );
    }
}