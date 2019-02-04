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
 * This class contains banner slider grid function
 */
class Apptha_Marketplace_Block_Adminhtml_Sidebar extends Mage_Adminhtml_Block_Widget_Grid_Container {
    /**
     * Banner slider grid construct
     */
    public function __construct() {
        $this->_controller = 'adminhtml_sidebar';
        $this->_blockGroup = 'marketplace';
        $this->_headerText = Mage::helper ( 'marketplace' )->__ ( 'Sidebar Slider' );
        $this->_addButtonLabel = Mage::helper ( 'marketplace' )->__ ( 'Add Slider image' );
        parent::__construct ();
    }
}