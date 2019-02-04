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
 * Get Order Details
 */
class Apptha_Marketplace_Block_Adminhtml_Order extends Mage_Adminhtml_Block_Widget_Grid_Container {

    /**
     * Construct the inital display of grid information
     * Setting the Block files group for this grid
     * Setting the Header text to display
     * Setting the Controller file for this grid
     * 
     * Return order details as array
     * @return array
     */
    public function __construct() {
    /**
     * Get Seller Id
     * @var seller_id
     */
        $sellerId = $this->getRequest()->getParam('id');
        $orderCollection = Mage::getModel('marketplace/sellerprofile')->load($sellerId, 'seller_id');
        $sellerTitle = $orderCollection['store_title'];
        $this->_controller = 'adminhtml_order';
        $this->_blockGroup = 'marketplace';
        $this->_headerText = Mage::helper('marketplace')->__('Orders of ' . $sellerTitle);
        $this->_addButton('button1', array(
            'label' => Mage::helper('marketplace')->__('Back'),
            'onclick' => 'setLocation(\'' . $this->getUrl('marketplaceadmin/adminhtml_manageseller/index') . '\')',
            'class' => 'back',
        ));
        parent::__construct();
        $this->_removeButton('add');
    }

}

