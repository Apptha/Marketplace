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
 * Function written in this file are used for seller manipulation
 */
class Apptha_Marketplace_Model_Seller extends Mage_Core_Model_Abstract {
    
    /**
     * Check whether locin credentials exist or not
     *
     * @param
     *            $login
     * @return number $isLoginCredentials
     */
    public function isLoginCredentials($login) {
        $isLoginCredentials = 0;
        if (! empty ( $login ['username'] ) && ! empty ( $login ['password'] )) {
            $isLoginCredentials = 1;
        }
        return $isLoginCredentials;
    }
    
    /**
     * Set seller profile details
     *
     * @param array $collection            
     * @param array $data            
     * @param
     *            $storeLogo
     * @param
     *            $storeBanner
     * @return $collection
     */
    public function setSellerProfileDetails($collection, $data, $storeLogo, $storeBanner, $imagesPathLogo, $imagesPathBanner) {
        /**
         * check remove logo has been posted
         */
        $removeLogo = $removeBanner = $showProfile = '';
        if (isset ( $data ['remove_logo'] )) {
            $removeLogo = $data ['remove_logo'];
        }
        /**
         * check remove banner has been posted
         */
        if (isset ( $data ['remove_banner'] )) {
            $removeBanner = $data ['remove_banner'];
        }
        /**
         * check show profile has been posted
         */
        if (isset ( $data ['show_profile'] )) {
            $showProfile = $data ['show_profile'];
        }
        
        /**
         * check empty or not of storelogo
         */
        if (! empty ( $storeLogo )) {
            $collection->setStoreLogo ( $imagesPathLogo );
        }
        /**
         * check empty or not of storebanner
         */
        if (! empty ( $storeBanner )) {
            $collection->setStoreBanner ( $imagesPathBanner );
        }
        /**
         * remove logo
         */
        if ($removeLogo == 1) {
            $collection->setStoreLogo ( '' );
        }
        /**
         * remove banner
         */
        if ($removeBanner == 1) {
            $collection->setStoreBanner ( '' );
        }
        /**
         * show profile
         */
        if ($showProfile) {
            /**
             * Set show profile
             */
            $collection->setShowProfile ( $showProfile );
        } else {
            $collection->setShowProfile ( '' );
        }
        return $collection;
    }
    
    /**
     * Create directory for seller profile image store
     *
     * @param string $basedir            
     * @param object $file            
     * @return void
     */
    public function createDirecotryForSellerProfileImage($basedir, $value) {
        /**
         * New VARIEN tO file
         */
        $file = new Varien_Io_File ();
        /**
         * check for resized directory and create new one if its not exist already
         */
        if (! is_dir ( $basedir . '/sellerimage' ) && $value == 1) {
            /**
             * mkdir
             */
            $file->mkdir ( $basedir . '/sellerimage' );
        }
        if (! is_dir ( $basedir . '/marketplace/resized' ) && $value == 2) {
            $file->mkdir ( $basedir . '/marketplace/resized' );
        }
    }
    
    /**
     * Url rewrite for seller
     *
     * @param number $sellerId            
     * @param string $storeName            
     */
    public function urlRewriteForSeller($sellerId, $storeName) {
        /**
         * get Target PATH
         */
        $targetPath = 'marketplace/seller/displayseller/id/' . $sellerId;
        $mainUrlRewrite = Mage::getModel ( 'core/url_rewrite' )->load ( $targetPath, 'target_path' );
        $getRequestPath = $mainUrlRewrite->getRequestPath ();
        /**
         * Get Request Path
         */
        $newGetRequestPath = Mage::getUrl ( $getRequestPath );
        if ($newGetRequestPath == Mage::getBaseUrl ()) {
            Mage::getModel ( 'marketplace/sellerprofile' )->addRewriteUrl ( $storeName, $sellerId );
        }
    }
    
