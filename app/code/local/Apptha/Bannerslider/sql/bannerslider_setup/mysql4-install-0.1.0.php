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
 *
 * @var $installer Mage_Core_Model_Resource_Setup
 */
/**
 * Load Initial setup
 */
$installer->startSetup ();
/**
 * Creating a apptha banner slider
 */
$installer->run ( "
        CREATE TABLE {$this->getTable('appthabannerslider')} (
        `banner_id` int(11) unsigned NOT NULL auto_increment,
        `title` varchar(255) NOT NULL default '',
        `file` varchar(255) NULL,`status` smallint(6) NOT NULL default '0',
        `link` varchar(255) NULL,
        `sort` smallint(6) NOT NULL default '0',
        `created_time` datetime NULL,
        `update_time` datetime NULL,
        PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;" );

$installer->endSetup ();
