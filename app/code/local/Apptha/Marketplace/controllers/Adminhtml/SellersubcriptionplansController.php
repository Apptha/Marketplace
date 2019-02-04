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
 * Manage membership Controller
 *
 * This class contains the functions for editing, adding and listing of memberships.
 */
class Apptha_Marketplace_Adminhtml_SellersubcriptionplansController extends Mage_Adminhtml_Controller_Action {
    
    /**
     * Define repeted string variables
     */
    public $strTestManager = 'test Manager';
    public $strMarketplaceSubscribedinfo = 'marketplace/subscribedinfo';
    public $strAdminhtmlSession = 'adminhtml/session';
    public $strCoreDate = 'core/date';
    /**
     * Initialize listing of installation types.
     *
     * @return string.
     */
    protected function _initAction() {
        $this->loadLayout ()->_setActiveMenu ( 'marketplace/set_time' )->_addBreadcrumb ( 'test Manager', 'test Manager' );
        return $this;
    }
    
    /**
     * Initilize and render layout.
     */
    public function indexAction() {
        $this->_initAction ();
        $this->renderLayout ();
    }
    /**
     * Perform editing installation types.
     */
    public function editAction() {
        $testId = $this->getRequest ()->getParam ( 'id' );
        $testModel = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $testId );
        if ($testModel->getId () || $testId == 0) {
            
            Mage::register ( 'Careers_data', $testModel );
            /**
             *load and render layout
             **/ 
            $this->loadLayout ();
            $this->_addBreadcrumb ( 'test Manager', 'test Manager' );
            $this->_addBreadcrumb ( 'Test Description', 'Test Description' );
            $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
            $this->_addContent ( $this->getLayout ()->createBlock ( 'marketplace/adminhtml_sellersubscriptionplans_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'installer/adminhtml_careers_edit_tabs' ) );
            
            $this->renderLayout ();
        } else {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( 'Test does not exist' );
            $this->_redirect ( '*/*/' );
        }
    }
    
    /**
     * Add new installer type - forwarded to editAction.
     */
    public function newAction() {
        $this->_forward ( 'edit' );
    }
    
    /**
     * Save installation types.
     *
     * @return void;
     */
    public function saveAction() {
        /**
         * Check for POST data.
         */
        if ($this->getRequest ()->getPost ()) {
            try {
                $postData = $this->getRequest ()->getPost ();
                $testModel = Mage::getModel ( 'marketplace/subscribedinfo' );
                if ($this->getRequest ()->getParam ( 'id' ) <= 0) {
                    $testModel->setCreatedTime ( Mage::getSingleton ( 'core/date' )->gmtDate () );
                }
                /**
                 * Save the post data.
                 */
                $testModel->addData ( $postData )->setUpdateTime ( Mage::getSingleton ( 'core/date' )->gmtDate () )->setId ( $this->getRequest ()->getParam ( 'id' ) )->save ();
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( 'successfully saved' );
                Mage::getSingleton ( 'adminhtml/session' )->settestData ( false );
                $this->_redirect ( '*/*/' );
                return;
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                Mage::getSingleton ( 'adminhtml/session' )->settestData ( $this->getRequest ()->getPost () );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
                return;
            }
        }
        $this->_redirect ( '*/*/' );
    }
    
    /**
     * Perform delete Action.
     */
    public function deleteAction() {
        /* Check for the delete ID */
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                $testModel = Mage::getModel ( 'marketplace/subscribedinfo' );
                $testModel->setId ( $this->getRequest ()->getParam ( 'id' ) )->delete ();
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( 'successfully deleted' );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
            }
        }
        $this->_redirect ( '*/*/' );
    }
    
    /**
     * Method to approve the upgradation plan
     *
     * @return void
     *
     *
     */
    public function massApproveAction() {
$planIds = $this->getRequest ()->getParam ( 'planid' );

if (! is_array ( $planIds )) {
Mage::getSingleton ( 'adminhtml/session' )->addError ( $this->__ ( 'Please select seller(s)' ) );
} else {
try {
$adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
$adminName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
$subscriptionPlantable = Mage::getSingleton ( "core/resource" )->getTableName ( 'marketplace_subscriptionplans' );
/**
 * Status 1- approved
 */
/**
 * Status 2- Need admin approval
 */
array_filter ( $planIds );
foreach ( $planIds as $Id ) {
$subscribedInfo = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $Id );
$payPalverify = $subscribedInfo->getPaypalVerify ();
$planIdTos = explode ( '_', $payPalverify );
$planIdTo = $planIdTos [1];
Mage::getSingleton ( 'marketplace/subscribedinfo' )->load ( $Id )->setAdminApproval ( '1' )->setsetUpgradeTo ( '' )->setStatus ( 'pending' )->setPlanId ( $planIdTo )->setUpgradeTo ( '' )->setPaidDate ( Mage::getModel ( 'core/date' )->date ( 'Y-m-d H:i:s' ) )->setIsMassupdate ( true )->save ();
/**
 * get Subscription collection
 * 
 * @var unknown
 */
$collection = Mage::getModel ( 'marketplace/subscribedinfo' )->getCollection ();
$collection->getSelect ()->join ( array (
't2' => $subscriptionPlantable 
), 'main_table.plan_id = t2.plan_id', 't2.plan_name' );
$collection->addFieldToFilter ( 'main_table.id', $Id );
$subscriptionDetails = $collection->getData ();
$planName = $subscriptionDetails ['0'] ['plan_name'];
$planAmount = $subscriptionDetails ['0'] ['yearly_fee'];
$sellerId = $subscriptionDetails ['0'] ['seller_id'];
$invoiceId = $subscriptionDetails ['0'] ['invoice_id'];
$subscriptionStartDate = Mage::helper ( 'marketplace/marketplace' )->subscribedDate ( $sellerId );
$subscriptionEndDate = Mage::helper ( 'marketplace/marketplace' )->subscriptionEndDate ( $sellerId );
$subscriptionEndDate = date ( "F j, Y", strtotime ( $subscriptionEndDate ) );
$custmerData = Mage::getModel ( 'customer/customer' )->load ( $sellerId )->getData ();
$emailReceiver = $custmerData ['email'];
$firstnameReceiver = $custmerData ['firstname'] . $custmerData ['lastname'];
$templateId = ( int ) Mage::getStoreConfig ( 'marketplace/subscription/admin_upgradation_seller_notify' );
/**
 * if it is user template then this process is continue
 */
if ($templateId) {
$emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
} else {
/**
 * we are calling default template
 */
$emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_subscription_admin_upgradation_seller_notify' );
}
/**
 * Mail sender name
 */
$emailTemplate->setSenderName ( $adminName );
/**
 * Mail sender email id
 */
$emailTemplate->setSenderEmail ( $adminEmailId );
$emailTemplateVariables = (array (
'planname' => $planName,
'invoice' => $invoiceId,
'ownername' => $firstnameReceiver,
"status" => 'Approved',
'startdate' => $subscriptionStartDate,
'enddate' => $subscriptionEndDate,
'planamount' => $planAmount 
));
$emailTemplate->setDesignConfig ( array (
'area' => 'frontend' 
) );
/**
 * Send mail to customer email ids
 */
$emailTemplate->send ( $emailReceiver, $firstnameReceiver, $emailTemplateVariables );
}

$this->_getSession ()->addSuccess ( $this->__ ( 'Total of %d record(s) were successfully updated', count ( $planIds ) ) );
} catch ( Exception $e ) {
$this->_getSession ()->addError ( $e->getMessage () );
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
