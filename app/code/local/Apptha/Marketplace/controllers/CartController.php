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
 * This file is used to product quick view functionality
 */
require_once 'Mage/Checkout/controllers/CartController.php';
class Apptha_Marketplace_CartController extends Mage_Checkout_CartController{
    /**
     * Set back redirect url to response
     *
     * @return Mage_Checkout_CartController
     * @throws Mage_Exception
     */
    protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }

            $this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()
        ) {
        $referralUrl=Mage::getSingleton('core/session')->getCustomRefererUrl();
        if(!empty($referralUrl)){
        $backUrl = Mage::getSingleton('core/session')->getCustomRefererUrl();
        Mage::getSingleton('core/session')->unsCustomRefererUrl();
        }
        $this->getResponse()->setRedirect($backUrl);
        } else {
            if ((strtolower($this->getRequest()->getActionName()) == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }
}