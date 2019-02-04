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
 * This form addes the form elements to the grid page.
 */
class Apptha_Marketplace_Block_Adminhtml_Sellersubcriptionplans_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
/**
 * Prepare form elements and initilize it.
 *
 * @return object form ansd its elements
 */
protected function _prepareForm() {
/**
 * Define repeted string variables
 */
$label = 'label';
$class = 'class';
$required = 'required';
$name = 'name';
$textType = 'text';
$requiredEntry = 'required-entry';
$adminhtmlSession = 'adminhtml/session';
$form = new Varien_Data_Form ();
$this->setForm ( $form );
$fieldset = $form->addFieldset ( 'Careers_form', array (
'legend' => 'Item information' 
) );
/**
 * Add job title.
 */
$fieldset->addField ( 'job_title', $textType, array (
$label => 'Title',
$class => $requiredEntry,
$required => true,
$name => 'job_title' 
) );
/**
 * Add job description.
 */
$fieldset->addField ( 'job_description', 'textarea', array (
$label => 'Description',$name => 'job_description',
$class => $requiredEntry,
$required => true

) );
/**
 * Add qualification.
 */
$fieldset->addField ( 'qualification', $textType, array (
$label => 'Qualification',
$required => true,
$name => 'qualification',$class => ''
) );
/**
 * Add experience.
 */
$fieldset->addField ( 'experience', $textType, array (
$label => 'Experience',
$class => '',$required => true,$name => 'experience' 


) );
/**
 * Add location.
 */
$fieldset->addField ( 'location', $textType, array (
$label => 'Location',
$class => '',
$required => true,
$name => 'location' 
) );
/**
 * Add vacancies
 */
$fieldset->addField ( 'vacancies', $textType, array (
$label => 'Vacancies',$class => $requiredEntry,
$name => 'vacancies',$required => true

) );
/**
 * Add status.
 */
$fieldset->addField ( 'status', 'select', array (
$label => 'Status',
$name => 'status',
'values' => array (
array (
'value' => 1,
$label => 'Enabled' 
),

array (
'value' => 0,
$label => 'Disabled' 
) 
) 
) );

if (Mage::getSingleton ( $adminhtmlSession )->getCareersData ()) {
$form->setValues ( Mage::getSingleton ( $adminhtmlSession )->getCareersData () );
Mage::getSingleton ( $adminhtmlSession )->setCareersData ( null );
}
if (Mage::registry ( 'Careers_data' )) {
$form->setValues ( Mage::registry ( 'Careers_data' )->getData () );
}
return parent::_prepareForm ();
}
}