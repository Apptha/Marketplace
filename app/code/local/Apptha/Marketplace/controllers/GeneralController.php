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
 * This file contains General functionality
 */
class Apptha_Marketplace_GeneralController extends Mage_Core_Controller_Front_Action {
   /**
     * Function to display change buyer into seller form
     *
     * change buyer in to seller form
     *
     * @return void
     */
    function changebuyerAction() {
        /**
         * To check customer logged in or not
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'customer/account/login' );
            return;
        } else {
            /**
             * Getting customer status
             */
            $customerStatus = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
            /**
             * Getting Group id
             */
            $getGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
            /**
             * Get Customer
             */
            $customer = Mage::getSingleton ( "customer/session" )->getCustomer ();
            /**
             * Getting Customer Group Id
             */
            $customerGroupId = $customer->getGroupId ();
            if ($customerStatus == 1 && $getGroupId == $customerGroupId) {
                $this->_redirect ( 'marketplace/seller/dashboard' );
                return true;
            }
        }
        /**
         * To load and render Layout
         */
        
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * Function to change buyer into seller
     *
     * convert and change group id from buyer into seller
     *
     * @return void
     */
    function becomesellerAction() {
        /**
         * Getting store config for admin approval
         */
        $adminApproval = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/need_approval' );
        $approval = 0;
        if ($adminApproval == 1) {
            $approval = 0;
        } else {
            $approval = 1;
        }
        /**
         * Getting group id
         */
        $getGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
        /**
         * Get Customer
         */
        $customer = Mage::getSingleton ( "customer/session" )->getCustomer ();
        /**
         * Save Group Id
         */
        $customer->setGroupId ( $getGroupId )->save ();
        /**
         * getting customer Id
         */
        $customerId = $customer->getId ();
        /**
         * To load based on customer id
         */
        $model = Mage::getModel ( 'customer/customer' )->load ( $customerId );
        /**
         * Approval save
         */
        $model->setCustomerstatus ( $approval )->save ();
        Mage::getModel ( 'marketplace/sellerprofile' )->adminApproval ( $customerId );
        /**
         * Setting Session Message
         */
        if ($adminApproval == 1) {
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
        } else {
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Thank you for registering with %s.', Mage::app ()->getStore ()->getFrontendName () ) );
        }
        $this->_redirect ( 'customer/account' );
    }
    
    /**
     * Function to display vacation mode to seller
     *
     * Display vacation mode page
     *
     * @return void
     */
    function vacationmodeAction() {
        /**
         * To load and render layout
         */
        $this->loadLayout ();
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Vacation Mode' ) );
        $this->renderLayout ();
    }
    
    /**
     * Function to save vacation mode to seller
     *
     * Display vacation mode save page
     *
     * @return void
     */
    function vacationmodesaveAction() {
        /**
         * Get parameter values vacation status
         * and get vacation message
         * and get disable products
         * and get date from
         * and get date to
         */
        $vacationStatus = $this->getRequest ()->getParam ( 'vacation_status' );
        $vacationMessage = $this->getRequest ()->getParam ( 'vacation_message' );
        $disableProducts = $this->getRequest ()->getParam ( 'disable_products' );
        $dateFrom = $this->getRequest ()->getParam ( 'date_from' );
        $dateTo = $this->getRequest ()->getParam ( 'date_to' );
        /**
         * Get current date
         */
        $currentDate = Mage::getModel ( 'core/date' )->date ( 'Y-m-d' );
        
        /**
         * Check login status
         */
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            
            /**
             * Get seller id.
             */
            $seller = Mage::getSingleton ( 'customer/session' )->getCustomer ();
            $sellerId = $seller->getId ();
            
            /**
             * Collect product collection
             * and get products id
             */
            $product = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $sellerId );
            $productId = array ();
            foreach ( $product as $_product ) {
                $productId [] = $_product->getId ();
            }
            /**
             * Collect seller vaction mode for seller.
             * aGet seller id.
             */
            $sellerInfo = Mage::getModel ( 'marketplace/vacationmode' )->load ( $sellerId, 'seller_id' );
            $getId = $sellerInfo->getId ();
            
