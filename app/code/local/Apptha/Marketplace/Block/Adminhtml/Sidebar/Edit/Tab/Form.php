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
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
?>
<?php
/**
 * This class contains banner slider form
 */
class Apptha_Marketplace_Block_Adminhtml_Sidebar_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * Prepare banner slider form
     */
    protected function _prepareForm() {
        $formObj = new Varien_Data_Form ();
        $this->setForm ( $formObj );
        $fieldset = $formObj->addFieldset ( 'bannerslider_form', array (
                'legend' => Mage::helper ( 'marketplace' )->__ ( 'Sidebar information' ) 
        ) );
        
        $fieldset->addField ( 'title', 'text', array (
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Sidebar Title' ),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'title',
                'maxlength' => 15
        ) );        
      
        
        $fieldset->addField ( 'banner_html', 'text', array (
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Sidebar html' ),
                'class' => 'required-entry',
                'required' => true,
                'name' => 'banner_html',
                'maxlength' => 15
        ) );
        
        /**
         * Slider image resolution
         */
        $res = $this->__ ( 'Resolution: 2000 x 600' );
        if($this->getRequest ()->getParam ( 'id' )){
        $html = "<div>$res</div>";	
        }else{
        	$html = "<div>$res</div>
        	<script type=\"text/javascript\">$('image').addClassName('required-entry');</script>";
        }      

        $fieldset->addField ( 'image', 'image', array (
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Image' ),
        		'class'     => 'required-file',
                'required' => true,
                'after_element_html' => $html,
                'name' => 'file' 
        ) );
        
        /**
         * Banner status
         */
        $fieldset->addField ( 'status', 'select', array (
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Status' ),
                'name' => 'status',
                'values' => array (
                        array (
                                'value' => 1,
                                'label' => Mage::helper ( 'marketplace' )->__ ( 'Enabled' ) 
                        ),
                        
                        array (
                                'value' => 2,
                                'label' => Mage::helper ( 'marketplace' )->__ ( 'Disabled' ) 
                        ) 
                ) 
        ) );
        
        /**
         * Link text
         */
        $fieldset->addField ( 'link', 'text', array (
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Target Url' ),
                'class' => 'validate-url',
                'required' => false,
                'name' => 'link' 
        ) );
        
        /**
         * Sort order field
         */
        $fieldset->addField ( 'sort', 'text', array (
                'name' => 'sort',
                'label' => $this->__ ( 'Sort Order' ),
                'title' => $this->__ ( 'Sort Order' ),
                'class' => 'validate-digits' 
        ) );
        
        /**
         * Checking for bannder slider data
         */
        if (Mage::getSingleton('adminhtml/session')->getBannerSliderData ()) {
            $bannerData = Mage::getSingleton ( 'adminhtml/session' )->getBannerSliderData ();
            Mage::getSingleton('adminhtml/session')->setBannerSliderData ( null );
        } elseif (Mage::registry ( 'marketplace_data' )) {
            $bannerData = Mage::registry ( 'marketplace_data' )->getData ();
        }
        if(isset($bannerData ['stores'])) {
        $bannerData ['store_id'] = explode ( ',', $bannerData ['stores'] );
        }
        $formObj->setValues ( $bannerData );
        
        return parent::_prepareForm ();
    }
}