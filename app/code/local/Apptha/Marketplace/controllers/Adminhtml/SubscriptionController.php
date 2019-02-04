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
 * This file is used for commission functionality from admin panel
 */
class Apptha_Marketplace_Adminhtml_SubscriptionController extends Mage_Adminhtml_Controller_Action {
    /**
     * Define repeted string variables
     */
    protected function _initAction() {
        $this->loadLayout ()->_setActiveMenu ( 'marketplace/items' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Items Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ) );
        return $this;
    }
    /**
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Function to Save subscription plans
     * 
     * @params planid,
     * planname,
     * yearlyfee,
     * subscriptionperiod,
     * validityperiod,
     * offerperiod,
     * commission amount,
     * commission fixed,
     * product count,
     * product type
     * @return void
     */
    public function savesubscriptionAction() {
    /**
     * Get subscription plan details
     **/
        $planId = $this->getRequest ()->getPost ( 'plan_id' );
        $planName = $this->getRequest ()->getPost ( 'plan_name' );
        $yearlyFee = $this->getRequest ()->getPost ( 'yearly_fee' );
        $subscriptionPeriod = $this->getRequest ()->getPost ( 'subscription_period' );
        $validityPeriod = $this->getRequest ()->getPost ( 'validity_period' );
        $offerPeriod = $this->getRequest ()->getPost ( 'offer_period' );
        $offerValidityPeriod = $this->getRequest ()->getPost ( 'offer_validity_period' );
        $commision = $this->getRequest ()->getPost ( 'commision_amount' );
        $commissionFixed = $this->getRequest ()->getPost ( 'commission_fixed' );
        $productCount = $this->getRequest ()->getPost ( 'product_count' );
        $productType = $this->getRequest ()->getPost ( 'product_type' );
        /**
         *
         *Save subscription plan
         *@return object
         */ 
        try {
            /**
             *Check whether plan already exists or not
             *@return number
             */ 
            if ($planId == '') {
                $subscriptionCollection = Mage::getModel ( 'marketplace/subscriptionplans' );
                $subscriptionCollection->setPlanName ( $planName );
                $subscriptionCollection->setSubscriptionPeriod ( $subscriptionPeriod );
                $subscriptionCollection->setValidityPeriod ( $validityPeriod );
                $subscriptionCollection->setYearlyFee ( $yearlyFee );
                $subscriptionCollection->setProductCount ( $productCount );
                $subscriptionCollection->setOfferPeriod ( $offerPeriod );
                $subscriptionCollection->setOfferValidityPeriod ( $offerValidityPeriod );
                $subscriptionCollection->setFlag ( '1' );
                $subscriptionCollection->setCommission ( $commision );
                $subscriptionCollection->setCommissionFixed ( $commissionFixed );
                $subscriptionCollection->setProductType ( $productType );
                $subscriptionCollection->save ();
            } else {
            /**
             *load by subscription plan id
             */ 
                $collection = Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId );
                $collection->setPlanName ( $planName );
                $collection->setSubscriptionPeriod ( $subscriptionPeriod );
                $collection->setValidityPeriod ( $validityPeriod );
                $collection->setYearlyFee ( $yearlyFee );
                $collection->setProductCount ( $productCount );
                $collection->setOfferPeriod ( $offerPeriod );
                $collection->setOfferValidityPeriod ( $offerValidityPeriod );
                $collection->setFlag ( '1' );
                $collection->setCommission ( $commision );
                $collection->setCommissionFixed ( $commissionFixed );
                $collection->setProductType ( $productType );
                $collection->save ();
            }
            Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Subscription plan saved successfully' ) );
            $this->_redirect ( '*/*/' );
        } catch ( Mage_Core_Exception $e ) {
            $e->getMessage ();
        }
    }
    /**
     * Delete subscription plans
     * @params id 
     * @return void
     */
    public function deleteAction() {
    /**
     *Get subscription plan id
     */ 
        $planId = $this->getRequest ()->getParam ( 'id' );
        $collection = Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId );
        $collection->setFlag ( 0 )->save ();
        /**
         * Add success Message
         */ 
        Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Subscription plan deleted successfully' ) );
        $this->_redirect ( '*/*/' );
    }
    /**
     * Offline Subscription
     */
    public function offlinesubscriptionAction() {
    /**
     * load and render layout
     */
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Function to Save Offline Subscription
     * @params seller id,
     * currentdate,
     * invoice id,
     * plan id,
     * @return void
     * 
     */
    public function saveOfflineSubscriptionAction() {
        $sellerId = $this->getRequest ()->getParam ( 'sellerid' );
        $currentDate = Mage::getModel ( 'core/date' )->date ( 'Y-m-d' );
        $invoiceId = $this->getRequest ()->getParam ( 'invoice' );
        $planId = $this->getRequest ()->getParam ( 'id' );
        $planInfo = Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId );
        $planAmount = $planInfo->getYearlyFee ();
        $subscribedInfo = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $sellerId, 'seller_id' );
        $id = $subscribedInfo->getId ();
        $subscribed = Mage::getModel ( 'marketplace/subscribedinfo' );
        if ($id) {
            $subscribed = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $id );
        }
        $subscribed->save ();
        $collection = Mage::getModel ( 'marketplace/subscriptionpayments' );
        $collection->setInvoice ( $invoiceId );
        $collection->setItemNo ( $planId );
        $collection->setCustomerId ( $sellerId );
        $collection->setPaidDate ( $currentDate );
        $collection->setStatus ( 'pending' );
        $collection->save ();
        $subscriptionPlanInfo = Mage::helper ( 'marketplace/marketplace' )->getSubscriptionPlanInfo ( $planId );
        $planName = $subscriptionPlanInfo->getPlanName ();
        $planAmount = Mage::app ()->getLocale ()->currency ( $currency_code )->getSymbol () . $subscriptionPlanInfo->getYearlyFee ();
        $subscriptionStartDate = Mage::helper ( 'marketplace/marketplace' )->subscribedDate ( $sellerId );
        $subscriptionEndDate = Mage::helper ( 'marketplace/marketplace' )->subscriptionEndDate ( $sellerId );
        /**
         * Email Notification of Successful subscription to customer
         * @params email template id
         */
        $getemplateId = ( int ) Mage::getStoreConfig ( 'marketplace/subscription/email_notification' );
        $getAdminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        $getfromMailId = Mage::getStoreConfig ( "trans_email/ident_$getAdminEmailId/email" );
        $getfromName = Mage::getStoreConfig ( "trans_email/ident_$getAdminEmailId/name" );
        if ($getemplateId) {
            $getemailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $getemplateId );
        } else {
            $getemailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_subscription_email_notification' );
        }
        $getCustomer = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $sellerId );
        $getSellerEmail = $getCustomer->getEmail ();
        $getSellerName = $getCustomer->getName ();
        $getRecipientSeller = $getSellerEmail;
        $getemailTemplate->setSenderName ( $getfromName );
        $getemailTemplate->setSenderEmail ( $getfromMailId );
        /**
         * Get Email template variables
         * @var array
         */
        $emailTemplateVariables = (array (
                'invoice' => $invoiceId,
                'sellername' => $getSellerName,
                'planname' => $planName,
                'planamount' => $planAmount,
                'startdate' => $subscriptionStartDate,
                'enddate' => $subscriptionEndDate 
        ));
        $getemailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * Send Email using email template
         */
        $getemailTemplate->send ( $getRecipientSeller, $getSellerName, $emailTemplateVariables );
        /**
         * Email notification to admin for seller/designer subscription
         */
        $adminTemplateId = ( int ) Mage::getStoreConfig ( 'marketplace/subscription/admin_subscribe' );
        
        if ($adminTemplateId) {
            $getemailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $adminTemplateId );
        } else {
            $getemailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_subscription_admin_subscribe' );
        }
        $emailTemplateVariables = (array (
                'ownername' => $getfromName,
                'invoice' => $invoiceId,
                'selleremail' => $getSellerEmail,
                'sellername' => $getSellerName,
                'planname' => $planName,
                'planamount' => $planAmount,
                'startdate' => $subscriptionStartDate,
                'enddate' => $subscriptionEndDate 
        ));
        $getemailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        $getemailTemplate->send ( $getfromMailId, $getfromName, $emailTemplateVariables );
        Mage::getSingleton ( 'core/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'You have successfully subcribed' ) );
        $this->_redirect ( 'marketplaceadmin/adminhtml_manageseller/index' );
    }
    
    /**
     * Resolved acl issue while adding secure batch or magento 1.9.2
     */
    protected function _isAllowed() {
    return true;
    }
}