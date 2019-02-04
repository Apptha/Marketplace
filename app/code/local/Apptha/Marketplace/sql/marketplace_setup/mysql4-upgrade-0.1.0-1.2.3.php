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
 * @var $installer Mage_Core_Model_Resource_Setup 
 */
$installer->startSetup();
/**
 * Table structure for table `marketplace_vacationmode`
 * Delete if table exist in the database 
 * Fields:
 * id,seller id,
 * vacation message,product disabled,
 * product status,date from
 * date to,time
 * Primary key:id
 */
$installer->run("
  DROP TABLE IF EXISTS {$this->getTable('marketplace_vacationmode')};

  CREATE TABLE IF NOT EXISTS {$this->getTable('marketplace_vacationmode')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `vacation_message` text CHARACTER SET utf8 NOT NULL,
  `product_disabled` tinyint(4) NOT NULL,
  `vacation_status` tinyint(4) NOT NULL,
  `date_from` datetime NOT NULL,
  `date_to` datetime NOT NULL,
  `set_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
/**
 * create compare_product_id attribute
 * Which is comes under the catalog product attribute
 * input:text,
 * type:varchar,
 * group:special attributes
 * scope:global
 */

$setup->addAttribute('catalog_product', 'compare_product_id', array(
    'group' => 'Special Attributes',
    'input' => 'text',    'type' => 'varchar',    'label' => 'Compare Price with Product Id',   
    'backend' => '',    'visible' => 1,    'required' => 0,
    'user_defined' => 1,    'searchable' => 1,
    'filterable' => 0,    'comparable' => 1,
    'visible_on_front' => 1,    'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
/**
 * create seller shipping option attribute
 * Which is comes under the catalog product attribute
 * input:select,
 * type:varchar,
 * group:special attributes
 * scope:store
 */
$setup->addAttribute('catalog_product', 'seller_shipping_option', array(
    'group' => 'Special Attributes',    'label' => 'Shipping',    'type' => 'varchar',    'input' => 'select',
    'default' => '',    'class' => '',    'backend' => 'eav/entity_attribute_backend_array',
    'frontend' => '',    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'apply_to' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'visible' => true,    'required' => true,
    'user_defined' => false,    'searchable' => true,
    'filterable' => false,    'comparable' => false,
    'visible_on_front' => true,    'option' => array(
        'value' => array('Free' => array(0 => 'Free'), 'Shipping Cost' => array(0 => 'Shipping Cost')),
        'order' => array('Free' => '0', 'Shipping Cost' => '1')
    ),    'visible_in_advanced_search' => true,
));

/**
 * create seller national shipping price
 * Which is comes under the catalog product attribute
 * input:text,
 * type:int,
 * group:special attributes
 * scope:global
 * 
 */
$setup->addAttribute('catalog_product', 'national_shipping_price', array('group' => 'Special Attributes',
    'input' => 'text','type' => 'int',
    'label' => 'National Shipping Price','apply_to' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'backend' => '','visible' => 1,
    'required' => 0, 'user_defined' => 1,'searchable' => 1,
    'filterable' => 0,'comparable' => 1,
    'visible_on_front' => 1,'visible_in_advanced_search' => 0,
    'is_html_allowed_on_front' => 0,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
/**
 * create seller international shipping price
 * Which is comes under the catalog product attribute
 * input:text,
 * type:int,
 * group:special attributes
 * scope:global
 */
$setup->addAttribute('catalog_product', 'international_shipping_price', array(
    'group' => 'Special Attributes',
    'input' => 'text',    'type' => 'int',
    'label' => 'International Shipping Price',
    'apply_to' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'backend' => '',    'visible' => 1,    'required' => 0,
    'user_defined' => 1,    'searchable' => 1,
    'filterable' => 0,    'comparable' => 1,
    'visible_on_front' => 1,'visible_in_advanced_search' => 0, 'is_html_allowed_on_front' => 0,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
/**
 * create seller default country to set shipping price
 * Which is comes under the catalog product attribute
 * input:select,
 * type:varchar,
 * group:special attributes
 * scope:global*/
$setup->addAttribute('catalog_product', 'default_country', array(
    'group' => 'Special Attributes',
    'input' => 'select',
    'type' => 'varchar', 'label' => 'Default Shipping Country',
    'apply_to' => Mage_Catalog_Model_Product_Type::TYPE_SIMPLE,
    'backend' => '',    'visible' => 1,'required' => 0, 'user_defined' => 1,
    'searchable' => 1,    'filterable' => 0,
    'comparable' => 1,    'visible_on_front' => 1,
    'visible_in_advanced_search' => 0,    'is_html_allowed_on_front' => 0,
    'source' => 'catalog/product_attribute_source_countryofmanufacture',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
));
/**
 * End installer setup
 */
$installer->endSetup();
