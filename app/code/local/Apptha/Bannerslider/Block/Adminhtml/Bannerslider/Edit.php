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

class Apptha_Bannerslider_Block_Adminhtml_Bannerslider_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    public function __construct() {
        parent::__construct ();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'bannerslider';
        $this->_controller = 'adminhtml_bannerslider';
        
        $this->_updateButton ( 'save', 'label', Mage::helper ( 'bannerslider' )->__ ( 'Save Banner' ) );
        $this->_updateButton ( 'delete', 'label', Mage::helper ( 'bannerslider' )->__ ( 'Delete Banner' ) );
        
        $this->_addButton ( 'saveandcontinue', array (
                'label' => Mage::helper ( 'adminhtml' )->__ ( 'Save And Continue Edit' ),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save' 
        ), - 100 );
        
        $this->_formScripts [] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('bannerslider_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'bannerslider_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'bannerslider_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    /**
     * Set header text
     */
    public function getHeaderText() {
        if (Mage::registry ( 'bannerslider_data' ) && Mage::registry ( 'bannerslider_data' )->getId ()) {
            return Mage::helper ( 'bannerslider' )->__ ( "Edit banner '%s'", $this->htmlEscape ( Mage::registry ( 'bannerslider_data' )->getTitle () ) );
        } else {
            return Mage::helper ( 'bannerslider' )->__ ( 'Add Banner' );
        }
    }
}