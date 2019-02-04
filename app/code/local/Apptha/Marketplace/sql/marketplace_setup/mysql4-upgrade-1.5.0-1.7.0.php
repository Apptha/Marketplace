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
 * This file is used to create table for Add new attribute for refund request
 */
$installer = $this;
/**
 *
 * @var $installer Mage_Core_Model_Resource_Setup
 */
/**
 * Load Initial setup
 */
$installer->startSetup ();
/**
 * Alter table airhotels_calendar,airhotels_customer_inbox
 */
$installer->run ( "
ALTER TABLE  {$this->getTable('marketplace_commission')} ADD  `refund_request_customer` smallint(6) NOT NULL default '0';
ALTER TABLE  {$this->getTable('marketplace_commission')} ADD  `cancel_request_customer` smallint(6) NOT NULL default '0';
ALTER TABLE  {$this->getTable('marketplace_commission')} ADD  `refund_request_seller` smallint(6) NOT NULL default '0';
ALTER TABLE  {$this->getTable('marketplace_commission')} ADD  `item_order_status` varchar(30) NOT NULL default 'pending';
" );

$setup = new Mage_Eav_Model_Entity_Setup ( 'core_setup' );

/**
 * create config_assign_simple_id attribute
 * 
 * input:text,
 * type:varchar,
 * group:special attributes
 * scope:global 
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

$installer->endSetup ();