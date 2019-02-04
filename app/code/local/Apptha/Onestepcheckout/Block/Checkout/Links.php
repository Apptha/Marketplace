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
 * One step checkout links management
 */
class Apptha_OneStepCheckout_Block_Checkout_Links extends Mage_Checkout_Block_Links {
    /**
     * Redirect to onestepcheck url if enabled
     *
     * @return string
     */
    public function addCheckoutLink() {
        if (! $this->helper ( 'onestepcheckout' )->isOnepageCheckoutLinksEnabled ()) {
            return parent::addCheckoutLink ();
        }
        
        if (! $this->helper ( 'checkout' )->canOnepageCheckout ()) {
            return $this;
        }
        if ($parentBlock = $this->getParentBlock ()) {
            $text = $this->__ ( 'Checkout' );
            $parentBlock->addLink ( $text, 'onestepcheckout', $text, true, array (
                    '_secure' => true 
            ), 60, null, 'class="top-link"' );
        }
        return $this;
    }
}
