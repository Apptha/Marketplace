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
 * Manage Seller actions in admin section
 * This Class has been used to manage seller actions in admin section like
 * Save
 * Delete
 * Approval
 * Disapproval
 */
class Apptha_Marketplace_Adminhtml_ManagesellerController extends Mage_Adminhtml_Controller_Action {
    protected function _initAction() {
        $this->loadLayout ()->_setActiveMenu ( 'marketplace/items' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Items Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Seller Manager' ) );
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
     * Edit seller data
     *
     * @return void
     */
    public function editAction() {
        $id = $this->getRequest ()->getParam ( 'id' );
        $marketplaceModel = Mage::getModel ( 'marketplace/marketplace' )->load ( $id );
        /**
         * Check the model has been set
         * or the posted id has been set
         */
        if ($marketplaceModel->getId () || $id == 0) {
            $data = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            /**
             * Check the retrieved data is not equal to empty
             */
            if (! empty ( $data )) {
                $marketplaceModel->setData ( $data );
            }
            Mage::register ( 'marketplace_data', $marketplaceModel );
            /**
             * Initialising layout, breadcumbs
             */
            $this->loadLayout ();
            $this->_setActiveMenu ( 'marketplace/items' );
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Seller Manager' ) );
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item News' ), Mage::helper ( 'adminhtml' )->__ ( 'Seller News' ) );
            $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
            $this->_addContent ( $this->getLayout ()->createBlock ( 'marketplace/adminhtml_marketplace_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'marketplace/adminhtml_marketplace_edit_tabs' ) );
            $this->renderLayout ();
        } else {
            /**
             * If the retrieved data is equal to empty
             * display the error message
             */
            $this->getSessionErrorMessage ();
            $this->_redirect ( '*/*/' );
        }
    }
    /**
     * error message function
     */
    public function getSessionErrorMessage() {
        Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'marketplace' )->__ ( 'Seller details does not exist' ) );
    }
    
    /**
     * Save seller data
     * and change the status
     *
     * @return void
     */
    public function saveAction() {
        $data = $this->getRequest ()->getPost ();
        if ($data) {
            $marketplaceModel = Mage::getModel ( 'marketplace/marketplace' );
            $marketplaceModel->setData ( $data )->setId ( $this->getRequest ()->getParam ( 'id' ) );
            try {
                if ($marketplaceModel->getCreatedTime == NULL || $marketplaceModel->getUpdateTime () == NULL) {
                    $marketplaceModel->setCreatedTime ( now () )->setUpdateTime ( now () );
                } else {
                    $marketplaceModel->setUpdateTime ( now () );
                }
                $marketplaceModel->save ();
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Please select at least one seller Approved Successfully' ) );
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( false );
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', array (
                            'id' => $marketplaceModel->getId () 
                    ) );
                    return;
                }
                $this->_redirect ( '*/*/' );
                return;
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( $data );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
                return;
            }
        }
        $this->getErrorMessage ();
        $this->_redirect ( '*/*/' );
    }
    
    /**
     * get ERROR Message
     */
    public function getErrorMessage() {
        Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'marketplace' )->__ ( 'Seller details not updated' ) );
    }
    
    /**
     * Delete multiple seller's at a time
     *
     * @return void
     */
    public function massDeleteAction() {
        /**
         * Get the marketplace ids
         */
        $marketplaceIds = $this->getRequest ()->getParam ( 'marketplace' );
        /**
         * Check the marketplace ids is not an array
         * if so display error message to selecte atleast one seller
         */
        if (! is_array ( $marketplaceIds )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select at least one seller' ) );
        } else {
            try {
                foreach ( $marketplaceIds as $marketplaceId ) {
                    Mage::helper ( 'marketplace/common' )->deleteSeller ( $marketplaceId );
                    $productCollections = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $marketplaceId );
                    
                    Mage::helper ( 'marketplace/general' )->deleteProducts ( $productCollections );
                    Mage::getModel ( 'marketplace/sellerprofile' )->load ( $marketplaceId, 'seller_id' )->delete ();
                }
                /**
                 * Display Success message upon Deletion
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were successfully deleted', count ( $marketplaceIds ) ) );
            } catch ( Exception $e ) {
                /**
                 * Display Error message
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
    /**
     * Setting commission for admin
     *
     * @return void
     */
    public function setcommissionAction() {
        $this->_initAction ()->renderLayout ();
    }
    /**
     * Save commission information in database
     *
     * @return void
     */
    public function savecommissionAction() {
        /**
         * Check the posted id is greater than zero
         * if so get the id from posted value
         * and get the commission details from posted commission
         */
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            $id = $this->getRequest ()->getParam ( 'id' );
            $commission = $this->getRequest ()->getParam ( 'commission' );
            try {
                $collection = Mage::getModel ( 'marketplace/sellerprofile' )->load ( $id, 'seller_id' );
                $getId = $collection->getId ();
                /**
                 * Check the retrieved is is not equal to empty
                 * if so Save the commission information
                 */
                if ($getId != '') {
                    Mage::getModel ( 'marketplace/sellerprofile' )->load ( $id, 'seller_id' )->setCommission ( $commission )->save ();
                } else {
                    $collection = Mage::getModel ( 'marketplace/sellerprofile' );
                    $collection->setCommission ( $commission );
                    $collection->setSellerId ( $id );
                    $collection->save ();
                }
                /**
                 * Display success message on successful commission save action
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Seller commission saved successfully .' ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Display error message on commission save failure
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/' );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    /**
     * Set seller status as approve after seller register in the website
     *
     * @return void
     */
    public function approveAction() {
        /**
         * Confirming the posted id is greater than zero
         * if so get the id from the posted value
         */
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            $id = $this->getRequest ()->getParam ( 'id' );
            try {
                $model = Mage::getModel ( 'customer/customer' )->load ( $this->getRequest ()->getParam ( 'id' ) );
                $model->setCustomerstatus ( '1' )->save ();
                /**
                 * Approve and enable seller products
                 */
                $sellerProductCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('seller_id',$id);
                $sellerProductDatas=$sellerProductCollection->getData();
                foreach($sellerProductDatas as $sellerProducts){
                $productId=$sellerProducts['entity_id'];
                Mage::getModel('catalog/product')->load ( $productId )->setStatus(1)->save ();
                }
                /**
                 * send email to customer regarding approval of seller registration
                 */
                $getTemplateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/seller_email_template_selection' );
                /**
                 * Get the admin email id
                 */
                $adminEmailIdVal = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                /**
                 * Get the to mail id
                 */
                $getToMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/email" );
                /**
                 * Get the to mail id
                 */
                $getToName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/name" );
                if ($getTemplateId) {
                    $getEmailTemplateForSeller = Mage::getModel ( 'core/email_template' )->load ( $getTemplateId );
                } else {
                    $getEmailTemplateForSeller = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_seller_email_template_selection' );
                }
                /**
                 * Get the customer information like
                 * customer data
                 * customer email
                 * customer name
                 */
                $customerData = Mage::getModel ( 'customer/customer' )->load ( $id );
                $getRecipient = $customerData->getEmail ();
                $getCustomerName = $customerData->getName ();
                $getEmailTemplateForSeller->setSenderEmail ( $getToMailId );
                $getEmailTemplateForSeller->setSenderName ( ucwords ( $getToName ) );
                /**
                 * Replacing values in email template with dynamic values
                 */
                $emailTemplateForSellerVariables = (array (
                        'cname' => ucwords ( $getCustomerName ),
                        'ownername' => ucwords ( $getToName ) 
                ));
                $getEmailTemplateForSeller->setDesignConfig ( array (
                        'area' => 'frontend' 
                ) );
                $getEmailTemplateForSeller->getProcessedTemplate ( $emailTemplateForSellerVariables );
                /**
                 * Sending email
                 */
                $getEmailTemplateForSeller->send ( $getRecipient, ucwords ( $getCustomerName ), $emailTemplateForSellerVariables );
                /**
                 * end email
                 */
                $noticMsg = 'Seller approved successfully.';
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( $noticMsg ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Display Error message
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/' );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    /**
     * Set seller status as disapprove after seller register in the website
     *
     * @return void
     */
    public function disapproveAction() {
        /**
         * Checking the posted id values is greater than 0
         * if so retreive the posted id value
         */
    if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            $id = $this->getRequest ()->getParam ( 'id' );
            try {
                $model = Mage::getModel ( 'customer/customer' )->load ( $this->getRequest ()->getParam ( 'id' ) );
                $model->setCustomerstatus ( '2' )->save ();
                /**
                 * DisApprove and enable seller products
                 */
               $sellerProductData = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('seller_id',$id);
                $sellerProducts=$sellerProductData->getData();
                foreach($sellerProducts as $sellerProduct){
                $productId=$sellerProduct['entity_id'];
                Mage::getModel('catalog/product')->load ( $productId )->setStatus ( 2 )->save ();
                }
                
                /**
                 * send email to admin regarding disapprove of seller registration
                 */
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/seller_email_template_disapprove' );
                /**
                 * Get the admin configuration information related mail like
                 * admin email id
                 * to mail id
                 * to name
                 */
                $adminEmailIdData = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdData/email" );
                $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdData/name" );
                /**
                 * Check the template id has been set in admin section
                 * if so load the template id
                 * else load the admin approval seller registration seller email template disapprove template
                 */
                if ($templateId) {
                    $emailTemplateForSeller = Mage::getModel ( 'core/email_template' )->load ( $templateId );
                } else {
                    $emailTemplateForSeller = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_seller_email_template_disapprove' );
                }
                /**
                 * get the customer information like
                 * Customer data
                 * customer name
                 * customer email
                 */
                $customer = Mage::getModel ( 'customer/customer' )->load ( $id );
                $recipient = $customer->getEmail ();
                $customerName = $customer->getName ();
                /**
                 * Replacing the retrieved values dynamicall in the email template
                 */
                $emailTemplateForSellerVariables = (array (
                        'ownername' => ucwords ( $toName ),
                        'cname' => ucwords ( $customerName ) 
                ));
                
                $emailTemplateForSeller->setSenderName ( ucwords ( $toName ) );
                $emailTemplateForSeller->setSenderEmail ( $toMailId );
                
                $emailTemplateForSeller->setDesignConfig ( array (
                        'area' => 'frontend' 
                ) );
                $emailTemplateForSeller->getProcessedTemplate ( $emailTemplateForSellerVariables );
                /**
                 * Send email function
                 */
                $emailTemplateForSeller->send ( $recipient, ucwords ( $customerName ), $emailTemplateForSellerVariables );
                /**
                 * end email
                 * Display the success message on seller disapprove success
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Seller disapproved.' ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Display error message if the seller action is failure
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/' );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    /**
     * Dellete seller from website
     *
     * @return void
     */
    public function deleteAction() {
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                /**
                 * Reset group id
                 */
            $id=$this->getRequest ()->getParam ( 'id' );
                $model = Mage::getModel ( 'customer/customer' )->load ( $this->getRequest ()->getParam ( 'id' ) );
                $model->setGroupId ( 1 );
                $model->save ();
                $productCollections = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $this->getRequest ()->getParam ( 'id' ) );
                foreach ( $productCollections as $product ) {
                    $productId = $product->getEntityId ();
                    $model = Mage::getModel ( 'catalog/product' )->load ( $productId );
                    $model->delete ();
                }
                
                $sellerProductCollection = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('seller_id',$id);
                $sellerProductDatas=$sellerProductCollection->getData();
                foreach($sellerProductDatas as $sellerProducts){
                $productId=$sellerProducts['entity_id'];
                Mage::getModel('catalog/product')->load ( $productId )->setStatus(2)->save ();
                }
                
                Mage::getModel ( 'marketplace/sellerprofile' )->load ( $this->getRequest ()->getParam ( 'id' ), 'seller_id' )->delete ();
                
                /**
                 * show success message on successfull seller deletion
                 */
                $this->successMessage ();
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Error message on seller deletion failure
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    
    /**
     * Success Message
     */
    public function successMessage() {
        Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Seller successfully deleted' ) );
    }
    
    /**
     * Set seller status as approve multiple seller register in the website
     *
     * @return void
     */
    public function massApproveAction() {
        /**
         * Retrieve the sell buy ids from posted values
         */
        $marketplaceIds = $this->getRequest ()->getParam ( 'marketplace' );
        /**
         * Check the sell buy ids is not an array
         * if so display error message like please select atleast one seller
         */
        if (! is_array ( $marketplaceIds )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select at least one seller' ) );
        } else {
            try {
                foreach ( $marketplaceIds as $marketplaceId ) {
                    Mage::helper ( 'marketplace/common' )->approveSellerStatus ( $marketplaceId );
                    /**
                     * send email to customer regarding approval of seller registration
                     */
                    $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/seller_email_template_selection' );
                    /**
                     * Get the stored configured information like
                     * Admin email id
                     * to mail id
                     * to name
                     */
                    $adminEmailIdValue = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                    $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdValue/email" );
                    $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdValue/name" );
                    $emailTemplateForSeller = Mage::helper ( 'marketplace/general' )->getSellerApprovalEmailTemplate ( $templateId, 1 );
                    /**
                     * Get he information of customer like
                     * name of the customer
                     * email id of the customer
                     */
                    $customer = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $marketplaceId );
                    $recipient = $customer->getEmail ();
                    $customerName = $customer->getName ();
                    $emailTemplateForSeller->setSenderName ( ucwords ( $toName ) );
                    $emailTemplateForSeller->setSenderEmail ( $toMailId );
                    /**
                     * Replace the mail template variables with the retrieved information dynamically
                     */
                    $emailTemplateForSellerVariables = (array (
                            'ownername' => ucwords ( $toName ),
                            'cname' => ucwords ( $customerName ) 
                    ));
                    $emailTemplateForSeller->setDesignConfig ( array (
                            'area' => 'frontend' 
                    ) );
                    $emailTemplateForSeller->getProcessedTemplate ( $emailTemplateForSellerVariables );
                    $emailTemplateForSeller->send ( $recipient, ucwords ( $customerName ), $emailTemplateForSellerVariables );
                /**
                 * end email
                 */
                }
                /**
                 * Display succcess message after successfull approval
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'A total of %d record(s) is successfully approved', count ( $marketplaceIds ) ) );
            } catch ( Exception $e ) {
                /**
                 * Display error message if the approval has been failed
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
    /**
     * Set seller status as disapprove multiple seller register in the website
     *
     * @return void
     */
    public function massDisapproveAction() {
        /**
         * Get all the seel buy ids from posted values
         */
        $marketplaceIds = $this->getRequest ()->getParam ( 'marketplace' );
        /**
         * Check the sell buy ids is not an array
         * if so add error message like select seller
         */
        if (! is_array ( $marketplaceIds )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select at least one seller' ) );
        } else {
            try {
                foreach ( $marketplaceIds as $marketplaceId ) {
                    Mage::helper ( 'marketplace/common' )->disapproveSellerStatus ( $marketplaceId );
                    /**
                     * send email to admin regarding disapprove of seller registration
                     */
                    $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/seller_email_template_disapprove' );
                    /**
                     * Get and assign the values like admin email id, to mail id, to name from admin store configuration
                     * if so display error message to selecte atleast one seller
                     */
                    $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                    $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
                    $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
                    
                    $emailTemplateForSeller = Mage::helper ( 'marketplace/general' )->getSellerApprovalEmailTemplate ( $templateId, 2 );
                    /**
                     * Retrieve the customer info like
                     * customername
                     * customer mail id
                     */
                    $customer = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $marketplaceId );
                    $customerName = $customer->getName ();
                    $recipient = $customer->getEmail ();
                    
                    /**
                     * Dyanamically replace the retrieved values in email template
                     */
                    $emailTemplateForSeller->setSenderName ( ucwords ( $toName ) );
                    $emailTemplateForSeller->setSenderEmail ( $toMailId );
                    
                    $emailTemplateForSeller->setDesignConfig ( array (
                            'area' => 'frontend' 
                    ) );
                    $emailTemplateSellerVariables = (array (
                            'ownername' => ucwords ( $toName ),
                            'cname' => ucwords ( $customerName ) 
                    ));
                    $emailTemplateForSeller->getProcessedTemplate ( $emailTemplateSellerVariables );
                    /**
                     * Email sending function for the dynamically replaced values
                     */
                    $emailTemplateForSeller->send ( $recipient, ucwords ( $customerName ), $emailTemplateSellerVariables );
                /**
                 * end email
                 */
                }
                /**
                 * Success message of records on disapproval
                 */
                $successMsg = Mage::helper ( 'adminhtml' )->__ ( 'A total of %d record(s) is successfully disapproved', count ( $marketplaceIds ) );
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( $successMsg );
            } catch ( Exception $e ) {
                /**
                 * Error message of records on disapproval failure
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
    /**
     * Load form for getting the mail template for subject and description
     */
    public function emailToSellersAdminAction() {
        $this->loadLayout ();
        $this->_addContent ( $this->getLayout ()->createBlock ( 'marketplace/adminhtml_massemail_edit_form' ) );
        $this->renderLayout ();
    }
    public function massMailToSellerAction() {
        $mailDescription = $this->getRequest ()->getParam ( 'maildescription' );
        $mailSubject = $this->getRequest ()->getParam ( 'mailsubject' );
        $mailIds = $this->getRequest ()->getParam ( 'mailids' );
        
        /**
         * Get and assign the values like admin email id, to mail id, to name from admin store configuration
         * if so display error message to selecte atleast one seller
         */
        $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        /**
         * Dyanamically replace the retrieved values in email template
         */
        $emailTemplateForSeller = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_massemail_from_admin_to_seller' );
        $emailTemplateForSeller->setSenderName ( ucwords ( $toName ) );
        $emailTemplateForSeller->setSenderEmail ( $toMailId );
        $emailTemplateForSeller->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        $emailTemplateSellerVariables = (array (
        'subject_message' => $mailSubject,
        'domainname' => Mage::getBaseUrl (Mage_Core_Model_Store::URL_TYPE_WEB),
                'customermessage' => ucwords ( $mailDescription ) 
        ));
        $emailTemplateForSeller->getProcessedTemplate ( $emailTemplateSellerVariables );
        /**
         * Email sending function for the dynamically replaced values
         */
        foreach ( $mailIds as $mailid ) {
            $customerData = Mage::getModel ( 'customer/customer' )->load ( $mailid )->getData ();
            $emailTemplateForSeller->send ( $customerData ['email'], $customerData ['firstname'], $emailTemplateSellerVariables );
        }
        $successMsg = Mage::helper ( 'adminhtml' )->__ ( "Mail was sent successfully to the total of  %d mail id's", count ( $mailIds ) );
        Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( $successMsg );
        $this->_redirect ( '*/*/index' );
    }
    
    /**
     * Edit seller data
     *
     * @return void
     */
    public function newAction() {
        $this->_forward ( 'edit' );
    }
    /**
     * Resolved acl issue while adding secure batch or magento 1.9.2
     */
    protected function _isAllowed() {
        return true;
    }
} 