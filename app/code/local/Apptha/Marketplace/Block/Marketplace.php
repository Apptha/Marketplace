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
 * This File Contains Become a Seller
 * Option as Become a seller in Dashboard
 */
class Apptha_Marketplace_Block_Marketplace extends Mage_Core_Block_Template {
/**
 * Function to prepare layout
 * {@inheritDoc}
 * @see Apptha_Marketplace_Block_Marketplace::_prepareLayout()
 */
protected function _prepareLayout() {
if (! $this->getLayout ()->getBlock ( 'head' ) == null) {
$this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper ( 'marketplace' )->__ ( 'Marketplace' ) );
}
return parent::_prepareLayout ();
}
/**
 * Function to get Become a seller url Return the become a seller action url @return string
 */
public function becomeAsellerUrl() {
return Mage::getUrl ( 'marketplace/general/becomeseller' );
}
} 