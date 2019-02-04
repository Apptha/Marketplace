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
 * This Block helps to added grid for memberships.
 */
class Apptha_Marketplace_Block_Adminhtml_Sellersubcriptionplans_Grid extends Mage_Adminhtml_Block_Widget_Grid {
public $strMarketplace = 'marketplace';
/**
 * Preparing the membership list layout.
 *
 *
 * @return void
 */
public function __construct() {
parent::__construct ();
$this->setId ( 'SellersubcriptionplansGrid' );
$this->setDefaultSort ( 'id' );
$this->setDefaultDir ( 'DESC' );
$this->setSaveParametersInSession ( true );
}

/**
 * Prepare collection for solar types.
 */
protected function _prepareCollection() {
/**
 * Get Subscription Info Collection
 * 
 * @var status
 */
$collection = Mage::getModel ( 'marketplace/subscribedinfo' )->getCollection ()->addFieldToFilter ( 'status', array (
array (
'eq' => 'success' 
),
array (
'eq' => 'pending' 
) 
) );
$this->setCollection ( $collection );
return parent::_prepareCollection ();
}

/**
 * Add necessary columns for displaying in the grid.
 *
 * @return void;
 */
protected function _prepareColumns() {
/**
 * Define repeted string variables
 */
$header = 'header';
$align = 'align';
$width = 'width';
$index = 'index';
$renderer = 'renderer';
/**
 * Field:id,
 * index:id
 */
$this->addColumn ( 'id', array (
$header => 'ID',
$align => 'right',
$width => '50px',
$index => 'id' 
) );
/**
 * Field:seller_id,
 * index:seller_id
 */
$this->addColumn ( 'seller_id', array (
$header => 'Seller Name',
$align => 'left',
$index => 'seller_id',
$renderer => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Sellername' 
) );
/**
 * Field:plan_id,
 * index:plan_id
 */
$this->addColumn ( 'plan_id', array (
$header => 'Plan Name',
$align => 'left',
$index => 'plan_id',
$renderer => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Planname' 
) );
/**
 * Field:paid_date,
 * index:paid_date
 */
$this->addColumn ( 'paid_date', array (
$header => 'Plan Start Date',
$align => 'left',
$index => 'paid_date',
$renderer => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Plandate' 
) );
/**
 * Field:admin_approval,
 * index:admin_approval
 */
$this->addColumn ( 'admin_approval', array (
$header => Mage::helper ( 'marketplace' )->__ ( 'Status' ),
$align => 'left',
$width => '120px',
$index => 'admin_approval',
'type' => 'options',
 'options' => array (
      0 => 'Payment Cancel',       
      1 => 'Approved',
      2=>  'Waitig for approval',
      3=>  'Expired'       
    )) );
return parent::_prepareColumns ();
}
/**
 * Function to prepare mass action
 * 
 *
 * @return object
 *
 */
protected function _prepareMassaction() {
$this->setMassactionIdField ( 'id' );
$this->getMassactionBlock ()->setFormFieldName ( 'planid' );
$this->getMassactionBlock ()->addItem ( 'Approve', array (
'label' => Mage::helper ( 'marketplace' )->__ ( 'Approve' ),
'confirm' => Mage::helper ( 'marketplace' )->__ ( 'Are you sure?' ),
'url' => $this->getUrl ( '*/*/massApprove' ),
) );
return $this;
}
}