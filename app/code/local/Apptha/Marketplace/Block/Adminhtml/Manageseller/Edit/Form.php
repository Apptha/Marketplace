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
class Apptha_Marketplace_Block_Adminhtml_Manageseller_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    
    /**
     * Get the get the seller commission from admin
     *
     * @return void
     */
    protected function _prepareForm() {
        /**
         * Get Seller Id
         */
        $sellerId = $this->getRequest ()->getParam ( 'id' );
        /**
         * Load Seller Id
         */
        $collection = Mage::getModel ( 'marketplace/sellerprofile' )->load ( $sellerId, 'seller_id' );
        /**
         * Set Collection
         */
        $this->setCollection ( $collection );
        /**
         * New Varien Form
         */
        $form = new Varien_Data_Form ( array (
                /**
                 * create Id
                 */
                'id' => 'edit_form',
                /**
                 * Set Action
                 */
                'action' => $this->getUrl ( '*/*/savecommission', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) ),
                /**
                 * Method Post
                 */
                'method' => 'post',
                'onsubmit' => "disabledFormSaveAction()",
                'enctype' => 'multipart/form-data' 
        ) );
        /**
         * Add FieldSet
         */
        $fieldset = $form->addFieldset ( 'set_commission', array (
                'legend' => Mage::helper ( 'marketplace' )->__ ( 'Seller Commission' ) 
        ) );
        $fieldset->addField ( 'commission', 'text', array (
                /**
                 * Set Name
                 */
                'name' => 'commission',
                /**
                 * Set Title
                 */
                'title' => Mage::helper ( 'marketplace' )->__ ( 'Seller Commission(%)' ),
                /**
                 * Set Label
                 */
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Seller Commission(%)' ),
                /**
                 * Required Condition
                 */
                'required' => true,
                'class' => 'validate-percents',
                'value' => $collection ['commission'] 
        ) )->setAfterElementHtml ( '
        <script type="text/javascript">
        function saveCommissionForSeller(value){ 
        //<![CDATA[ 
            if (editForm.validator && editForm.validator.validate()){
            value.disable();
             document.getElementById("edit_form").submit();              
            }
     //]]>        
        } 
        </script>' );
        /**
         * Set Container
         */
        $form->setUseContainer ( true );
        /**
         * Set Form
         */
        $this->setForm ( $form );
        return parent::_prepareForm ();
    }
}

