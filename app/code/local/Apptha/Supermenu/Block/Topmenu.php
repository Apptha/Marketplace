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
 * If Super menu module is enabled the class
 */
if (! Mage::getStoreConfig ( 'supermenu/general/enabled' ) || Mage::helper('supermenu')->isMobile()) {
 class Apptha_Supermenu_Block_Topmenu extends Mage_Page_Block_Html_Topmenu {
 }
 
 return;
}

/**
 * If Super menu module is not enabled the class
 */
class Apptha_Supermenu_Block_Topmenu extends Apptha_Supermenu_Block_Navigation {
}
