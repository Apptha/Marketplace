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
$installer = $this;
/**
 * Ready to start Installer 
 */
$installer->startSetup ();
/**
 * Create object eav model
 */
$installer = new Mage_Eav_Model_Entity_Setup ( 'core_setup' );
/**
 * Add custom attributes into customer attribute
 */
$installer->addAttribute ( 'customer', 'login_provider', array (
        'label' => 'Provider',
        'type' => 'varchar',
        'input' => 'text',
        'visible' => true,
        'required' => false 
) );
/**
 * Get eav config from singleton 
 */
$eavConfig = Mage::getSingleton ( 'eav/config' );
$attribute = $eavConfig->getAttribute ( 'customer', 'login_provider' );
/**
 * enable all action
 */
$attribute->setData ( 'used_in_forms', array (
        'adminhtml_customer',
        'customer_account_create',
        'customer_account_edit' 
) );
/**
 * Save attribute into eav attribute collection
 */
$attribute->save ();
/**
 * End installer setup 
 */
$installer->endSetup (); 