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
class Apptha_Marketplace_Block_Subscription_Subscribe extends Mage_Core_Block_Template {
 /**
  * Define repeted string variables
  */
 public $strMarketplaceSubscriptionplans = 'marketplace/subscriptionplans';
 
 /**
  * Load layout function
  */
 protected function _prepareLayout() {
  $this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper ( 'marketplace' )->__ ( 'Subscription' ) );
  return parent::_prepareLayout ();
 }
 /**
  * Function to get subscription details page
  *
  * Return the subscription url
  * 
  * @return string
  */
 public function subscribeUrl() {
  return Mage::getUrl ( 'marketplace/subscription/subscribe' );
 }
 /**
  * Function to get subscription plans
  *
  * Return the subscription plans as array
  * 
  * @return array
  */
 public function subscriptionPlans() {
  return Mage::getModel ( 'marketplace/subscriptionplans' )->getCollection ()->addFieldToFilter ( 'flag', 1 )->setOrder ( 'yearly_fee', 'ASC' );
 }
 /**
  * Function to get particular subscription plan info
  *
  * Passed the plan id to retrieve the info
  * 
  * @param int $planId
  *         Return plan info as an array
  * @return array
  */
 public function retriveplaninfo($planId) {
  return Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId );
 }
 /**
  * Checking whether customer already subscribed or not
  *
  * Passed the customer id to get the subscription details
  * 
  * @param $customerId Return
  *         subscription info as an array
  * @return array
  */
 public function checkSubscribed($customerId) {
  return Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId );
 }
} 