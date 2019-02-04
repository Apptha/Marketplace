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
 * Shipment method manangement
 * This class used for one step checkout shipment method
 */
class Apptha_Onestepcheckout_Block_Onestep_Shipmethod extends Mage_Checkout_Block_Onepage_Shipping {
    /**
     * Sales Qoute Shipping Address instance
     *
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address = null;
    
    /**
     * Initialize shipping address step
     */
    protected function _construct() {
    }
}