    /**
     * Save seller profile data
     *
     * @param number $sellerId            
     * @param string $storeName            
     * @param string $imagesPathLogo            
     * @param string $imagesPathBanner            
     * @param number $showProfile            
     * @param array $data            
     * @param string $storeLogo            
     * @param string $storeBanner            
     * @param number $flag            
     * @return void
     *
     */
    public function saveSellerProfileData($sellerId, $imageInfoForSeller, $showProfile, $data, $storeLogo, $storeBanner, $flag) {
        /**
         * Getting seller profile datas
         */
    $shippingCost = $nationalShippingCost = $internationalShippingCost = '';
        $storeName = $imageInfoForSeller ['store_name'];
        $imagesPathLogo = $imageInfoForSeller ['images_path_logo'];
        $imagesPathBanner = $imageInfoForSeller ['images_path_banner'];
        $state = $data ['state'];
        $country = $data ['country'];
        $description = $data ['description'];
        $metaKeyword = $data ['meta_keyword'];
        $metaDescription = $data ['meta_description'];
        $twitterId = $data ['twitter_id'];
        $facebookId = $data ['facebook_id'];
        $googleId = $data ['google_id'];
        $linkedId = $data ['linked_id'];
        $bankPayment = $data ['bank_payment'];
        $paypalId = $data ['paypal_id'];
        $contact = $data ['contact'];   
        if(isset($data ['shipping_cost'])){
        $shippingCost = $data ['shipping_cost'];
        }
        if(isset($data ['national_shipping_cost'])){
        $nationalShippingCost = $data ['national_shipping_cost'];
        }
        if(isset($data ['international_shipping_cost'])){
        $internationalShippingCost = $data ['international_shipping_cost'];
        }
        
        if ($flag == 1) {
            $collection = Mage::getModel ( 'marketplace/sellerprofile' )->load ( $sellerId, 'seller_id' );
            $collection = Mage::getModel ( 'marketplace/seller' )->setSellerProfileDetails ( $collection, $data, $storeLogo, $storeBanner, $imagesPathLogo, $imagesPathBanner );
        } else {
            $collection = Mage::getModel ( 'marketplace/sellerprofile' );
        }
        /**
         * Set Seller Profile Data
         */
        
        $collection->setSellerId ( $sellerId );
        $collection->setStoreTitle ( $storeName );
        $collection->setState ( $state );
        $collection->setCountry ( $country );
        $collection->setContact ( $contact );
        $collection->setDescription ( $description );
        $collection->setMetaKeyword ( $metaKeyword );
        $collection->setMetaDescription ( $metaDescription );
        $collection->setTwitterId ( $twitterId );
        $collection->setFacebookId ( $facebookId );
        $collection->setGoogleId ( $googleId );
        $collection->setLinkedId ( $linkedId );
        $collection->setBankPayment ( $bankPayment );
        $collection->setPaypalId ( $paypalId );
        $collection->setShippingCost ( $shippingCost );
        $collection->setNationalShippingCost ( $nationalShippingCost );
        $collection->setInternationalShippingCost ( $internationalShippingCost );
        
        if ($showProfile == 1) {
            $collection->setShowProfile ( 1 );
        } else {
            $collection->setShowProfile ( 0 );
        }
        
        if ($flag != 1) {
            $collection->setStoreLogo ( $imagesPathLogo );
            $collection->setStoreBanner ( $imagesPathBanner );
        }
        $collection->save ();
    }
    /**
     * save logo for seller
     *
     * @param array $filesDataArray            
     * @param string $basedir            
     * @return string $imagesPathLogo
     */
    public function saveLogoForSeller($filesDataArray, $basedir, $logo) {
        /**
         * check store logo has not been empty
         */
        $imagesPathLogoBanner = '';
        if (isset ( $filesDataArray [$logo] ['name'] ) && (file_exists ( $filesDataArray [$logo] ['tmp_name'] ))) {
            try {
                $uploader = new Varien_File_Uploader ( $filesDataArray [$logo] );
                /**
                 * Define the allowed file extension of uploading file
                 */
                $uploader->setAllowedExtensions ( array (
                        'jpg',
                        'jpeg',
                        'gif',
                        'png' 
                ) );
                $uploader->addValidateCallback ( 'catalog_product_image', Mage::helper ( 'catalog/image' ), 'validateUploadFile' );
                $uploader->setAllowRenameFiles ( true );
                $uploader->setFilesDispersion ( false );
                
                $path = $basedir . DS . 'sellerimage';
                /**
                 * upload the file to the images path
                 */
                $uploader->save ( $path, $filesDataArray [$logo] ['name'] );
                $imagesPathLogoBanner = $uploader->getUploadedFileName ();
            } catch ( Exception $e ) {
                /**
                 * Display error message for images upload
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
            }
            /**
             * check for resized directory and create new one if its not exist already
             */
            Mage::getModel ( 'marketplace/seller' )->createDirecotryForSellerProfileImage ( $basedir, 2 );
            /**
             * Define the logo and resized logo paths
             */
            
            if ($logo == 'store_logo') {
                $imageUrlLogo = Mage::getBaseDir ( 'media' ) . DS . 'sellerimage' . DS . $imagesPathLogoBanner;
                $imageResizedLogo = Mage::getBaseDir ( 'media' ) . DS . 'marketplace' . DS . 'resized' . DS . $imagesPathLogoBanner;
            } else {
            $imageResizedLogo = Mage::getBaseDir ( 'media' ) . DS . 'marketplace' . DS . 'resized' . DS . $imagesPathLogoBanner;
                $imageUrlLogo = Mage::getBaseDir ( 'media' ) . DS . 'sellerimage' . DS . $imagesPathLogoBanner;                
            }
            
            /**
             * check the file is in logo path and not in resized logo path
             */
            if (file_exists ( $imageUrlLogo ) && ! file_exists ( $imageResizedLogo )) {
                /**
                 * Define the atributes before resize
                 */
                $imageObj = new Varien_Image ( $imageUrlLogo );
                $imageObj->constrainOnly ( TRUE );
                $imageObj->keepAspectRatio ( False );
                $imageObj->keepFrame ( FALSE );
                /**
                 * Resize the image according to the atributes
                 */
                
                if ($logo == 'store_logo') {
                    $imageObj->resize ( 150, 110 );
                } else {
                    $imageObj->resize ( 750, 230 );
                }
                /**
                 * Saving the Resized image
                 */
                $imageObj->save ( $imageResizedLogo );
            }
        }
        
        return $imagesPathLogoBanner;
    }
    
    /**
     * Set group id for customer
     *
     * @param number $adminApproval  
     * 
     * @return customer object
     */
    public function setGroupIdForCustomer($adminApproval) {
        if (! $customer = Mage::registry ( 'current_customer' )) {
            $customer = Mage::getModel ( 'customer/customer' )->setId ( null );
        }
        $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
        $customer->setGroupId ( $groupId );
        
        /**
         * set customer status depends upon admin approval
         */
        if ($adminApproval == 1) {
            /**
             * set customer status
             */
            $customer->setCustomerstatus ( '0' );
        } else {
            /**
             * set customer status
             */
            $customer->setCustomerstatus ( '1' );
        }
        
        return $customer;
    }
    /**
     * Set confirmation for seller
     *
     * @param array $customer            
     * @param string $sellerPassword            
     * @param string $sellerConfirmation            
     * @return array $customer
     */
    public function setConfirmationForSeller($customer, $sellerPassword, $sellerConfirmation) {
        /**
         * set password
         */
        $customer->setPassword ( $sellerPassword );
        $magentoVersion = Mage::getVersion ();
        if (version_compare ( $magentoVersion, '1.9.1', '>=' )) {
            /**
             * set password confirmation
             */
            $customer->setPasswordConfirmation ( $sellerConfirmation );
        } else {
            $customer->setConfirmation ( $sellerConfirmation );
        }
        return $customer;
    }
    
    /**
     * Get error details
     *
     * @param string $customerErrors            
     * @param array $errors            
     * @return aray $errors
     */
    public function getErrorDetails($customerErrors, $errors) {
        if (is_array ( $customerErrors )) {
            /**
             * Merge Array
             */
            $errors = array_merge ( $customerErrors, $errors );
        }
        return $errors;
    }
} 