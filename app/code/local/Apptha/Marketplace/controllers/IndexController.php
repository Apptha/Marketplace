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
class Apptha_Marketplace_IndexController extends Mage_Core_Controller_Front_Action {
 /**
  * Retrieve customer session model object
  *
  * @return Mage_Customer_Model_Session
  */
 protected function _getSession() {
  return Mage::getSingleton ( 'customer/session' );
 }
 /**
  * Load phtml file layout
  *
  * @return void
  */
 public function indexAction() {
   if (! $this->_getSession ()->isLoggedIn ()) {
    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
    $this->_redirect ( 'marketplace/seller/login' );
    return;
   }
   $this->loadLayout ();
   $this->renderLayout ();
 }
 /**
  * Display home page banner images
  *
  * @return void
  */
 public function bannerAction() {
  $this->loadLayout ();
  $this->renderLayout ();
 }
 /**
  * Display category listings
  *
  * @return void
  */
 public function categorydisplayAction() {
  $this->loadLayout ();
  $this->renderLayout ();
 }
}