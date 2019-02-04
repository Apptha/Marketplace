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
 * This file is used to create table for save seller default language
 */
$installer = $this;
/**
 *  @var $installer Mage_Core_Model_Resource_Setup */

/**
 * Load Initial setup
 */

/**
 * Table name:marketplace_sellerlanguage
 * Fields: 
 * id,sellerid,
 * productid,storeid,time
 * Primary key:id
 */
$installer->startSetup();
$installer->run("
  DROP TABLE IF EXISTS {$this->getTable('marketplace_sellerlanguage')};

  CREATE TABLE IF NOT EXISTS {$this->getTable('marketplace_sellerlanguage')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ");
/**
 * Table :marketplace_facebookstore
 * @params seller_id,banner_enable,
 * bannername,facebook_store,
 * facebook_appid,
 * facebook_appcanvas,facebook_appsecret,banner_selected
 * Primary key:id
 */
$installer->run ( "
DROP TABLE IF EXISTS {$this->getTable('marketplace_facebookstore')};

CREATE TABLE IF NOT EXISTS {$this->getTable('marketplace_facebookstore')} (
`id` int(11) NOT NULL AUTO_INCREMENT,
`seller_id` int(11) NOT NULL,
`banner_enable` text NOT NULL,
`bannername` text NOT NULL,
`facebook_enable` text NOT NULL,
`facebook_appid` text NOT NULL,
`facebook_appcanvas` text NOT NULL,
`facebook_appsecret` text NOT NULL,
`banner_selected` text NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 " );


/**
 * Marketplace side bar slider
 * @return void
 */

$installer->run ( "
CREATE TABLE {$this->getTable('marketplace_sidebar')} (
`banner_id` int(11) unsigned NOT NULL auto_increment,
`title` varchar(255) NOT NULL default '',
`file` varchar(255) NULL,`status` smallint(6) NOT NULL default '0',
`link` varchar(255) NULL,
`banner_html` text NULL,
`sort` smallint(6) NOT NULL default '0',
`created_time` datetime NULL,
`update_time` datetime NULL,
PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;" );

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

/**
 * Add Attribute For Total Sales
 * which is used in toolbar section
 * type:text,
 * scope:global
 * input:text
 * 
 */
$setup->addAttribute ( 'catalog_product', 'total_sales', array (
'group' => 'Special Attributes',
'input' => 'text',
'type' => 'text',
'label' => 'Total sales of the product',
'backend' => '','visible' => 1,'required' => 0,'user_defined' => 1,
'searchable' => 1,'filterable' => 0,'visible_on_front' => 1,
'visible_in_advanced_search' => 0,
'comparable' => 1,
'default' => 0,
'is_html_allowed_on_front' => 0,
'used_for_sort_by' => 1,
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
) );
/**
 * Add Attribute for Global Commission
 * @scope:global
 * type:text,
 * input:text,
 * group:general information
 */
$attribute  = array(
'type' => 'int',
'label'=> 'Commission (in %)',
'input' => 'text',
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
'visible' => true,
'required' => false,
'user_defined' => true,
'default' => "",
'group' => "General Information"
);
$setup->addAttribute('catalog_category', 'commission', $attribute);

/**
 * Add Attribute for showing category in home page
 * @input:select,
 * scope:global,
 * type:varchar
 * 
 */
$setup->addAttribute(Mage_Catalog_Model_Category::ENTITY, 'isvisible', array(
'group' => 'General Information',
'input' => 'select',
'type' => 'varchar',
'label' => 'Is visible',
'backend' => '',
'visible' => 1,
'required' => 0,
'user_defined' => 1,
'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
'source' => 'eav/entity_attribute_source_boolean',
));
/**
 * Add Attribute for discount
 * 
 * 
 * @return integer
 */
$setup->addAttribute('catalog_product', "discount", array(
        'group' => 'prices',
        'type'       => 'int',
        'input'      => 'select',
        'label'      => 'Discount',
        'visible' => 1,
        'sort_order' => 10,
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'searchable' => 0,
        'filterable' => 0,
        'comparable' => 0,
        'visible_on_front' => 0,
        'visible_in_advanced_search' => 0,
        'is_html_allowed_on_front' => 0,
        'is_configurable' => 1,
        'global'     => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'backend'    => 'eav/entity_attribute_backend_array',
        'option'     => array (
                'values' => array(
                		5 =>'5',
                        10 => '10',
                        20 => '20',
                        30 =>'30',
                        40 =>'40',
                        50 =>'50',
                        60 =>'60',
                        70 =>'70',
                        80 =>'80',
                        90 =>'90',
                )
        ),

));

$installer->endSetup();