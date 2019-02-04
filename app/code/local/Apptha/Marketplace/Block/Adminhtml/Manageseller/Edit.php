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
 * Setting the files and info for the seller profile admin grid
 */
class Apptha_Marketplace_Block_Adminhtml_Manageseller_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {

    /**
     * Construct the inital display of grid information
     * Setting the Block files group for this grid
     * Setting the Object id
     * Setting the Controller file for this grid     
     */
    public function __construct() {
        parent::__construct();
        $this->_removeButton('reset');
        $this->_removeButton('delete');
        $this->_updateButton('save', 'onclick', 'saveCommissionForSeller(this)');
        $this->_objectId = 'id';
        $sellBuyText = 'marketplace';
        $adminhtmlManageseller = 'adminhtml_manageseller';
        $this->_blockGroup = $sellBuyText;
        $this->_controller = $adminhtmlManageseller;
    }

    /**
     * Display header text information
     * 
     * Return the header text
     * return varchar    
     */
    public function getHeaderText() {
        $sellerId = $this->getRequest()->getParam('id');
        $sellerCollection = Mage::getModel('marketplace/sellerprofile')->load($sellerId, 'seller_id');
        $sellerTitle = $sellerCollection['store_title'];
        if (!empty($sellerTitle)) {
            return $this->__('Commission Details of ' . $sellerTitle);
        } else {
            return $this->__('Commission Details');
        }
    }

}
