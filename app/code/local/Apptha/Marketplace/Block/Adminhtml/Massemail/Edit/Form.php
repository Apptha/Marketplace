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
 * Get seller commission
 * Form to get the seller commission from admin
 */
class Apptha_Marketplace_Block_Adminhtml_Massemail_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    protected function _prepareForm() {
        
        /**
         * Get seller id's
         */
        $sellerIds = $this->getRequest ()->getParam ( 'marketplace' );
        foreach ( $sellerIds as $sellerId ) {
            /**
             * Collect seller information
             * and store the email id
             */
            $customerData = Mage::getModel ( 'customer/customer' )->load ( $sellerId )->getData ();
            $customerEmails [] = array (
                    "value" => $sellerId,
                    "label" => $customerData ['email'] 
            );
        }
        $form = new Varien_Data_Form ( array (
               
                'id' => 'edit_form',
              
                'action' => $this->getUrl ( '*/*/massMailToSeller' ),
                
                'method' => 'post',
                
                'enctype' => 'multipart/form-data' 
        ) );
        
        /**
         * Add base field.
         */
        $baseFieldset = $form->addFieldset ( 'base', array (
                'legend' => Mage::helper ( 'marketplace' )->__ ( 'Mass email to Seller' ) 
        ) );
        
        /**
         * Add mail subject.
         */
        $baseFieldset->addField ( 'mailsubject', 'text', array (
                
                'name' => 'mailsubject',
               
                'title' => Mage::helper ( 'marketplace' )->__ ( 'Mail subject' ),
               
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Mail subject' ),
               
                'required' => true 
        )
         );
        /**
         * Add mail description.
         */
        $baseFieldset->addField ( 'maildescription', 'textarea', array (
                
                'name' => 'maildescription',
                
                'title' => Mage::helper ( 'marketplace' )->__ ( 'Mail Description' ),
               
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Mail Description' ),
               
                'required' => true 
        )
         );
        
        /**
         * Add seller email ids.
         */
        $baseFieldset->addField ( 'selleremailids', 'multiselect', array (
               
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Seller Emails' ),
               
                'class' => 'required-entry',
               
                'required' => true,
                
                'name' => 'mailids',
               
                'onclick' => "return false;",
                
                'onchange' => "return false;",
              
                'value' => $sellerIds,
               
                'values' => $customerEmails,
               
                'disabled' => false,
               
                'readonly' => false,
                
                'tabindex' => 1 
        ) );
        /**
         * Add submit value.
         */
        $baseFieldset->addField ( 'submit', 'submit', array (
               
                'required' => true,
               
                'value' => 'Submit',
                
                'tabindex' => 1 
        ) );
        
        $form->setUseContainer ( true );
        $this->setForm ( $form );
        
        parent::_prepareForm ();
    }
}