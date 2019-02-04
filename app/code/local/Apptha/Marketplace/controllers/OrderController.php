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
 * This file is used to manage order information
 */
class Apptha_Marketplace_OrderController extends Mage_Core_Controller_Front_Action {
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton ( 'customer/session' );
    }
    /**
     * Load phtml layout file to display order information
     *
     * @return void
     */
    public function indexAction() {
        /**
         * check that seller is login or not.
         */
        if (! $this->_getSession ()->isLoggedIn ()) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        /**
         * Load and render layout
         */
        $this->loadLayout ();
        $this->renderLayout ();
    }

    /**
     * Manage orders by sellers
     *
     * @return void
     */
    public function manageAction() {
        /**
         * check customer logged in or not
         *
         * @var unknown
         */
        $isCustomerLoggedIn = $this->_getSession ()->isLoggedIn ();
        if (! $isCustomerLoggedIn) {
            /**
             * add error message
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        /**
         * load and render layout
         */
        $this->loadLayout ();
        /**
         * Set title
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Manage Orders' ) );
        $this->renderLayout ();
    }
    /**
     * View full order information by seller
     *
     * @return void
     */
    public function vieworderAction() {
        /**
         * Initilize customer and seller group id
         */
        $customerGroupId = $sellerGroupId = $customerStatus = '';
        /**
         * Get customerid,groupid and seller status.
         */
        $customerGroupId = Mage::getSingleton ( 'customer/session' )->getCustomerGroupId ();
        $sellerGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
        $customerStatus = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
        $returnFlag = 0;
        /**
         * Check the seller login.
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn () && $customerGroupId != $sellerGroupId) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $returnFlag = 1;
        }
        /**
         * Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
            $returnFlag = 1;
        }
        if ($returnFlag == 1) {
            $this->_redirect ( 'marketplace/seller/login' );
            return false;
        }
        /**
         * Get the order id from the parms value.
         */
        $orderId = $this->getRequest ()->getParam ( 'orderid' );
        /**
         * Get the product details based on the orderid.
         */
        $orderPrdouctIds = Mage::helper ( 'marketplace/vieworder' )->getOrderProductIds ( Mage::getSingleton ( 'customer/session' )->getId (), $orderId );
        if (count ( $orderPrdouctIds ) <= 0) {
            $this->_redirect ( 'marketplace/order/manage' );
            return false;
        }
        /**
         * Get ordered collection.
         */
        $collection = Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'seller_id', Mage::getSingleton ( 'customer/session' )->getId () )->addFieldToFilter ( 'order_id', $orderId )->getFirstItem ();
        /**
         * Check orderid from the order collection.
         */
        if (count ( $collection ) >= 1 && $collection->getOrderId () == $orderId) {
            /**
             * load and render layout
             */
            $this->loadLayout ();
            $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'View Order' ) );
            $this->renderLayout ();
        } else {
            /**
             * Check the errors, when internal error occured in view order.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You do not have permission to access this page' ) );
            $this->_redirect ( 'marketplace/order/manage' );
            return false;
        }
    }
    /**
     * View full transaction history by seller
     *
     * @return void
     */
    function viewtransactionAction() {
        /**
         * Checked that user is loggin or not.
         */
        if (! $this->_getSession ()->isLoggedIn ()) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        /**
         * load and render layout
         */
        $this->loadLayout ();
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Transaction History' ) );
        $this->renderLayout ();
    }
    /**
     * Seller payment acknowledgement
     *
     * @return void
     */
    function acknowledgeAction() {
        /**
         * Check the user login or not.
         */
        if (! $this->_getSession ()->isLoggedIn ()) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        /**
         * load and render layout
         */
        $this->loadLayout ();
        $this->renderLayout ();
        /**
         * Get commission id parameteers.
         */
        $commissionId = $this->getRequest ()->getParam ( 'commissionid' );
        /**
         * Get transaction status using commission id
         */
        if ($commissionId != '') {
            $collection = Mage::getModel ( 'marketplace/transaction' )->changeStatus ( $commissionId );
            if ($collection == 1) {
                /**
                 * Success message for after payment received status.
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Payment received status has been updated" ) );
                $this->_redirect ( 'marketplace/order/viewtransaction' );
            } else {
                /**
                 * Error message for payment received status.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Payment received status was not updated' ) );
                $this->_redirect ( 'marketplace/order/viewtransaction' );
            }
        }
    /**
     * End if clause
     */
    }
    /**
     * customer order cancel request
     *
     * @return void
     */
    public function cancelAction() {
        /**
         * Admin configuration for order cancel request active status.
         */
        $orderCancelStatusFlag = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/order_cancel_request' );
        $data = $this->getRequest ()->getPost ();
        $emailSent = '';
        /**
         * Get order id
         * @var int
         */
        $orderId = $data ['order_id'];
        $loggedInCustomerId = '';
        /**
         * Check that customer login or not.
         */
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn () && isset ( $orderId )) {
            /**
             * Get customer data
             *  @var id
             */
            $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
            $loggedInCustomerId = $customerData->getId ();
            $customerid = Mage::getModel ( 'sales/order' )->load ( $data ['order_id'] )->getCustomerId ();
        } else {
            /**
             * Error message for the when unwanted person access these request.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You do not have permission to access this page" ) );
            $this->_redirect ( 'sales/order/history' );
            return;
        }
        if ($orderCancelStatusFlag == 1 && ! empty ( $loggedInCustomerId ) && $customerid == $loggedInCustomerId) {
            $shippingStatus = 0;
            try {
                /**
                 * Get templete id for the order cancel request notification.
                 */
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/order_cancel_request_notification_template_selection' );
                if ($templateId) {
                    /**
                     * Load email templete.
                     */
                    $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_cancel_order_admin_email_template_selection' );
                }
                /**
                 * Load order product details based on the orde id.
                 */
                $_order = Mage::getModel ( 'sales/order' )->load ( $orderId );
                /**
                 * Get increment id
                 *  @var int
                 */
                $incrementId = $_order->getIncrementId ();
                $sellerProductDetails = array ();
                $selectedProducts = $data ['products'];
                $selectedItemproductId = '';
                /**
                 * Get the order item from the order.
                 */
                foreach ( $_order->getAllItems () as $item ) {
                    /**
                     * Get Product id
                     * @var int
                     */
                    $itemProductId = $item->getProductId ();
                    $orderItem = $item;
                    if (in_array ( $itemProductId, $selectedProducts )) {
                        $shippingStatus = $this->getShippingStatus ( $orderItem );

                        $sellerId = Mage::getModel ( 'catalog/product' )->load ( $itemProductId )->getSellerId ();
                        $selectedItemproductId = $itemProductId;
                        $sellerProductDetails [$sellerId] [] = $item->getName ();
                    }
                }
                /**
                 * Get seller product details.
                 */
                foreach ( $sellerProductDetails as $key => $productDetails ) {
                    $productDetailsHtml = "<ul>";
                    /**
                     * Increment foreach loop
                     */
                    foreach ( $productDetails as $productDetail ) {
                        $productDetailsHtml .= "<li>";
                        $productDetailsHtml .= $productDetail;
                        $productDetailsHtml .= "</li>";
                    }
                    $productDetailsHtml .= "</ul>";
                    $customer = Mage::getModel ( 'customer/customer' )->load ( $loggedInCustomerId );
                    $seller = Mage::getModel ( 'customer/customer' )->load ( $key );
                    /**
                     * Get customer name and customer email id.
                     */
                    $buyerName = $customer->getName ();
                    $buyerEmail = $customer->getEmail ();
                    $sellerEmail = $seller->getEmail ();
                    $sellerName = $seller->getName ();
                    $recipient = $sellerEmail;
                    if (empty ( $sellerEmail )) {
                        $adminEmailIdVal = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                        /**
                         * Get the to mail id
                         */
                        $getToMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/email" );
                        $recipient = $getToMailId;
                    }
                    $emailTemplate->setSenderName ( $buyerName );
                    $emailTemplate->setSenderEmail ( $buyerEmail );
                    /**
                     * To set cancel/refund request sent
                     */
                    if ($shippingStatus == 1) {
                        $requestedType = $this->__ ( 'cancellation' );
                        Mage::getModel ( 'marketplace/order' )->updateSellerRequest ( $selectedItemproductId, $orderId, $loggedInCustomerId, $sellerId, 0 );
                    } else {
                        $requestedType = $this->__ ( 'return' );
                        Mage::getModel ( 'marketplace/order' )->updateSellerRequest ( $selectedItemproductId, $orderId, $loggedInCustomerId, $sellerId, 1 );
                    }
                    $emailTemplateVariables = array (
                            'ownername' => $sellerName,'productdetails' => $productDetailsHtml, 'order_id' => $incrementId,
                            'customer_email' => $buyerEmail,'customer_firstname' => $buyerName,
                            'reason' => $data ['reason'],'requesttype' => $requestedType,
                            'requestperson' => $this->__ ( 'Customer' )
                    );
                    $emailTemplate->setDesignConfig ( array ('area' => 'frontend') );
                    /**
                     * Sending email to admin
                     */
                    $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                    $emailSent = $emailTemplate->send ( $recipient, $sellerName, $emailTemplateVariables );
                }
                if ($shippingStatus == 1) {
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Item cancellation request has been sent successfully." ) );
                } else {
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Item return request has been sent successfully." ) );
                }
                $this->_redirect ( 'sales/order/view/order_id/' . $data ['order_id'] );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                $this->_redirect ( 'sales/order/view/order_id/' . $data ['order_id'] );
            }
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You do not have permission to access this page" ) );
            $this->_redirect ( 'sales/order/view/order_id/' . $orderId );
        }
    }
    /**
     * Form to send the withdrawal requset to the admin
     *
     * @return void
     */
    function withdrawrequestAction() {
        /**
         * Check whether customer logged in or not
         */
        if (! $this->_getSession ()->isLoggedIn ()) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        /**
         * load and render layout
         */
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * send the withdrawal requset to the admin
     *
     * @return void
     */
    function sendwithdrawreqAction() {
        /**
         * check whether customer logged in or not
         */
        if (! $this->_getSession ()->isLoggedIn ()) {
            /**
             * add error message
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        /**
         * Get message
         *
         * @var string
         */
        $message = $this->getRequest ()->getParam ( 'req_message' );
        /**
         * Get Pending Amount
         *
         * @var Apptha_Marketplace_OrderController $pendingAmount
         */
        $pendingAmount = $this->getRequest ()->getParam ( 'pending-amount' );
        /**
         * Get Customer Data
         *
         * @var uobject
         */
        $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        /**
         * Get customer Id
         *
         * @var unknown
         */
        $customerId = $customerData->getId ();
        /**
         * Sending email to admin to ask for pending amount
         */
        $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        /**
         * Get to mail id
         *
         * @var string
         */
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        /**
         * Get to name
         *
         * @var name
         */
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_seller_withdraw_request_to_admin' );

        /**
         * Load customer details.
         */
        $customer = Mage::getModel ( 'customer/customer' )->load ( $customerId );
        $selleremail = $customer->getEmail ();
        /**
         * Get Recipient details
         *
         * @var unknown
         */
        $recipient = $toMailId;
        $sellername = $customer->getName ();
        $emailTemplate->setSenderName ( $sellername );
        $emailTemplate->setSenderEmail ( $selleremail );
        $emailTemplateVariables = (array (
                'ownername' => $toName,
                'sellername' => $sellername,
                'requestmessage' => $message,
                'pendingamt' => $pendingAmount
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend'
        ) );
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * Send email to admin.
         */
        $emailTemplate->send ( $recipient, $sellername, $emailTemplateVariables );
        /**
         * Success message after send email to admin.
         */
        Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Request Sent to Admin Successfully.' ) );
        $this->_redirect ( 'marketplace/order/viewtransaction/' );
        return;
    }

    /**
     * Get shipping status
     *
     * @param int $orderItem
     * @return int $shippingStatus
     */
    public function getShippingStatus($orderItem) {
        $shippingStatus = '';
        /**
         * Get Qty shipped and check virtual item or not
         */
        if ($orderItem->getQtyShipped () < $orderItem->getQtyOrdered () && $orderItem->getIsVirtual () != 1) {
            $shippingStatus = 1;
        }
        return $shippingStatus;
    }
}