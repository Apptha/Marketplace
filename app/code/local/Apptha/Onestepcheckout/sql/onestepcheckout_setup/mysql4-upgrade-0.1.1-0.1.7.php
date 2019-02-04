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
/**
 * Add additional column for customer feedback
 */
$installer = $this;
$installer->startSetup ();
$resource = Mage::getResourceModel ( 'sales/order_collection' );
if (! method_exists ( $resource, 'getEntity' )) {
    $installer->run ( "ALTER TABLE {$this->getTable('sales_flat_order')} ADD COLUMN `onestepcheckout_customerfeedback` TEXT CHARACTER SET utf8 DEFAULT NULL" );
}

$installer->endSetup ();
