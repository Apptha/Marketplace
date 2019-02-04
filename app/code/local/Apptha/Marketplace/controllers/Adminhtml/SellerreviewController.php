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
 * Seller Review Details
 * This file is used to maintain seller review details
 */
class Apptha_Marketplace_Adminhtml_SellerreviewController extends Mage_Adminhtml_Controller_Action {
    protected function _initAction() {
        $this->loadLayout ()->_setActiveMenu ( 'marketplace/items' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Items Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Seller Review' ) );
        return $this;
    }
    /**
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {
        $this->_initAction ()->renderLayout ();
    }
    /**
     * Delete multiple reviews
     *
     * @return void
     */
    public function massDeleteAction() {
        /**
         * Get the posted sell buy ids
         */
        $marketplaceIdsArray = $this->getRequest ()->getParam ( 'marketplace' );
        /**
         * check the marketplace ids array is not an array
         * if so then add error message like select atlease one review
         */
        if (! is_array ( $marketplaceIdsArray )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select at least one review' ) );
        } else {
            try {
                foreach ( $marketplaceIdsArray as $marketplaceId ) {
                    Mage::helper ( 'marketplace/common' )->deleteReview ( $marketplaceId );
                }
                /**
                 * Success message on successful deletion
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were successfully deleted', count ( $marketplaceIdsArray ) ) );
            } catch ( Exception $e ) {
                /**
                 * Error message on Review Deletion failure
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
    /**
     * Approve customer reviews for sellers
     *
     * @return void
     */
    public function approveAction() {
        /**
         * Check the posted id is greater than zero
         */
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                $model = Mage::getModel ( 'marketplace/sellerreview' )->load ( $this->getRequest ()->getParam ( 'id' ) );
                $model->setStatus ( '1' )->save ();
                /**
                 * Get customer id
                 * and seller id
                 */
                $customeId = $model->getCustomerId ();
                $sellerId = $model->getSellerId ();
                /**
                 * send email
                 * Get store configuration like
                 * template id
                 * admin email id
                 * to mail id
                 * to name
                 */
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/seller_review/approve_review' );
                $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
                $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
                /**
                 * check the store configure template id has been set
                 * if so load the particular template
                 * if not load the default seller review approval template
                 */
                if ($templateId) {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_seller_review_approve_review' );
                }
                /**
                 * Get customer data like
                 * customer name
                 * customer email id
                 */
                $customer = Mage::getModel ( 'customer/customer' )->load ( $customeId );
                $customerName = $customer->getName ();
                $recipient = $customer->getEmail ();
                $emailTemplate->setSenderEmail ( $toMailId );
                $emailTemplate->setSenderName ( ucwords ( $toName ) );
                /**
                 * Dynamically replace the template variables with the retrieved values
                 */
                $emailTemplateVariables = (array (
                        'ownername' => ucwords ( $toName ),
                        'cname' => ucwords ( $customerName ) 
                ));
                $emailTemplate->setDesignConfig ( array (
                        'area' => 'frontend' 
                ) );
                /**
                 * Sending email using the template and replaced variables
                 */
                $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                $emailTemplate->send ( $recipient, ucwords ( $customerName ), $emailTemplateVariables );
                /**
                 * Get Seller data
                 */
                $sellerData = Mage::getModel ( 'customer/customer' )->load ( $sellerId );
                $recipientSeller = $sellerData->getEmail ();
                $cnameSeller = $sellerData->getName ();
                $emailTemplateVariables = (array (
                        'ownername' => ucwords ( $toName ),
                        'cname' => ucwords ( $cnameSeller ) 
                ));
                $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                $emailTemplate->send ( $recipientSeller, ucwords ( $cnameSeller ), $emailTemplateVariables );
                /**
                 * end email
                 */
                /**
                 * success message upon successfull review approval
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Review approved successfully.' ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * If the review approval failed add the error message
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/' );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    /**
     * Status as Pending once customer posted the reviews for sellers
     *
     * @return void
     */
    public function pendingAction() {
        /**
         * Confirming the posted id is greater than zero
         */
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                $model = Mage::getModel ( 'marketplace/sellerreview' )->load ( $this->getRequest ()->getParam ( 'id' ) );
                $model->setStatus ( '0' )->save ();
                $reviewStatusMsg = 'Review is Pending.';
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( $reviewStatusMsg ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/' );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    /**
     * Delete reviews
     *
     * @return void
     */
    public function deleteAction() {
        $deleteId = $this->getRequest ()->getParam ( 'id' );
        /**
         * Check the posted deleted id is greater than zero
         */
        if ($deleteId > 0) {
            try {
                /**
                 * Reset group id
                 */
                $model = Mage::getModel ( 'marketplace/sellerreview' );
                $model->setId ( $this->getRequest ()->getParam ( 'id' ) )->delete ();
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Review successfully deleted' ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $redirectId = $this->getRequest ()->getParam ( 'id' );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $redirectId 
                ) );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    /**
     * Approve multiple customer reviews for sellers
     *
     * @return void
     */
    public function massApproveAction() {
        /**
         * Get the sellerbuy ids array from posted value
         */
        $marketplaceIdsArray = $this->getRequest ()->getParam ( 'marketplace' );
        /**
         * check the seller buy ids values is not an array
         * if so then add the error message to select atleast one review
         */
        if (! is_array ( $marketplaceIdsArray )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select at least one review' ) );
        } else {
            try {
                foreach ( $marketplaceIdsArray as $marketplaceId ) {
                    $model = Mage::helper ( 'marketplace/common' )->approveReview ( $marketplaceId );
                    $customeId = $model->getCustomerId ();
                    $sellerId = $model->getSellerId ();
                    /**
                     * send email
                     * Get the store configuration like
                     * templateid
                     * Email id of admin
                     * to email id
                     * to name
                     */
                    $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/seller_review/approve_review' );
                    $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                    $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
                    $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
                    
                    $emailTemplate = Mage::helper ( 'marketplace/general' )->getSellerApprovalEmailTemplate ( $templateId, 3 );
                    
                    /**
                     * Get customer data information like
                     * name of the customer
                     * recipient email address
                     * email template
                     */
                    $customer = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $customeId );
                    $customerName = $customer->getName ();
                    $recipient = $customer->getEmail ();
                    $emailTemplate->setSenderName ( ucwords ( $toName ) );
                    $emailTemplate->setSenderEmail ( $toMailId );
                    $emailTemplate->setDesignConfig ( array (
                            'area' => 'frontend' 
                    ) );
                    $emailTemplateVariables = (array (
                            'ownername' => ucwords ( $toName ),
                            'cname' => ucwords ( $customerName ) 
                    ));
                    /**
                     * Dyanamically replacing template variables with the use of retrieved values
                     */
                    $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                    /**
                     * Mail sending function with the replaced values
                     */
                    $emailTemplate->send ( $recipient, ucwords ( $customerName ), $emailTemplateVariables );
                    
                    /**
                     * Get Seller data like
                     * Seller full information
                     * seller email id
                     * seller name
                     */
                    $sellerData = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $sellerId );
                    $recipientSeller = $sellerData->getEmail ();
                    $cnameSeller = $sellerData->getName ();
                    $emailTemplateVariables = (array (
                            'ownername' => ucwords ( $toName ),
                            'cname' => ucwords ( $cnameSeller ) 
                    ));
                    /**
                     * Assign the retrieved values to the email template
                     */
                    $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                    $emailTemplate->send ( $recipientSeller, ucwords ( $cnameSeller ), $emailTemplateVariables );
                
                /**
                 * end email
                 */
                }
                /**
                 * Success message on approval success
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'A total of %d record(s) is successfully approved', count ( $marketplaceIdsArray ) ) );
            } catch ( Exception $e ) {
                /**
                 * Failure message on approval failure
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
    /**
     * change status to pending for multiple customer reviews
     *
     * @return void
     */
    public function massPendingAction() {
        $marketplaceIdsArray = $this->getRequest ()->getParam ( 'marketplace' );
        /**
         * Confirming the marketplaceidsarray is not an array
         * if it is display message to select atleast one review
         */
        if (! is_array ( $marketplaceIdsArray )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select at least one review' ) );
        } else {
            try {
                foreach ( $marketplaceIdsArray as $marketplaceId ) {
                    Mage::helper ( 'marketplace/common' )->approveReview ( $marketplaceId );
                }
                /**
                 * Success message after pending has been done successfully done
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'A total of %d record(s) is pending', count ( $marketplaceIdsArray ) ) );
            } catch ( Exception $e ) {
                /**
                 * Error message after pending has been Failured
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
    
    /**
     * Resolved acl issue while adding secure batch or magento 1.9.2
     */
    protected function _isAllowed() {
        return true;
    }
}