            if ($getId) {
                /**
                 * Set vacation message
                 * and set vacation status
                 * and set from date
                 * and set to date
                 * and set seller id
                 */
                $updateExisting = Mage::getModel ( 'marketplace/vacationmode' )->load ( $sellerId, 'seller_id' );
                $updateExisting->setVacationMessage ( $vacationMessage );
                $updateExisting->setVacationStatus ( $vacationStatus );
                if (strtotime ( $dateTo ) >= strtotime ( $currentDate ) && ($vacationStatus==0)) {
                    $updateExisting->setProductDisabled ( $disableProducts );
                }
                $updateExisting->setDateFrom ( $dateFrom );
                $updateExisting->setDateTo ( $dateTo );
                $updateExisting->setSellerId ( $sellerId );
                $updateExisting->save ();
            } else {
                /**
                 * If vaction mode is not exist previousle create a new one.
                 */
                $insertNew = Mage::getModel ( 'marketplace/vacationmode' );
                $insertNew->setVacationMessage ( $vacationMessage );
                $insertNew->setVacationStatus ( $vacationStatus );
                if (strtotime ( $dateTo ) >= strtotime ( $currentDate )) {
                    $insertNew->setProductDisabled ( $disableProducts );
                }
                $insertNew->setDateFrom ( $dateFrom );
                $insertNew->setDateTo ( $dateTo );
                $insertNew->setSellerId ( $sellerId );
                $insertNew->save ();
            }
            /**
             * Set current store
             */
            Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
            
            Mage::helper ( 'marketplace/product' )->changevacationmode ( $vacationStatus, $disableProducts, $productId, $dateTo, $currentDate );
            
            /**
             * Success message after save the vacation mode.
             */
            
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your vacation mode information is saved successfully' ) );
            
