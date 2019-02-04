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
 * This class contains banner edit form content
 */
class Apptha_Marketplace_Block_Adminhtml_Sidebar_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * Prepare banner slider form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form ( array (
                'id' => 'edit_form',
                'action' => $this->getUrl ( '*/*/save', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) ),
                'method' => 'post',
                'enctype' => 'multipart/form-data' 
        ) );
        
        $form->setUseContainer ( true );
        $this->setForm ( $form );
        return parent::_prepareForm ();
    }
}