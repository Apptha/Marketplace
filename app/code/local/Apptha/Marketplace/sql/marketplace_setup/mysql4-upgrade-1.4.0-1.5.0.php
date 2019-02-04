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
$applyTo = array (
  'simple',
  'configurable' 
);
/**
 * Update seller_shipping_option attribute.
 */
$setup->updateAttribute ( 'catalog_product', 'seller_shipping_option', 'apply_to', join ( ',', $applyTo ) );
/**
 * Update national_shipping_price attribute.
 */
$setup->updateAttribute ( 'catalog_product', 'national_shipping_price', 'apply_to', join ( ',', $applyTo ) );
/**
 * Update international_shipping_price attribute.
 */
$setup->updateAttribute ( 'catalog_product', 'international_shipping_price', 'apply_to', join ( ',', $applyTo ) );
/**
 * Update default_country attribute.
 */
$setup->updateAttribute ( 'catalog_product', 'default_country', 'apply_to', join ( ',', $applyTo ) );
/**
 * Add attribute is_assign_product.
 * input:text,
 * type:varchar,
 * group:special attributes
 * scope:global 
 */
$setup->addAttribute ( 'catalog_product', 'is_assign_product', array ('group' => 'Special Attributes','input' => 'text',
  'type' => 'text','label' => 'Is assign product',
  'backend' => '','visible' => 1,
  'required' => 0,'user_defined' => 1,
  'searchable' => 1,'filterable' => 0,
  'comparable' => 1,'visible_on_front' => 1,
  'visible_in_advanced_search' => 0,'is_html_allowed_on_front' => 0,
  'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL 
) );

/**
 * Add attribute assign_product_id.
 * 
 * input:text,
 * type:varchar,
 * group:special attributes
 * scope:global
 * 
 */
$setup->addAttribute ( 'catalog_product', 'assign_product_id', array ('group' => 'Special Attributes',
  'input' => 'text','type' => 'text',
  'label' => 'Assign product id','backend' => '',
  'visible' => 1,'required' => 0,
  'user_defined' => 1,'searchable' => 1,
  'filterable' => 0,'comparable' => 1,
  'visible_on_front' => 1,'visible_in_advanced_search' => 0,
  'is_html_allowed_on_front' => 0,
  'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL 
) );

$installer->endSetup ();
