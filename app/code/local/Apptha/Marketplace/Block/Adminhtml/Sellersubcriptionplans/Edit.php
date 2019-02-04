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
 * This Block helps to added grid for editing solar types.
 */
class Apptha_Marketplace_Block_Adminhtml_Sellersubcriptionplans_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
/**
 * Initilize the edit form for solar types.
 */
public function __construct() {
parent::__construct ();

$this->_objectId = 'id';
$this->_blockGroup = 'installer';
$this->_controller = 'adminhtml_sellersubcriptionplans';

$this->_updateButton ( 'save', 'label', 'Save' );
$this->_updateButton ( 'delete', 'label', 'Delete' );
}

/**
 * Display the title for edit or add action.
 *
 * @return string title
 */
public function getHeaderText() {
if (Mage::registry ( 'Sellersubcriptionplans_data' ) && Mage::registry ( 'Sellersubcriptionplans_data' )->getId ()) {
return "Edit this Sellersubcriptionplans" . $this->htmlEscape ( Mage::registry ( 'Sellersubcriptionplans_data' )->getTitle () );
} else {
return 'Add Item';
}
}
}