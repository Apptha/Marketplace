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
 * This file is used to create table for "Seller Vacation Mode"
 */
$installer = $this;
/**
 *  @var $installer Mage_Core_Model_Resource_Setup 
 */
$installer->startSetup ();
$setup = new Mage_Eav_Model_Entity_Setup ( 'core_setup' );
/**
 * create quick view category field to get the quick view
 * input:select,
 * type:int,
 * group:custom fields
 * scope:global */
$setup->addAttribute ( 'catalog_category', 'quick_view', array (
  'group' => 'Custom Fields',
  'type' => 'int',
  'backend' => '',
  'frontend_input' => '',
  'frontend' => '',
  'label' => 'Quick View',
  'input' => 'select',
  'default' => array (
    0 
  ),
  'class' => '',
  'source' => 'eav/entity_attribute_source_boolean',
  'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
  'visible' => true,
  'frontend_class' => '',
  'required' => false,
  'user_defined' => true,
  'default' => '',
  'position' => 100 
) );
/**
 * Update seller_shipping_option attribute.
 */
$setup->updateAttribute ( 'catalog_product', 'seller_shipping_option', 'apply_to', 'simple' );
/**
 * Update national_shipping_price attribute.
 */
$setup->updateAttribute ( 'catalog_product', 'national_shipping_price', 'apply_to', 'simple' );
/**
 * Update international_shipping_price attribute.
 */
$setup->updateAttribute ( 'catalog_product', 'international_shipping_price', 'apply_to', 'simple' );
/**
 * Update default_country attribute.
 */
$setup->updateAttribute ( 'catalog_product', 'default_country', 'apply_to', 'simple' );
$installer->endSetup ();
