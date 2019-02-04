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
class Apptha_Sociallogin_Block_Sociallogin extends Mage_Core_Block_Template {
    /**
     * preparing the social login pop-up layout
     *
     * Include the social login js file
     */
    public function _prepareLayout() {
        if (Mage::getStoreConfig ( 'sociallogin/general/enable_sociallogin' ) == 1 && ! Mage::helper ( 'customer' )->isLoggedIn ()) {
            $this->getLayout ()->getBlock ( 'head' )->addJs ( 'sociallogin/sociallogin.js' );
        }
        
        return parent::_prepareLayout ();
    }
}