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
$installer->startSetup ();
$sql = <<<SQLTEXT
DROP TABLE IF EXISTS {$this->getTable('superdeals_orders')};

CREATE TABLE {$this->getTable('superdeals_orders')} (
`serial_id` int(11) unsigned NOT NULL auto_increment,
`customer_id` varchar(50),
`customer_mail_id` varchar(30),
`order_no` varchar(11),
`deal_id` varchar(100),
`quantity` int(11) ,
`actual_price` decimal(12, 4),
`deal_price` decimal(12, 4) ,
`purchase_date` datetime,
`status` varchar(12),
`product_status` varchar(12) NOT NULL,
`duplicate` int(11) NULL,
PRIMARY KEY (`serial_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('superdeals_reports')};
CREATE TABLE {$this->getTable('superdeals_reports')} (
`serial_id` int(11) NOT NULL AUTO_INCREMENT,
`product_id` int(11),
`deal_id` varchar(150),
`sku` varchar(50),
`quantity` int(11),
`actual_price` decimal(12, 4),
`deal_price` decimal(12, 4),
`total_sales` float,
`save_amount` float,
`deal_start_date` date,
`deal_end_date` date,
`status` varchar(12) NOT NULL,
PRIMARY KEY (`serial_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SQLTEXT;
$installer->run ( $sql );

$installer->endSetup ();

