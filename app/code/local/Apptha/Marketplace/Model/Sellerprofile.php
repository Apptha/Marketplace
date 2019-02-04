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
 * This class initiates the seller profile model
 *
 */
class Apptha_Marketplace_Model_Sellerprofile extends Mage_Core_Model_Abstract {
/**
 * model:sellerprofile
 * {@inheritDoc}
 * @see Varien_Object::_construct()
 */
    public function _construct() {
        parent::_construct ();
        $this->_init ( 'marketplace/sellerprofile' );
    }
    
    /**
     * Function to approve or disapprove seller
     *
     * Passed the customer id of the seller
     *
     * @param int $customerId            
     *
     * @return void
     */
    function adminApproval($customerId) {
        $adminApproval = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/need_approval' );
        if ($adminApproval == 1) {
            $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/email_template_selection' );
        } else {
            $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/new_seller_template' );
        }
        $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        if ($templateId) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
        } else {
            if ($adminApproval == 1) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_email_template_selection' );
            } else {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_new_seller_template' );
            }
        } $adminurl = Mage::helper ( 'adminhtml' )->getUrl ( 'marketplaceadmin/adminhtml_manageseller/index' );
        /**
         * Send Email for seller when they approved
         * @var id
         */
        
        $customer = Mage::getModel ( 'customer/customer' )->load ( $customerId );
        $cemail = $customer->getEmail ();
        $cname = $customer->getName ();
        $emailTemplate->setSenderName ( ucwords ( $cname ) );
        $emailTemplate->setSenderEmail ( $cemail );
        $emailTemplateVariables = (array ( 'ownername' => ucwords ( $toName ),
                'cname' => ucwords ( $cname ),'cemail' => $cemail,
                'adminurl' => $adminurl));
        $emailTemplate->setDesignConfig ( array ('area' => 'frontend') );
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        $emailTemplate->send ( $toMailId, ucwords ( $toName ), $emailTemplateVariables );
    }
    
    /**
     * Function to get seller profile info
     *
     * Passed the customer id of the seller
     *
     * @param int $id
     *            Return seller information as array
     * @return array
     */
    function collectprofile($id) {
        return Mage::getModel ( 'marketplace/sellerprofile' )->load ( $id, 'seller_id' );
    }
    /**
     * Function to display new products
     *
     * Passed the seller id of the seller
     *
     * @param int $sellerid
     *            Return new products as array
     * @return array
     */
    function newproduct($sellerid) {
        $storeId = Mage::app ()->getStore ()->getId ();
        $todayDate = Mage::app ()->getLocale ()->date ()->toString ( Varien_Date::DATETIME_INTERNAL_FORMAT );
        return Mage::getModel ( 'catalog/product' )->getCollection ()->addStoreFilter ( $storeId )->addAttributeToSelect ( '*' )->addFieldToFilter ( 'seller_id', $sellerid )->addFieldToFilter ( 'status', 1 )->addAttributeToFilter ( 'news_from_date', array (
                'date' => true,
                'to' => $todayDate 
        ) )->addAttributeToFilter ( 'news_to_date', array (
                'or' => array (
                        0 => array (
                                'date' => true,
                                'from' => $todayDate 
                        ),
                        1 => array (
                                'is' => new Zend_Db_Expr ( 'null' ) 
                        ) 
                ) 
        ), 'left' )->addAttributeToSort ( 'entity_id', 'DESC' )->setPage ( 1, 5 );
    }
    /**
     * Function to display popular products
     *
     * Passed the seller id of the seller
     *
     * @param int $sellerid
     *            Return popular products as array
     * @return array
     */
    function popularproduct($sellerid) {
        return Mage::getResourceModel ( 'reports/product_collection' )->addOrderedQty ()->addFieldToFilter ( 'seller_id', $sellerid )->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'status', array (
                'eq' => 1 
        ) )->setPage ( 1, 5 );
    }
    /**
     * Function for url management
     *
     * Passed the seller id of the seller
     *
     * @param int $sellerId
     *            Passed the seller store name to create url
     * @param int $storeName
     *            Return popular products as array
     * @return array
     */
    function addRewriteUrl($storeName, $sellerId) {
        $trimStr = trim ( preg_replace ( '/[^a-z0-9-]+/', '-', strtolower ( $storeName ) ), '-' );
        $mainUrlRewrite = Mage::getModel ( 'core/url_rewrite' )->load ( $trimStr, 'request_path' );
        $getUrlRewriteId = $mainUrlRewrite->getUrlRewriteId ();
        if ($getUrlRewriteId) {
            $requestPath = $trimStr . '-' . $sellerId;
        } else {
            $requestPath = $trimStr;
        }
        Mage::getModel ( 'core/url_rewrite' )->setIsSystem ( 0 )->setIdPath ( 'seller/' . $sellerId )->setTargetPath ( 'marketplace/seller/displayseller/id/' . $sellerId )->setRequestPath ( $requestPath )->save ();
    }
    /**
     * Function to get seller product info
     *
     * Passed the seller id
     * @param int $id
     * Return seller product information as array
     * @return array
     */
    function sellerProduct($id) {
        return Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'seller_id', $id );
    }
    /**
     * Function to get seller product info
     *
     * Passed the seller id
     *
     * @param int $id
     *            Return seller product information as array
     * @return array
     */
    function getTodayOrder($id) {
        $currentDate = Mage::getModel ( 'core/date' )->date ( 'Y-m-d' );
        $yesterDay = date ( 'Y-m-d', strtotime ( '+1 day', strtotime ( $currentDate ) ) );
        return Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'seller_id', $id )->addFieldToFilter ( 'created_at', array (
                'from' => $currentDate,
                'to' => $yesterDay 
        ) );
    }
    /**
     * Function to Yesterday order
     *
     * Passed the seller id
     *
     * @param int $id
     *            Return seller product information as array
     * @return array
     */
    function getYesterdayOrder($id) {
        $currentDate = Mage::getModel ( 'core/date' )->date ( 'Y-m-d' );
        $yesterDay = date ( 'Y-m-d', strtotime ( '+1 day', strtotime ( $currentDate ) ) );
        $dayBefore = date ( 'Y-m-d', strtotime ( '+1 day', strtotime ( $yesterDay ) ) );
        return Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'seller_id', $id )->addFieldToFilter ( 'created_at', array (
                'from' => $dayBefore,
                'to' => $yesterDay 
        ) );
    }
    /**
     * Get Last week income
     * Passed the seller id
     *
     * @param int $id
     *            Return seller product information as array
     * @return array
     *
     */
    function getLastWeekIncome($id) {
        $to = date ( 'd-m-Y' );
        $toDay = date ( 'l', strtotime ( $to ) );
        /**
         * if today is monday, take last monday
         */
        if ($toDay == 'Monday') {
            $startDay = strtotime ( "-1 monday midnight" );
            $endDay = strtotime ( "yesterday" );
        } else {
            $startDay = strtotime ( "-2 monday midnight" );
            $endDay = strtotime ( "-1 sunday midnight" );
        }
        $from = date ( 'Y-m-d', $startDay );
        $to = date ( 'Y-m-d', $endDay );
        $to = date ( 'Y-m-d', strtotime ( $to . ' + 1 day' ) );
        $fromDisplay = $from;
        $toDisplay = date ( 'Y-m-d', $endDay );
        return Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'seller_id', $id )->addFieldToFilter ( 'created_at', array (
                'from' => $fromDisplay,
                'to' => $toDisplay 
        ) );
    }
    /**
     * Get Last month income
     * Passed the seller id
     *
     * @param int $id
     *            Return seller product information as array
     * @return array
     *
     */
    function getLastMonthIncome($id) {
        $from = date ( 'Y-m-01', strtotime ( 'last month' ) );
        $to = date ( 'Y-m-t', strtotime ( 'last month' ) );
        $to = date ( 'Y-m-d', strtotime ( $to . ' + 1 day' ) );
        $fromDisplay = $from;
        $toDisplay = date ( 'Y-m-t', strtotime ( 'last month' ) );
        return Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'seller_id', $id )->addFieldToFilter ( 'created_at', array (
                'from' => $fromDisplay,
                'to' => $toDisplay 
        ) );
    }
    /**
     * Get Last year income
     * Passed the seller id
     *
     * @param int $id
     *            Return seller product information as array
     * @return array
     */
    function getLastYearIncome($id) {
        $startDate = date ( 'm-01-Y 00:00:00', strtotime ( 'last year' ) );
        $endDate = date ( 'm-t-Y 12:59:59', strtotime ( 'last year' ) );
        return Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'seller_id', $id )->addFieldToFilter ( 'created_at', array (
                'from' => $startDate,
                'to' => $endDate 
        ) );
    }
    /**
     * Function to get seller product order info
     *
     * Passed the seller id
     *
     * @param int $id
     *            Return seller product order information as array
     * @return array
     */
    function getdataProduct($orderIds) {
        $items = Mage::getModel ( "sales/order_item" )->getCollection ()->addFieldToSelect ( 'product_id' )->addFieldToSelect ( 'order_id' )->addFieldToSelect ( 'name' )->addFieldToSelect ( 'qty_invoiced' )->addFieldToSelect ( 'qty_ordered' )->addFieldToSelect ( 'base_price' )->addAttributeToSort ( 'order_id', 'DESC' )->addFieldToFilter ( "order_id", array (
                "in" => $orderIds 
        ) );
        $items->getSelect ()->join ( array (
                't2' => Mage::getConfig ()->getTablePrefix () . 'sales_flat_order' 
        ), 'main_table.order_id = t2.entity_id', array (
                'increment_id' => 't2.increment_id' 
        ) );
        $items->getSelect ()->limit ( 5 );
        return $items;
    }
    /**
     * Function to display top seller
     *
     * Passed the seller id
     *
     * @param int $id
     *            Return seller information as array
     * @return array
     */
    function topSeller($id) {
        return Mage::getModel ( 'marketplace/sellerprofile' )->getCollection ()->addFieldToFilter('seller_id',array(
                $id 
        ) );
    }
    
    /**
     * Function to display approved seller
     *
     * Passed the seller id
     *
     * @param int $id
     *            Return seller information as array
     * @return array
     */
    function approvedSeller($id) {
        return Mage::getModel ( 'marketplace/sellerprofile' )->getCollection ()->addFieldToFilter('seller_id',array(
                $id
        ) )
        ->addFieldToFilter('store_title',array('notnull' => true));
        
    }
    
    /**
     * Get top sellers data
     *
     * @param array $id            
     * @return array $collection
     */
    function topSellers($id) {
        $collection = Mage::getModel ( 'marketplace/sellerprofile' )->getCollection ()->addFieldToFilter ( 'seller_id', array (
                $id 
        ) );
        $collection->getSelect ()->order ( 'rand()' );
        return $collection;
    }
    
    /**
     * function to approve or disapprove seller
     *
     * Passed the customer id of the seller
     *
     * @param int $customerId            
     *
     * @return void
     */
    function newSeller($customerId) {
        $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/new_seller_template' );
        $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
        if ($templateId) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
        } else {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_new_seller_template' );
        }
        $adminurl = Mage::helper ( 'adminhtml' )->getUrl ( 'marketplaceadmin/adminhtml_manageseller/index' );
        $customer = Mage::getModel ( 'customer/customer' )->load ( $customerId );
        $cemail = $customer->getEmail ();
        $cname = $customer->getName ();
        $emailTemplate->setSenderName ( ucwords ( $cname ) );
        $emailTemplate->setSenderEmail ( $cemail );
        $emailTemplateVariables = (array (
                'ownername' => ucwords ( $toName ),
                'cname' => ucwords ( $cname ),
                'cemail' => $cemail,
                'adminurl' => $adminurl 
        ));
        $emailTemplate->setDesignConfig ( array (
                'area' => 'frontend' 
        ) );
        $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
        $emailTemplate->send ( $toMailId, ucwords ( $toName ), $emailTemplateVariables );
    }
}