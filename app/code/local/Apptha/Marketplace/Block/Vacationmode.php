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
 * Seller vacation mode
 * Getting seller vacation information
 */
class Apptha_Marketplace_Block_Vacationmode extends Mage_Core_Block_Template {

    /**
     * Load vacation information by default in vacation form if seller already seller submit the vacation form
     * 
     * Return the seller vacation information
     * @return array
     */
    function loadVactionInfo() {
    /**
     * Get customer data
     * @var obj
     */
        $seller = Mage::getSingleton('customer/session')->getCustomer();
        /**
         * Get Seller Id
         * @var id
         */
        $sellerId = $seller->getId();
        /**
         * load seller object by seller id
         */
        return Mage::getModel('marketplace/vacationmode')->load($sellerId, 'seller_id');       
    }

}

