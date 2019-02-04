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
 * This file contains designer login/ registration, designer profile page functionality
 */
class Apptha_Marketplace_SubscriptionController extends Mage_Core_Controller_Front_Action {
    /**
     * Subscription page display
     *
     * @return void
     */
    function subscribeAction() {
        /**
         * Get customer group id.
         */
        $getCustomerGroupId = Mage::getSingleton ( 'customer/session' )->getCustomerGroupId ();
        /**
         * Get seller Group id
         * @var unknown
         */
        $getSellerGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
        /**
         * Check seller login.
         */
        if (! Mage::getSingleton('customer/session' )->isLoggedIn () && $getCustomerGroupId != $getSellerGroupId) {
            Mage::getSingleton('core/session' )->addError( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect('marketplace/seller/login' );
            return;
        }
        /**
         * Load and render layout
         */
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Paypal payment success
     *
     * @return void
     */
    public function paymentsuccessAction() {
        $request = "cmd=_notify-validate";
        /**
         * Increment foreach loop
         */
        foreach ( $_POST as $varname => $varvalue ) {
            /**
             * check condition get_magic_quotes_gpc function is exist
             */
            if (function_exists ( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc ()) {
                $varvalue = urlencode ( stripslashes ( $varvalue ) );
            } else {
                $value = urlencode ( $value );
            }
            /**
             * end if
             */
            $request .= "&$varname=$varvalue";
        }
        /**
         * Get sandbox mode.
         */
        $sandbox = Mage::getStoreConfig('marketplace/subscription/live_sandbox_mode' );
        /**
         * check condition sanbox mode value is equal to 1
         */
        if ($sandbox == 1) {
            $actionUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $actionUrl = 'https://www.paypal.com/cgi-bin/webscr';
        }
        
        /**
         * Curl function to get response from paypal.
         */
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $actionUrl );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $request );
        curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, false );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        /**
         * check condition result value is verified or not verified
         */
        if (trim ( rtrim ( $result, ',' ) ) == 'VERIFIED') {
            /**
             * Get parameter value.
             */
            $paymentStatus = $this->getRequest ()->getPost ( 'payment_status' );
            /**
             * Get transaction id
             * @var int
             */
            $txnId = $this->getRequest()->getPost ( 'txn_id' );
            /**
             * Get payment date
             * @var date
             */
            $paymentDate = $this->getRequest ()->getPost ( 'payment_date' );
            /**
             * Get item name
             * @var string
             */
            $itemName = $this->getRequest ()->getPost ( 'item_name' );
            $invoice = $this->getRequest ()->getPost ( 'invoice' );
            $settleAmount = $this->getRequest ()->getPost ( 'mc_gross' );
            /**
             * Update subscription payment table
             */
            $collection = Mage::getModel ( 'marketplace/subscriptionpayments' )->load ( $invoice, 'invoice' );
           /**
            * Set status,
            * txn id,
            * paid date.
            * amount,item name
            */
            $collection->setStatus ( $paymentStatus );
            $collection->setTxnCode ( $txnId );
            $collection->setPaidDate ( $paymentDate );
            $collection->setAmount ( $settleAmount );
            $collection->setItemName ( $itemName );
            $collection->save ();
            /**
             * Update Subscribed info table
             */
            $subscribedInfo = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $invoice, 'invoice_id' );
            $subscribedInfo->setPaidDate ( $paymentDate );
            $subscribedInfo->setStatus ( $paymentStatus );
            $subscribedInfo->save ();
            /**
             * Enable seller products
             */
            $subscribedInfo = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $invoice, 'invoice_id' );
            /**
             * Get Seller Id
             * @var int
             */
            $sellerId = $subscribedInfo->getSellerId ();
            /**
             * Get Product Collection
             * @var obj
             */
            $product = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $sellerId );
            /**
             * Increment foreach loop
             */
            foreach ( $product as $_product ) {
                $productId = $_product->getId ();
                Mage::getModel ( 'catalog/product' )->load ( $productId )->setStatus ( 1 )->save ();
            }
        } else {
            /**
             * Display error, when get error while processing a request .
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Some error occured while processing your request' ) );
            $this->_redirect ( 'marketplace/subscription/subscribe' );
        }
        /**
         * end if
         */
    }
    /**
     * Subscription success message with email notification
     *
     * @return void
     */
    public function subscriptionsuccessAction() {
        /**
         * Get params from the form.
         */
        $invoiceId = $this->getRequest ()->getParam ( 'invoice' );
        $planId = $this->getRequest ()->getParam ( 'planid' );
        $sellerId = $this->getRequest ()->getParam ( 'customerid' );
        $subscriptionPlanInfo = Mage::helper ( 'marketplace/subscription' )->getSubscriptionPlanInfo ( $planId );
        $planName = $subscriptionPlanInfo->getPlanName ();
        $planProductCount = $subscriptionPlanInfo->getProductCount ();
        $planAmount = Mage::app ()->getLocale ()->currency ( $currencyCode )->getSymbol () . $this->getRequest ()->getParam ( 'planamount' );
        $subscriptionStartDate = Mage::helper ( 'marketplace/marketplace' )->subscribedDate ( $sellerId );
        $subscriptionEndDate = Mage::helper ( 'marketplace/marketplace' )->subscriptionEndDate ( $sellerId );
        $subscriptionEndDate = date ( "F j, Y", strtotime ( $subscriptionEndDate ) );
        $subscribedInfo = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $invoiceId, 'invoice_id' );
        $sellerId = $subscribedInfo->getSellerId ();
        $payPalverify = $subscribedInfo->getPaypalVerify ();
        $planIds = explode ( '_', $payPalverify );
        $planId = $planIds [1];
        $adminApproval = $subscribedInfo->getAdminApproval ();
        $oldPlanId = $subscribedInfo->getPlanId ();
        $enableProductCount = 1;
        /**
         * Get product collection.
         */
        $product = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $sellerId );
        foreach ( $product as $_product ) {
            $productId = $_product->getId ();
            if ($enableProductCount <= $planProductCount) {
                Mage::getModel ( 'catalog/product' )->load ( $productId )->setStatus ( 1 )->save ();
                $enableProductCount ++;
            }
        }
        /**
         * Check subscription for seller.
         */
        $adminApproval = '1';
        $checksubscription = Mage::helper ( 'marketplace/subscription' )->checkSubscribed ( $sellerId );
        /**
         * check condition subscribtion value is equal to 1 or 3
         */
        if ($checksubscription == 1 || $checksubscription == 3) {
            $adminApproval = '2';
        }
        /**
         * set subscribtion value
         */
        $subscribedInfo = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $invoiceId, 'invoice_id' );
        $oldActiveDate = $subscribedInfo->getOldActiveDate ();
        $subscribedInfo->getOldActiveDate ( $oldActiveDate );
        $subscribedInfo->setStatus ( 'pending' );
        $subscribedInfo->setAdminApproval ( $adminApproval );
        if ($adminApproval != 2) {
            $subscribedInfo->setPlanId ( $planId );
            $subscribedInfo->save ();
            /**
             * Email Notification of Successful subscription to customer
             */
            $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/subscription/email_notification' );
            $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
            $fromMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
            $fromName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
            /**
             * check condition template id is not empty
             */
            if ($templateId) {
                $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
            } else {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_subscription_email_notification' );
            }
            /**
             * Get customer information.
             */
            $customer = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $sellerId );
            $sellerEmail = $customer->getEmail ();
            $sellerName = $customer->getName ();
            $recipientSeller = $sellerEmail;
            $emailTemplate->setSenderName ( $fromName );
            $emailTemplate->setSenderEmail ( $fromMailId );
            $emailTemplateVariables = (array (
                    'invoice' => $invoiceId,
                    'sellername' => $sellerName,
                    'planname' => $planName,
                    'planamount' => $planAmount,
                    'startdate' => $subscriptionStartDate,
                    'enddate' => $subscriptionEndDate 
            ));
            $emailTemplate->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            /**
             * Send email notification.
             */
            $emailTemplate->send ( $recipientSeller, $sellerName, $emailTemplateVariables );
            /**
             * Email notification to admin for seller/designer subscription
             */
            $getSubcribeTemplateId = ( int ) Mage::getStoreConfig ( 'marketplace/subscription/admin_subscribe' );
            $marketplaceAdminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
            $fromMailAddress = Mage::getStoreConfig ( "trans_email/ident_$marketplaceAdminEmailId/email" );
            $fromAdminName = Mage::getStoreConfig ( "trans_email/ident_$marketplaceAdminEmailId/name" );
            /**
             * check condition template id is not empty
             */
            if ($getSubcribeTemplateId) {
                $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $getSubcribeTemplateId );
            } else {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_subscription_admin_subscribe' );
            }
            /**
             * Load seller details.
             */
            $customerDetails = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $sellerId );
            $sellerEmail = $customerDetails->getEmail ();
            $sellerName = $customerDetails->getName ();
            $emailTemplate->setSenderName ( $sellerName );
            $emailTemplate->setSenderEmail ( $sellerEmail );
            $emailTemplateVariables = (array (
                    'ownername' => $fromAdminName,
                    'invoice' => $invoiceId,
                    'selleremail' => $sellerEmail,
                    'sellername' => $sellerName,
                    'planname' => $planName,
                    'planamount' => $planAmount,
                    'startdate' => $subscriptionStartDate,
                    'enddate' => $subscriptionEndDate 
            ));
            $emailTemplate->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            /**
             * Send email notification.
             */
            $emailTemplate->send ( $fromMailAddress, $fromAdminName, $emailTemplateVariables );
            Mage::getSingleton ( 'core/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'You have successfully subscribed' ) );
        } else {
            /**
             * Need to change the payment status
             */
            $subscribedInfo->setUpgradeTo ( $planId );
            $subscribedInfo->save ();
            $this->adminSendMail($customer, $sellerId , $planName,$invoiceId, $oldPlanId );
        }
        $this->_redirect ( 'marketplace/subscription/pagedelay/' );
    }
    /**
     * Function to send mail to admin
     * @param unknown $customer
     * @param unknown $sellerId
     * @param unknown $planName
     * @param unknown $invoiceId
     * @param unknown $oldPlanId
     */
    public function adminSendMail ($customer, $sellerId , $planName,$invoiceId, $oldPlanId ) {
        /**
        * Get the admin information from configuration.
        */
        $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        $adminMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        $adminName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        /**
         * Get seller deatils.
        */
        $customer = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $sellerId );
        $sellerEmail = $customer->getEmail ();
        $sellerName = $customer->getName ();
        $templeId = ( int ) Mage::getStoreConfig ( 'marketplace/subscription/upgradation_seller_notify' );
        
        /**
         * if it is user template then this process is continue
        */
        if ($templeId) {
        $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templeId );
        } else {
        
        /**
        * we are calling default template
        */
        $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_subscription_upgradation_seller_notify' );
        }
        
        /**
        * Mail sender name
        */
        $emailTemplate->setSenderName ( $adminName );
        /**
         * Mail sender email id
        */
        $emailTemplate->setSenderEmail ( $adminMailId );
        $emailTemplateVariables = (array (
                'planname' => $planName,
                'invoice' => $invoiceId,
                'ownername' => $sellerName
        ));
        $emailTemplate->setDesignConfig ( array (
        'area' => 'frontend'
        ) );
        
        /**
         * It return the temp body
        */
        $processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        /**
        * Send mail to customer email ids
        */
        $emailTemplate->send ( $sellerEmail, $sellerName, $emailTemplateVariables );
        $sellername = $customerData ['firstname'] . $customerData ['lastname'];
        $planInfoOld = Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $oldPlanId );
        $PlanNameOld = $planInfoOld->getPlanName ();
        $templeId = ( int ) Mage::getStoreConfig ( 'marketplace/subscription/admin_upgradation_notification' );
        /**
         * if it is user template then this process is continue
        */
        if ($templeId) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templeId );
        } else {
        /**
        * we are calling default template
        */
        $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_subscription_admin_upgradation_notification' );
        }
        /**
         * Mail sender name
         */
        $emailTemplate->setSenderName ( $adminName );
        /**
         * Mail sender email id
        */
        $emailTemplate->setSenderEmail ( $adminMailId );
        $emailTemplateVariables = (array (
                'sellername' => $sellername,
                'invoice' => $invoiceId,
                'currentplan' => $PlanNameOld,
                'ownername' => $adminName,
                'newplan' => $planName
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend'
        ) );
        /**
         * It return the temp body
        */
        $processedTemplate = $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * Send mail to customer email ids
        */
        $emailTemplate->send ( $adminMailId, $adminName, $emailTemplateVariables );
        
    }
    /**
     * Function to insert the subscribe data in database
     *
     * @return void
     */
    public function insertsubscribeAction() {
        $this->loadLayout ();
        /**
         * Get customer id.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirectUrl ( Mage::helper ( 'customer' )->getLoginUrl ());
            return;
        }else{
        $customerId = Mage::getSingleton ( 'customer/session' )->getId ();
        $currentDate = Mage::getModel ( 'core/date' )->date ( 'Y-m-d' );
        $invoiceId = $this->getRequest ()->getParam ( 'invoice' );
        $planId = $this->getRequest ()->getParam ( 'planid' );
        /**
         * Get plan subscription information.
         */
        $planInfo = Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId );
        $planAmount = $planInfo->getYearlyFee ();
        $status = empty ( $planAmount ) ? 'success' : '';
        $watingPlan = "wating_" . $planId;
        $adminApproval = '1';
        /**
         * Get the seller subscribed information.
         */
        $subscribedInfos = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $customerId, 'seller_id' );
        $id = $subscribedInfos->getId ();
        $statusExe = $subscribedInfos->getStatus ();
        /**
         * check condition if id is not empty
         */
        if ($id) {
            /**
             * Load marketplace subscribed information.
             */
            $subscribed = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $id );
            $oldPlanDate = $subscribed->getPaidDate ();
            $subscribed->setOldActiveDate ( $oldPlanDate );
            $subscribed->setPaypalVerify ( $watingPlan );
            /**
             * check condition status is expire and plan amount is equal to 0
             */
            if ($statusExe == 'expire' && $planAmount == 0) {
                $subscribed->setStatus ( $status );
                $subscribed->setPlanId ( $planId );
            }
        } else {
            /**
             * Save subscribed information.
             */
            $subscribed = Mage::getModel ( 'marketplace/subscribedinfo' );
            $subscribed->setSellerId ( $customerId );
            $subscribed->setPlanId ( $planId );
            $subscribed->setStatus ( $status );
            $subscribed->setPaypalVerify ( '' );
        }
        /**
         * save subscribtion detail into data base
         */
        $subscribed->setInvoiceId ( $invoiceId );
        $subscribed->setPaidDate ( $currentDate );
        $subscribed->setAdminApproval ( $adminApproval );
        $subscribed->setUpgradeTo ( '' );
        $subscribed->save ();
        /**
         * check condition plan amount is equal to 0
         */
        if ($planAmount == 0) {
            $this->sendMailtoAdmin ( $customerId, $planId );
        }
        /**
         * check condition plan amount is greaterthan 0
         */
        if ($planAmount > 0) {
            /**
             * Save subscription payment information.
             */
            $collection = Mage::getModel ( 'marketplace/subscriptionpayments' );
            $collection->setInvoice ( $invoiceId );
            $collection->setItemNo ( $planId );
            $collection->setCustomerId ( $customerId );
            $collection->save ();
            Mage::app ()->getLayout ()->getBlock ( 'marketplace_subscription' );
            $this->renderLayout ();
        } else {
            $this->_redirect ( 'marketplace/subscription/subscribe/' );
        }
       }
    }
    /**
     * Method to add delay fucntion for paypal response
     *
     * @return void
     */
    public function pagedelayAction() {
        /**
         * Get customer group id.
         */
        $customerGroupId = Mage::getSingleton ( 'customer/session' )->getCustomerGroupId ();
        $sellerGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
        /**
         * check condition customer is logged in
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn () && $customerGroupId != $sellerGroupId) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Method to send mail to admin for free subscription plan
     *
     *
     * @param int $sellerId            
     *
     * @return void
     */
    public function sendMailtoAdmin($sellerId, $planId) {
        /**
         * Get admin information from the admin configuration.
         */
        $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/subscription/admin_subscribe' );
        $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        $fromMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        $fromName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        /**
         * check condition template id is not empty
         */
        if ($templateId) {
            $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
        } else {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_subscription_admin_subscribe' );
        }
        /**
         * Get subscription plan information based on the plan id.
         */
        $subscriptionPlanInfo = Mage::helper ( 'marketplace/subscription' )->getSubscriptionPlanInfo ( $planId );
        $planName = $subscriptionPlanInfo->getPlanName ();
        $subscriptionInvoiceId = Mage::helper ( 'marketplace/outofstock' )->subscribtionInvoiceId ( $sellerId );
        $planAmount = Mage::app ()->getLocale ()->currency ( $currencyCode )->getSymbol () . $subscriptionPlanInfo->getYearlyFee ();
        $subscriptionStartDate = Mage::helper ( 'marketplace/subscription' )->subscribedDate ( $sellerId );
        $subscriptionEndDate = Mage::helper ( 'marketplace/marketplace' )->subscriptionEndDate ( $sellerId );
        $subscriptionEndDate = date ( "F j, Y", strtotime ( $subscriptionEndDate ) );
        /**
         * Load seller information.
         */
        $customer = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $sellerId );
        $sellerEmail = $customer->getEmail ();
        $sellerName = $customer->getName ();
        $emailTemplate->setSenderName ( $sellerName );
        $emailTemplate->setSenderEmail ( $sellerEmail );
        $emailTemplateVariables = (array (
                'ownername' => $fromName,
                'invoice' => $subscriptionInvoiceId,
                'selleremail' => $sellerEmail,
                'sellername' => $sellerName,
                'planname' => $planName,
                'planamount' => $planAmount,
                'startdate' => $subscriptionStartDate,
                'enddate' => $subscriptionEndDate 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        /**
         * Send email for subscribe plan.
         */
        $emailTemplate->send ( $fromMailId, $fromName, $emailTemplateVariables );
    }
}