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
?>
<?php
class Apptha_Marketplace_Block_Adminhtml_Sidebar_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    public function __construct() {
        parent::__construct ();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'marketplace';
        $this->_controller = 'adminhtml_sidebar';
        
        $this->_updateButton ( 'save', 'label', Mage::helper ( 'marketplace' )->__ ( 'Save Banner' ) );
        $this->_updateButton ( 'delete', 'label', Mage::helper ( 'marketplace' )->__ ( 'Delete Banner' ) );
        
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
        if (Mage::registry ( 'marketplace_data' ) && Mage::registry ( 'marketplace_data' )->getId ()) {
            return Mage::helper ( 'marketplace' )->__ ( "Edit banner '%s'", $this->htmlEscape ( Mage::registry ( 'marketplace_data' )->getTitle () ) );
        } else {
            return Mage::helper ( 'marketplace' )->__ ( 'Add Banner' );
        }
    }
}