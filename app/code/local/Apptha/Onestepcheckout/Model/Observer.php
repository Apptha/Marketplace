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
class Apptha_Onestepcheckout_Model_Observer extends Varien_Object {
    /**
     * Save news letter function
     */
    public function save_newsletter_checkout($observer) {
        /**
         * Check newsletter has been subscribed the customer
         */
        if (( bool ) Mage::getSingleton ( 'checkout/session' )->getCustomerIsSubscribed ()) {
            $quote = $observer->getEvent ()->getQuote ();
            /**
             * get Customer
             */
            
            $customer = $quote->getCustomer ();
            /**
             * Getting Sessin
             */
            $session = Mage::getSingleton ( 'core/session' );
            $status = Mage::getModel ( 'newsletter/subscriber' )->subscribe ( $quote->getBillingAddress ()->getEmail () );
            /**
             * Check Checkout methods of user
             */
            switch ($quote->getCheckoutMethod ()) {
                /**
                 * Check checkout method is register
                 */
                case Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER :
                    $customer->setIsSubscribed ( 1 );
                    break;
                /**
                 * Check checkout method is login
                 */
                case Mage_Sales_Model_Quote::CHECKOUT_METHOD_LOGIN_IN :
                    $customer->setIsSubscribed ( 1 );
                    break;
                /**
                 * Check checkout method is guest
                 */
                case Mage_Sales_Model_Quote::CHECKOUT_METHOD_GUEST :
                    $this->getStatus ( $status, $session );
                    break;
                default :
            }
            Mage::getSingleton ( 'checkout/session' )->setCustomerIsSubscribed ( 0 );
        }
        
        /**
         * customer comments
         */
        $orderComment = Mage::app ()->getRequest ()->getParam ( 'onestepcheckout_comments' );
        /**
         * set customer comment
         */
        $observer = $this->setCustomerComment ( $observer, $orderComment );
        
        /**
         * feedback data
         */
        $orderFeedback = Mage::app ()->getRequest ()->getParam ( 'onestepcheckout_feedback' );
        $orderFeedbackFreetext = Mage::app ()->getRequest ()->getParam ( 'onestepcheckout_feedback_freetext' );
        /**
         * set customer feedback
         */
        $observer = $this->setCustomerFeedback ( $observer, $orderFeedback, $orderFeedbackFreetext );
    }
    
    /**
     * Set customer comment
     *
     * @param array $observer            
     * @param string $orderComment            
     * @return array $observer
     */
    public function setCustomerComment($observer, $orderComment) {
        /**
         * Inserts Comments value in sales order table
         */
        $enable_comments = Mage::getStoreConfig ( 'onestepcheckout/display_option/display_comments' );
        /**
         * Check whether display comments enabled
         */
        if ($enable_comments == 1) {
            $orderComment = trim ( $orderComment );
            /**
             * Check the comments is not equal to empty
             */
            if ($orderComment != "") {
                $observer->getEvent ()->getOrder ()->setOnestepcheckoutCustomercomment ( $orderComment );
            }
        }
        return $observer;
    }
    
    /**
     * set customer feedback data
     *
     * @param array $observer            
     * @param string $orderFeedback            
     * @param string $orderFeedbackFreetext            
     * @return array $observer
     */
    public function setCustomerFeedback($observer, $orderFeedback, $orderFeedbackFreetext) {
        /**
         * feedback data
         */
        $enable_comments = Mage::getStoreConfig ( 'onestepcheckout/feedback/enable_feedback' );
        /**
         * Check Feedback of comments has been enabled
         */
        if ($enable_comments == 1) {
            
            $orderFeedback = trim ( $orderFeedback );
            /**
             * Check comments feedback is not equal to empty
             */
            if ($orderFeedback != "") {
                $observer->getEvent ()->getOrder ()->setOnestepcheckoutCustomerfeedback ( $orderFeedback );
            }
        }
        
        $enable_comments = Mage::getStoreConfig ( 'onestepcheckout/feedback/enable_feedback_freetext' );
        /**
         * Check Feedback freetext enabled
         */
        if ($enable_comments == 1) {
            $orderFeedbackFreetext = trim ( $orderFeedbackFreetext );
            /**
             * Check Feedback free text is not equal to empty
             */
            if ($orderFeedbackFreetext != "") {
                $observer->getEvent ()->getOrder ()->setOnestepcheckoutCustomerfeedback ( $orderFeedbackFreetext );
            }
        }
        return $observer;
    }
    /**
     * Function to get Status *
     */
    Public function getStatus($status, $session) {
        try {
            /**
             * Check newsletter subscriber status is not active
             */
            if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                $session->addSuccess ( Mage::helper ( 'onestepcheckout' )->__ ( 'Confirmation request has been sent regarding your newsletter subscription' ) );
            }
        } catch ( Mage_Core_Exception $e ) {
            $session->addException ( $e, Mage::helper ( 'onestepcheckout' )->__ ( 'There was a problem with the newsletter subscription: %s', $e->getMessage () ) );
        } catch ( Exception $e ) {
            $session->addException ( $e, Mage::helper ( 'onestepcheckout' )->__ ( 'There was a problem with the newsletter subscription' ) );
        }
    }
}