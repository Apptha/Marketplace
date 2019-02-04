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
class Apptha_Marketplace_Model_Status_Status extends Mage_Core_Model_Abstract {
 /**
  * Define repeated string variables
  */
 const STATUS_PENDING = 0;
 const STATUS_APPROVED = 1;
 const STATUS_DISAPPROVED = 2;
 /**
  * Retrieve option array
  *
  * @return array
  */
 static public function getOptionArray() {
  
  
  return array (
    static::STATUS_PENDING => Mage::helper ( 'marketplace' )->__ ( 'Pending' ),
    static::STATUS_APPROVED => Mage::helper ( 'marketplace' )->__ ( 'Approved' ),
    static::STATUS_DISAPPROVED => Mage::helper ( 'marketplace' )->__ ( 'Disapproved' ) 
  );
 }
} 