            /**
             * Redirect page after success the vacation mode.
             */
            $this->_redirect ( 'marketplace/general/vacationmode' );
            return true;
        }
    }
    
    /**
     * Function for ajax compare action
     * 
     * @params product
     * @return void
     */
    public function ajaxCompareAction() {
    $response = array ();
        /**
         * Get product id.
         */
        if ($productId = ( int ) $this->getRequest ()->getParam ( 'product' )) {
            /**
             * Collect product information.
             */
            $product = Mage::getModel ( 'catalog/product' )->setStoreId ( Mage::app ()->getStore ()->getId () )->load ( $productId );
            
            if ($product->getId ()) {
                /**
                 * If product is added to compare list.
                 * Then show the success message
                 */
                Mage::getSingleton ( 'catalog/product_compare_list' )->addProduct ( $product );
                $response ['status'] = 'SUCCESS';
                $response ['message'] = $this->__ ( 'The product %s has been added to comparison list.', Mage::helper ( 'core' )->escapeHtml ( $product->getName () ) );
                Mage::register ( 'referrer_url', $this->_getRefererUrl () );
                Mage::helper ( 'catalog/product_compare' )->calculate ();
                /**
                 * Execute the compare add product event.
                 */
                Mage::dispatchEvent ( 'catalog_product_compare_add_product', array (
                        'product' => $product 
                ) );
                /**
                 * Loads sidebar block for compare products
                 */
                $this->loadLayout ();
                $sidebar_block = $this->getLayout ()->getBlock ( 'catalog.compare.sidebar' );
                $sidebar_block->setTemplate ( 'catalog/product/compare/sidebar.phtml' );
                $sidebar = $sidebar_block->toHtml ();
                $response ['sidebar'] = $sidebar;
            }
        }
        $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $response ) );
        return;
    }
    
    /**
     * Function to remove the compared products
     * @params product
     * @return void
     */
    public function removeAction() {
        $response = array ();
        /**
         * Get product id.
         */
        $productId = ( int ) $this->getRequest ()->getParam ( 'product' );
        if ($productId) {
            /**
             * Collect product information
             */
            $product = Mage::getModel ( 'catalog/product' )->setStoreId ( Mage::app ()->getStore ()->getId () )->load ( $productId );
            
            if ($product->getId ()) {
                $item = Mage::getModel ( 'catalog/product_compare_item' );
                $item->loadByProduct ( $product );
                if ($item->getId ()) {
                    $item->delete ();
                    /**
                     * Item is deleted successfully from compare list
                     * then show the success message.
                     */
                    $response ['status'] = 'SUCCESS';
                    $response ['message'] = $this->__ ( 'The product %s has been removed from comparison list.', $product->getName () );
                    Mage::register ( 'referrer_url', $this->_getRefererUrl () );
                    Mage::helper ( 'catalog/product_compare' )->calculate ();
                    Mage::dispatchEvent ( 'catalog_product_compare_remove_product', array (
                            'product' => $item 
                    ) );
                    /**
                     * Loads sidebar block for compare products
                     */
                    $this->loadLayout ();
                    $sidebar_block = $this->getLayout ()->getBlock ( 'catalog.compare.sidebar' );
                    $sidebar_block->setTemplate ( 'catalog/product/compare/sidebar.phtml' );
                    $sidebar = $sidebar_block->toHtml ();
                    $response ['sidebar'] = $sidebar;
                }
            }
        }
        $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $response ) );
        return;
    }
    /**
     * Function to clear the compare products totally
     * @params product
     * @return void
     */
    public function clearAction() {
        $resource = Mage::getSingleton ( 'core/resource' );
        $write = $resource->getConnection ( 'core_write' );
        $query = 'TRUNCATE TABLE ' . $resource->getTableName ( 'catalog_compare_item' );
        $write->query ( $query );
        
        $response = array ();
        
        Mage::register ( 'referrer_url', $this->_getRefererUrl () );
        Mage::helper ( 'catalog/product_compare' )->calculate ();
        /**
         * Loads sidebar block for compare products
         */
        $this->loadLayout ();
        $sidebar_block = $this->getLayout ()->getBlock ( 'catalog.compare.sidebar' );
        $sidebar_block->setTemplate ( 'catalog/product/compare/sidebar.phtml' );
        $sidebar = $sidebar_block->toHtml ();
        $response ['sidebar'] = $sidebar;
        
        $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $response ) );
        return;
    }
    /**
     * Function to load facevook store configuration
     * @return void|boolean
     */
    public function facebookstoreAction(){
    
    if (Mage::getStoreConfig('facebookstore/faceconnect/enabled') == 1) {
    $this->loadLayout ();
    $this->renderLayout ();
    }else{
    $this->_redirect ( 'marketplace/seller/dashboard' );
    return;
    
    }
    
    }
    /**
     * Function to save facebook Store to seller
     *
     * Display facebook Store save page
     * @params customerid,banner eanble,banner selected,
     * facebookenable,appid,canvas,secret
     * @return void
     */
    function facebookstoreconfigAction() {
		$sellerId = $this->getRequest ()->getPost ( 'customerId' );
		$enableBanner = $this->getRequest ()->getPost ( 'banner_enable' );
		$bannerSelected = $this->getRequest ()->getPost ( 'banner_selected' );
		$enableFacebook = $this->getRequest ()->getPost ( 'facebook_enable' );
		$facebookAppId = $this->getRequest ()->getPost ( 'appId' );
		$facebookAppCanvas = $this->getRequest ()->getPost ( 'appCanvas' );
		$facebookAppSecret = $this->getRequest ()->getPost ( 'appSecret' );
		$sellerInfo = Mage::getModel ( 'marketplace/facebookstore' )->load ( $sellerId, 'seller_id' );
		$getId = $sellerInfo->getId ();
		if (! empty ( $_FILES ['banner_image'] ['name'] )) {
			$uploader = new Varien_File_Uploader ( 'banner_image' );
			$uploader->setAllowedExtensions ( array (
					'jpg',
					'png',
					'jpeg',
					'gif' 
			) );
			$uploader->setAllowRenameFiles ( true );
			$uploader->setFilesDispersion ( false );
			$path = Mage::getBaseDir ( 'media' ).DS.'banner_images'.DS.'default';
			$fileName = $_FILES ['banner_image'] ['name'];
			$uploader->save ( $path, $fileName );
		}
		if ($getId) {
		
			$updateExisting = Mage::getModel ( 'marketplace/facebookstore' )->load ( $sellerId, 'seller_id' );
			
			$updateExisting->setBannerEnable ( $enableBanner );
			$updateExisting->setBannerSelected ( $bannerSelected );
			if (! empty ( $fileName )) {
				$updateExisting->setBannername ( $fileName );
			}
			$updateExisting->setFacebookEnable ( $enableFacebook );
			$updateExisting->setFacebookAppid ( $facebookAppId );
			$updateExisting->setFacebookAppcanvas ( $facebookAppCanvas );
			$updateExisting->setFacebookAppsecret ( $facebookAppSecret );
			$updateExisting->save ();
		} else {
			
			$insertNew = Mage::getModel ( 'marketplace/facebookstore' );
			$insertNew->setSellerId ( $sellerId );
			$insertNew->setBannerEnable ( $enableBanner );
			$insertNew->setBannerSelected ( $bannerSelected );
			if (! empty ( $fileName )) {
				$insertNew->setBannername ( $fileName );
			}
			$insertNew->setFacebookEnable ( $enableFacebook );
			$insertNew->setFacebookAppid ( $facebookAppId );
			$insertNew->setFacebookAppcanvas ( $facebookAppCanvas );
			$insertNew->setFacebookAppsecret ( $facebookAppSecret );
			$insertNew->save ();
		}
		Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your Facebook Store information is saved successfully' ) );
		$this->_redirect ( 'marketplace/general/facebookstore' );
		return true;
	}
	/**
	 * Function for Disapproved Sellers
	 * @return void
	 */
	public function disapprovedSellerAction(){
		$customerStatus = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
		/**
		 * Getting Group id
		 */
		$getGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
		/**
		 * Get Customer
		 */
		$customer = Mage::getSingleton ( "customer/session" )->getCustomer ();
		/**
		 * Getting Customer Group Id
		 */
		$customerGroupId = $customer->getGroupId ();
		/**
		 * To load and render Layout
		 */
		if ($customerStatus == 1 && $getGroupId == $customerGroupId) {
			$this->_redirect ( 'marketplace/seller/dashboard' );
			return true;
		}
		
		$this->loadLayout ();
		$this->renderLayout ();
	}
}