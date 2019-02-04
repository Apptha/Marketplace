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
 * This class initiates the seller review model
 *
 */
class Apptha_Marketplace_Model_Sellerreview extends Mage_Core_Model_Abstract {
    /**
     * model:sellerreview
     * {@inheritDoc}
     * @see Varien_Object::_construct()
     */
    public function _construct() {
        parent::_construct ();
        $this->_init ( 'marketplace/sellerreview' );
    }
    /**
     * Function to save a seller review
     *@params array
     * @return void
     */
    
    function saveReview($data) {
        $needAdmin = Mage::getStoreConfig ( 'marketplace/seller_review/need_approval' );
        if ($data) {
            $storeId = Mage::app ()->getStore ()->getId ();
            $collection = Mage::getModel ( 'marketplace/sellerreview' );
            $collection->setSellerId ( $data ['seller_id'] );
            $collection->setProductId ( $data ['product_id'] );
            $collection->setCustomerId ( $data ['customer_id'] );
            $collection->setRating ( $data ['rating'] );
            $collection->setReview ( $data ['feedback'] );
            $collection->setStoreId ( $storeId );
            if ($needAdmin == 1) {
                $collection->setStatus ( 0 );
            } else {
                $collection->setStatus ( 1 );
            }
            $collection->save ();
            if ($needAdmin == 1) {
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/seller_review/admin_notify_review' );
            } else {
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/seller_review/notify_new_review' );
            }
            $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
            $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
            $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
            if ($templateId) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
            } else {
                if ($needAdmin == 1) {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_seller_review_admin_notify_review' );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_seller_review_notify_new_review' );
                }
            }
            $adminurl = Mage::helper ( 'adminhtml' )->getUrl ( 'marketplaceadmin/adminhtml_sellerreview/index' );
            $customer = Mage::getModel ( 'customer/customer' )->load ( $data ['customer_id'] );
            $customerName = $customer->getName ();
            $customerEmail = $customer->getEmail ();
            $emailTemplate->setSenderEmail ( $customerEmail );
            $emailTemplate->setSenderName ( ucwords ( $customerName ) );
            $emailTemplate->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            $emailTemplateVariables = (array (
                    'ownername' => ucwords ( $toName ),
                    'cname' => ucwords ( $customerName ),
                    'cemail' => $customerEmail,
                    'adminurl' => $adminurl 
            ));
            $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
            $emailTemplate->send ( $toMailId, ucwords ( $toName ), $emailTemplateVariables );
            return true;
        } else {
            return false;
        }
    }
    /**
     * Function to check customer already review for this product or not
     *
     * Passed the customer id as $customerId to get particular seller reviews
     *
     * @param int $customerId
     *            Passed the seller id as $id to get particular seller reviews
     * @param int $id
     *            Passed the product id as $productId to get particular seller reviews
     * @param int $productId
     *            Return count of total reviews
     * @return int
     *
     */
    
    function checkReview($customerId, $id, $productId) {
        $storeId = Mage::app ()->getStore ()->getId ();
        $coreResource = Mage::getSingleton ( 'core/resource' );
        $connection = $coreResource->getConnection ( 'core_read' );
        $table = $coreResource->getTableName ( 'marketplace/sellerreview' );
        $select = $connection->select ()->from ( array (
                'p' => $table 
        ), new Zend_Db_Expr ( 'seller_review_id' ) )->where ( 'seller_id = ?', $id )->where ( 'customer_id = ?', $customerId )->where ( 'product_id = ?', $productId )->where ( 'status = ?', 1 )->where ( 'store_id = ?', $storeId );
        return $connection->fetchOne ( $select );
    }
    /**
     * Function to display seller recent review
     *
     * Passed the seller id as $id to get particular seller reviews
     *
     * @param int $id
     *            Return reviews collection as array
     * @return array
     */
    
    function displayReview($id) {
        $storeId = Mage::app ()->getStore ()->getId ();
        return Mage::getModel ( 'marketplace/sellerreview' )->getCollection ()->addFieldToFilter ( 'status', 1 )->addFieldToFilter ( 'store_id', $storeId )->addFieldToFilter ( 'seller_id', $id )->setOrder ( 'created_at', 'DESC' )->setPageSize ( 5 );
    }
    /**
     * Function to get seller store name
     *
     * Passed the seller id as $id to get particular seller information
     *
     * @param int $id
     *            Return seller store name
     * @return string
     */
    
    function getSellerInfo($id) {
        $collection = Mage::getModel('marketplace/sellerprofile')->getCollection ()->addFieldToFilter ( 'seller_id', $id );
        foreach ( $collection as $data ) {
            return $data ['store_title'];
        }
    }
    /**
     * Function to get seller profile url of particular seller
     *
     * Passed the seller id as $id to get particular seller profile url
     *
     * @param int $id
     *            Return seller store url
     * @return string
     */
    
    function backUrl($id) {
        $sellerData = Mage::getModel ( 'marketplace/sellerreview' )->getSellerInfo ( $id );
        if ($sellerData) {
            $targetPath = 'marketplace/seller/displayseller/id/' . $id;
            /**
             * Load by target path
             * @var url
             */
            $mainUrlRewrite = Mage::getModel ( 'core/url_rewrite' )->load ( $targetPath, 'target_path' );
            $getRequestPath = $mainUrlRewrite->getRequestPath ();
            return Mage::getUrl ( $getRequestPath );
        }
    }
} 
