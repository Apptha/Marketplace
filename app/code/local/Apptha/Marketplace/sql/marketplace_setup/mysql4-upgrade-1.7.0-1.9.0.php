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
 *
 * @var $installer Mage_Core_Model_Resource_Setup
 */
$installer->startSetup ();
/**
 * setup core attribute Mage_Eav_Model_Entity_Setup
 */
$setup = new Mage_Eav_Model_Entity_Setup ( 'core_setup' );
/**
 * create config_assign_simple_id attribute
 * type:text,
 * input:text,
 * scope:global,
 * group:special attributes
 */
$setup->addAttribute ( 'catalog_product', 'config_assign_simple_id', array (
        'group' => 'Special Attributes',
        'input' => 'text',
        'type' => 'text',
        'label' => 'Assign product id [Simple Product]',
        'backend' => '',
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 1,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'is_html_allowed_on_front' => 0,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL 
) );
/**
 * To set product video url attribute which is comes under catalog_product
 * type:text,
 * input:text,
 * scope:global,
 * group:special attributes
 */
$setup->addAttribute ( 'catalog_product', 'apptha_product_video_url', array (
        'group' => 'Special Attributes',
        'input' => 'text',
        'type' => 'text',
        'label' => 'Youtube video url for product',
        'backend' => '',
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 1,
        'visible_on_front' => 1,
        'visible_in_advanced_search' => 0,
        'is_html_allowed_on_front' => 0,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL 
) );
/**
 * Adding new attribute group name "Custom Attribute"
 */
$setup->addAttributeGroup ( 'catalog_product', 'Default', 'Custom Attribute', 1000 );

/**
 * Adding new attribute group name "Custom Attribute"
 */
$installer->getConnection ()->addColumn ( $installer->getTable ( 'marketplace/transaction' ), 'paid_upto', array (
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'nullable' => false,
        'comment' => 'Seller Amount Paid Upto' 
) );
/**
 * Drop marketplace_subscription plan table if exist and create new table or create new table only
 * If table already exist, delete old table and create a new table.
 * Fields:
 * plan id,plan name,
 * subscription period,validity period,
 * offer period,offer validity period,
 * product count,product type,
 * yearly fee,flag,commission,
 * Primary key:plan id
 */
$installer->run ( "DROP TABLE IF EXISTS {$this->getTable('marketplace_subscriptionplans')};
CREATE TABLE IF NOT EXISTS {$this->getTable('marketplace_subscriptionplans')} (
`plan_id` int(11) NOT NULL AUTO_INCREMENT,
`plan_name` varchar(120) NOT NULL,
`subscription_period` int(11) NOT NULL,
`validity_period` int(11) NOT NULL,
`offer_period` int(11) NOT NULL,
`offer_validity_period` int(11) NOT NULL DEFAULT 0,
`product_count` int(11) NOT NULL,
`product_type` int(11) NOT NULL DEFAULT 0,
`yearly_fee` int(11) NOT NULL,
`flag` int(11) NOT NULL,
`commission` varchar(120) NOT NULL,
`commission_fixed` varchar(120) NOT NULL,
PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 " );

/**
 * Table structure for table `marketplace_subscribedinfo`
 *
 * if already table exist, delete old one and create a new table.
 * 
 * Fields:
 * id,seller id,
 * plan id,plan date,
 * invoice id,status,
 * paypal verify,admin approval,
 * upgarde to,old active date
 * 
 * Primary key:id
 */
$installer->run ( "
DROP TABLE IF EXISTS {$this->getTable('marketplace_subscribedinfo')};
CREATE TABLE IF NOT EXISTS {$this->getTable('marketplace_subscribedinfo')} (
`id` int(11) NOT NULL AUTO_INCREMENT,
`seller_id` int(11) NOT NULL,
`plan_id` int(11) NOT NULL,
`paid_date` date NOT NULL,
`invoice_id` varchar(150) CHARACTER SET utf8 NOT NULL,
`status` varchar(50) CHARACTER SET utf8 NOT NULL,
`paypal_verify` varchar(150) CHARACTER SET utf8 NOT NULL,
`admin_approval` varchar(150) CHARACTER SET utf8 NOT NULL,
`upgrade_to` varchar(15) CHARACTER SET utf8 NOT NULL,
`old_active_date` date NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 " );

/**
 * Table structure for table `marketplace_subscriptionpayments`
 *
 * if table already exist, drop the table and create a new table.
 * 
 * Fields:
 * payment id,invoice,
 * amount,txn code,
 * item no,status,
 * paid date,customer id,
 * item name
 * Primary key: payment id
 */
$installer->run ( "
DROP TABLE IF EXISTS {$this->getTable('marketplace_subscriptionpayments')};
CREATE TABLE IF NOT EXISTS {$this->getTable('marketplace_subscriptionpayments')} (
`payment_id` int(11) NOT NULL AUTO_INCREMENT,
`invoice` varchar(150) CHARACTER SET utf8 NOT NULL,
`amount` int(11) NOT NULL,
`txn_code` varchar(150) CHARACTER SET utf8 NOT NULL,
`item_no` int(11) NOT NULL,
`status` varchar(50) CHARACTER SET utf8 NOT NULL,
`paid_date` date NOT NULL,
`customer_id` int(11) NOT NULL,
`item_name` varchar(150) CHARACTER SET utf8 NOT NULL,
PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 " );
/**
 * Field: shipping cost.
 * type:int,
 * length:255
 * Create a column for seller profile table
 */
$installer->getConnection ()->addColumn ( $installer->getTable ( 'marketplace/sellerprofile' ), 'shipping_cost', array (
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'length' => 255,
        'comment' => 'Shipping Cost' 
) );
/**
 * Field: national shipping cost.
 * type:int,
 * length:255
 *
 */
$installer->getConnection ()->addColumn ( $installer->getTable ( 'marketplace/sellerprofile' ), 'national_shipping_cost', array (
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'length' => 255,
        'comment' => 'National Shipping Cost' 
) );
/**
 * Add international shipping cost.
 * Table table name is seller profile
 *  Field: International shipping cost.
 * type:int,
 * length:255
 */
$installer->getConnection ()->addColumn ( $installer->getTable ( 'marketplace/sellerprofile' ), 'international_shipping_cost', array (
        'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable' => false,
        'length' => 255,
        'comment' => 'International Shipping Cost' 
) );
/**
 * Add product status.
 * Which is comes under catalog_product attribute
 * type:int,
 * input:select,
 * scope:global,
 * group:special attributes
 */
$setup->addAttribute ( 'catalog_product', 'productstatus', array (
        'group' => 'Special Attributes',
        'input' => 'select',
        'type' => 'int',
        'label' => 'Product status',
        'backend' => '',
        'visible' => 1,
        'required' => 0,
        'user_defined' => 1,
        'searchable' => 1,
        'filterable' => 0,
        'comparable' => 1,
        'visible_on_front' => 1,
        'used_in_product_listing' => 1,
        'visible_in_advanced_search' => 0,
        'is_html_allowed_on_front' => 0,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
        'source' => 'eav/entity_attribute_source_boolean' 
) );
/**
 * End setup connection
 */
$installer->endSetup ();
