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
 * This file is used to bulk product upload and assign products for sellers
 */
class Apptha_Marketplace_BulkuploadController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Bulk product upload functinality for seller
     *
     * @return void
     */
    public function bulkuploadAction() {
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();
        
        try {
            /**
             * New zend File Uploader
             */
            $uploadsData = new Zend_File_Transfer_Adapter_Http ();
            $filesDataArray = $uploadsData->getFileInfo ();
            
            /**
             * Checking whether csv exist or not
             */
            if (! empty ( $filesDataArray )) {
                $this->saveBulkUploadFiles ( $filesDataArray );
            }
        } catch ( Exception $e ) {
            /**
             * Display error message for csv file upload
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
            $this->_redirect ( 'marketplace/product/manage' );
            return;
        }
    }
    
    /**
     * Check whether seller or not
     */
    public function checkWhetherSellerOrNot() {
        /**
         * Initilize customer and seller group id
         */
        $customerGroupId = $sellerGroupId = $customerStatus = '';
        /**
         * Get Customer Group Id
         */
        $customerGroupId = Mage::getSingleton ( 'customer/session' )->getCustomerGroupId ();
        /**
         * Get Group Id
         */
        $sellerGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
        /**
         * Get Customer Status
         */
        $customerStatus = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn () && $customerGroupId != $sellerGroupId) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        /**
         * Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
    }
    public function saveBulkUploadFiles($filesDataArray) {
        $csvFilePath = $homeFolder = $imageFilePath = '';
        /**
         * Get Customer Id
         */
        $sellerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * Getting Current Time
         */
        
        $currentDateTime = date ( "Ymd_His", Mage::getModel ( 'core/date' )->timestamp ( time () ) );
        
        foreach ( $filesDataArray as $key => $value ) {
            /**
             * Initilize file name
             */
            $filename = $key;
            
            /**
             * Upload csv file
             */
            
            if ($key == 'bulk-product-upload-csv-file' && isset ( $filesDataArray [$filename] ['name'] ) && (file_exists ( $filesDataArray [$filename] ['tmp_name'] ))) {
                $csvFilePath = '';
                $csvFilePath = array ();
                $uploader = new Varien_File_Uploader ( $filename );
                $uploader->setAllowedExtensions ( array (
                        'csv' 
                ) );
                $uploader->setAllowRenameFiles ( true );
                $uploader->setFilesDispersion ( false );
                $path = Mage::getBaseDir ( 'media' ) . DS . 'marketplace' . DS . 'bulk' . DS . 'product' . DS . 'csv' . DS;
                
                $uploader->save ( $path, 'seller_' . $sellerId . '_date_' . $currentDateTime . '.csv' );
                $csvFilePath = $path . $uploader->getUploadedFileName ();
            }
            
            /**
             * Upload csv image
             */
            if ($key == 'bulk-product-upload-image-file' && isset ( $filesDataArray [$filename] ['name'] ) && (file_exists ( $filesDataArray [$filename] ['tmp_name'] ))) {
                $uploader = new Varien_File_Uploader ( $filename );
                $uploader->setAllowedExtensions ( array (
                        'zip' 
                ) );
                $uploader->setAllowRenameFiles ( true );
                $uploader->setFilesDispersion ( false );
                $path = Mage::getBaseDir ( 'media' ) . DS . 'marketplace' . DS . 'bulk' . DS . 'product' . DS . 'image' . DS;
                /**
                 * Uploader save
                 */
                $uploader->save ( $path, 'seller_' . $sellerId . '_date_' . $currentDateTime . '.zip' );
                $imageFilePath = $path . $uploader->getUploadedFileName ();
                
                $ZipFileName = $imageFilePath;
                $homeFolder = Mage::getBaseDir ( 'media' ) . DS . 'marketplace' . DS . 'bulk' . DS . 'product' . DS . 'image' . DS . 'seller_' . $sellerId . '_date_' . $currentDateTime;
                /**
                 * New Varien File
                 */
                $file = new Varien_Io_File ();
                /**
                 * Make Directory
                 */
                $file->mkdir ( $homeFolder );
                Mage::helper ( 'marketplace/product' )->exportZipFile ( $ZipFileName, $homeFolder );
            }
        }
        /**
         * Conver csv file path
         */
        
        $productData = Mage::helper ( 'marketplace/product' )->convertCsvFileToUploadArray ( $csvFilePath );
        $this->bulkproductuploadfuncationality ( $imageFilePath, $productData, $homeFolder, $csvFilePath );
        return true;
    }
    
    /**
     *
     * @param string $imageFilePath            
     * @param array $productData            
     * @param string $homeFolder            
     * @param string $csvFilePath            
     * @return boolean
     */
    public function bulkproductuploadfuncationality($imageFilePath, $productData, $homeFolder, $csvFilePath) {
        if (file_exists ( $imageFilePath )) {
            /**
             * Delete images from temporary zip folder
             */
            unlink ( $imageFilePath );
        }
        
        if (isset ( $productData [0] )) {
            $configurableAttributes = array ();
            /**
             * Get Configurable Products
             */
            $configurableAttributes = $this->getRequest ()->getPost ( 'configurable_attribute' );
            Mage::helper ( 'marketplace/image' )->saveProductData ( $productData [0], $homeFolder, $configurableAttributes );
            if (Mage::getStoreConfig ( 'marketplace/product/save_uploadfiles' ) != 1) {
                if (file_exists ( $csvFilePath )) {
                    /**
                     * Delete csv file
                     */
                    unlink ( $csvFilePath );
                }
                
                /**
                 * Delete images from temporary zip folder
                 */
                Mage::helper ( 'marketplace/image' )->rrmdir ( $homeFolder );
            }
            $this->_redirect ( 'marketplace/product/manage/' );
        } else {
            /**
             * Add Notice
             */
            Mage::getSingleton ( 'core/session' )->addNotice ( Mage::helper ( 'marketplace' )->__ ( 'No data found' ) );
            $this->_redirect ( 'marketplace/product/manage/' );
            return true;
        }
    }
}