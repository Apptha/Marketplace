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
 * @package     Apptha_BannerSLider
 * @version     0.1.1
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
 * Alter table appthabannerslider
 */
$installer->run ( "
ALTER TABLE  {$this->getTable('appthabannerslider')} ADD  `banner_html` text NOT NULL;

" );
$setup = new Mage_Eav_Model_Entity_Setup ( 'core_setup' );
$installer->endSetup ();