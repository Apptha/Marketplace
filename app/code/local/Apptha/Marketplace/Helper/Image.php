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
 * Function written in this file are globally accessed
 */
class Apptha_Marketplace_Helper_Image extends Mage_Core_Helper_Abstract {
    
    /**
     * Function to upload product image
     *
     * @param int $filename            
     * @param array $filesDataArray            
     * @return string
     */
    public function uploadImage($filename, $filesDataArray) {
        /**
         * Create instance for uploader
         */
        $uploader = new Varien_File_Uploader ( $filename );
        /**
         * SEt allowed extensions
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
        /**
         * Initilize path
         */
        $path = Mage::getBaseDir ( 'media' ) . DS . 'tmp' . DS . 'catalog' . DS . 'product' . DS;
        $uploader->save ( $path, $filesDataArray [$filename] ['name'] );
        /**
         * Return path
         */
        return $path . $uploader->getUploadedFileName ();
    }
    
    /**
     * Function to save image in media gallery
     *
     * Product images are passed as array
     *
     * @param array $product
     *            This function will return true or false
     * @return bool
     */
    public function mediaGallery($product) {
        /**
         * Save product
         */
        $product->save ();
        return true;
    }
    
    /**
     * Function to disallow php files
     *
     * Uploaded file information are passed as array
     *
     * @param array $uploader
     *            Temporary storage path is passed to store the uploaded file
     * @param string $tmpPath
     *            This function will return true or false
     * @return bool
     */
    public function disAllowUpload($uploader, $tmpPath) {
        /**
         * Save tmp path
         */
        $uploader->save ( $tmpPath );
        return true;
    }
    
    /**
     * Function to delete existing product custom option
     *
     * Custom option details will be send as array
     *
     * @param array $opt
     *            This function will return true or false
     * @return bool
     */
    public function deleteCustomOption($opt) {
        /**
         * Delete options
         */
        $opt->delete ();
        return true;
    }
    
    /**
     * Resize normal images to display
     *
     * Return image url
     *
     * @return string
     */
    public function getNormalResizedImage($imagePath, $width, $height = null, $quality = 100) {
        /**
         * Initilize image url
         */
        $return = '';
        $imageUrl = Mage::getBaseDir ( 'skin' ) . DS . "frontend" . DS . "apptha" . DS . "marketplace" . DS . "images" . DS . $imagePath;
        if (! $imagePath || ! is_file ( $imageUrl )) {
            $return = false;
        } else {
            /**
             * Because clean Image cache function works in this folder only
             */
            $imageResizedData = Mage::getBaseDir ( 'media' ) . DS . 'catalog' . DS . 'product' . DS . "cache" . DS . "cat_resized" . DS . $width . $imagePath;
            if (! file_exists ( $imageResizedData ) && file_exists ( $imageUrl ) || file_exists ( $imageUrl ) && filemtime ( $imageUrl ) > filemtime ( $imageResizedData )) :
                $imageObj = new Varien_Image ( $imageUrl );
                $imageObj->constrainOnly ( true );
                $imageObj->keepAspectRatio ( false );
                $imageObj->quality ( $quality );
                $imageObj->keepFrame ( false );
                $imageObj->resize ( $width, $height );
                $imageObj->save ( $imageResizedData );
            endif;
            /**
             * Check whether file exists or not
             */
            if (file_exists ( $imageResizedData )) {
                $return = Mage::getBaseUrl ( 'media' ) . "catalog/product/cache/cat_resized/" . $width . $imagePath;
            } else {
                $return = $imagePath;
            }
        }
        
        return $return;
    }
    /**
     * Bulk product save function
     *
     * @param array $productInfo            
     */
    public function saveProductData($productData, $imagePath, $configurableAttributes) {
        $importProductsCount = $existSkuCounts = 0;
        /**
         * Check whether sku exists or not
         */
        if (isset ( $productData ['sku'] )) {
            $rowcountForImport = count ( $productData ['sku'] );
            $productCountArray = $this->saveBulkUploadProduct ( $productData, $imagePath, $configurableAttributes, $rowcountForImport );
            
            $importProductsCount = $productCountArray ['import_products_count'];
            $existSkuCounts = $productCountArray ['exist_sku_counts'];
        }
        /**
         * Check whether imported product counts greater than 1
         */
        if ($importProductsCount >= 1) {
            
            Mage::getModel ( 'marketplace/bulk' )->displayMessageForBulkUpload ( $importProductsCount );
            
            Mage::getModel ( 'marketplace/bulk' )->sendEamilToAdminForBulkProductUpload ();
        } else {
            Mage::getSingleton ( 'core/session' )->addNotice ( Mage::helper ( 'marketplace' )->__ ( 'No data found for upload' ) );
        }
        /**
         * end if clause
         */
        if ($existSkuCounts >= 1) {
            Mage::getSingleton ( 'core/session' )->addNotice ( $existSkuCounts . ' ' . $this->__ ( 'Sku(s) already exists' ) );
        }
        return true;
    }
    
    /**
     * Remove images direcotry
     *
     * @param string $dir            
     */
    public function rrmdir($dir) {
        /**
         * Remove directory
         */
        if (is_dir ( $dir )) {
            $objects = scandir ( $dir );
            /**
             * Increment foreach loop
             */
            foreach ( $objects as $object ) {
                if ($object != "." && $object != "..") {
                    $this->unlinkDir ( $dir, $object );
                }
            }
            /**
             * Reset objects
             */
            reset ( $objects );
            rmdir ( $dir );
        }
    }
    
    /**
     * Unlink dir
     *
     * @param string $dir            
     * @param array $object            
     */
    public function unlinkDir($dir, $object) {
    /**
     * Check file type
     */
        if (filetype ( $dir . "/" . $object ) == "dir") {
            Mage::helper ( 'marketplace/image' )->rrmdir ( $dir . "/" . $object );
        } else {
            /**
             * Unlink file
             */
            unlink ( $dir . "/" . $object );
        }
    }
    /**
     * Save bulk upload product
     *
     * @param array $productData            
     * @param array $imagePath            
     * @param array $configurableAttributes            
     * @param number $rowcountForImport            
     * @return array $productCountArray
     */
    public function saveBulkUploadProduct($productData, $imagePath, $configurableAttributes, $rowcountForImport) {
        $countries = Mage::getModel ( 'marketplace/bulk' )->getContriesValue ();
        /**
         * Initilize website ids
         */
        $websiteIds = array (
                Mage::app ()->getStore ( true )->getWebsite ()->getId () 
        );
        $importProductsCount = 0;
        $existSkuCounts = 0;
        $productCountArray = array ();
        /**
         * Increment foreach loop
         */
        foreach ( $productData ['sku'] as $key => $value ) {
            $flag = Mage::getModel ( 'marketplace/bulk' )->checkRequiredFieldForBulkUpload ( $productData, $key );
            if ($flag == 1) {
                $images = array ();
                $productSkuForCheck = Mage::getModel ( 'catalog/product' )->getIdBySku ( $productData ['sku'] [$key] );
                if ($productSkuForCheck) {
                    $existSkuCounts = $existSkuCounts + 1;
                    continue;
                }
                $orFlag = Mage::getModel ( 'marketplace/bulk' )->checkProductTypeForBulkUpload ( $productData, $key );
                if ($orFlag == 1) {
                    $product = Mage::getModel ( 'catalog/product' );
                    $categoryIds = array ();
                    /**
                     * Multi row product data
                     */
                    $attributeSetName = $productData ['_attribute_set'] [$key];
                    $sku = $productData ['sku'] [$key];
                    $name = $productData ['name'] [$key];
                    $description = $productData ['description'] [$key];
                    $shortDescription = $productData ['short_description'] [$key];
                    $price = $productData ['price'] [$key];
                    $type = $productData ['_type'] [$key];
                    $weight = Mage::getModel ( 'marketplace/bulk' )->getWeightForBulkUpload ( $productData, $key, $type );
                    /**
                     * Getting special price values
                     */
                    $specialPrice = $productData ['special_price'] [$key];
                    $specialDate = array ();
                    $specialDate ['special_from_date'] = $productData ['special_from_date'] [$key];
                    $specialDate ['special_to_date'] = $productData ['special_to_date'] [$key];
                    /**
                     * Fetch images info for product
                     */
                    $images = Mage::getModel ( 'marketplace/bulk' )->getImagesForBulkProduct ( $key, $productData );
                    $categoryIds = Mage::getModel ( 'marketplace/bulk' )->getCategoryIdsForBulk ( $key, $productData );
                    $customOptions = Mage::getModel ( 'marketplace/bulk' )->getCustomOptionsForBulk ( $key, $productData, $rowcountForImport );
                    $dataForBulkUpload = Mage::getModel ( 'marketplace/bulk' )->getDataForBulkProduct ( $key, $productData );
                    /**
                     * Fetch attribute set id by attribute set name
                     */
                    $entityTypeId = Mage::getModel ( 'eav/entity' )->setType ( 'catalog_product' )->getTypeId ();
                    $attributeSetId = Mage::getModel ( 'eav/entity_attribute_set' )->getCollection ()->setEntityTypeFilter ( $entityTypeId )->addFieldToFilter ( 'attribute_set_name', $attributeSetName )->getFirstItem ()->getAttributeSetId ();
                    
                    if (empty ( $attributeSetId )) {
                        $attributeSetId = Mage::getModel ( 'eav/entity_attribute_set' )->getCollection ()->setEntityTypeFilter ( $entityTypeId )->addFieldToFilter ( 'attribute_set_name', 'Default' )->getFirstItem ()->getAttributeSetId ();
                    }
                    
                    $product->setSku ( $sku );
                    $product->setName ( $name );
                    $product->setDescription ( $description );
                    $product->setShortDescription ( $shortDescription );
                    $product->setPrice ( $price );
                    /**
                     * Set product data for bulk product upload
                     */
                    $product = Mage::getModel ( 'marketplace/bulk' )->setProductDataForBulkProductUpload ( $product, $specialPrice, $specialDate, $type, $weight, $attributeSetId, $categoryIds );
                    $product = Mage::getModel ( 'marketplace/bulk' )->setProductInfoForBulkProductUpload ( $dataForBulkUpload, $productData, $key, $product, $websiteIds, $countries );
                    $product = Mage::getModel ( 'marketplace/bulk' )->setImagesAndCustomOptionForBulkProductUpload ( $product, $images, $imagePath, $customOptions );
                    /**
                     * Fetch configurable product options
                     */
                    $configurableProductsDataBulk = Mage::getModel ( 'marketplace/bulk' )->getConfigurableProductDataForBulkUpload ( $key, $type, $productData );
                    $attributeIds = $configurableProductsDataBulk ['attribute_ids'];
                    $configurableProductsData = $configurableProductsDataBulk ['configurable_products_data'];
                    $superProductsSkus = $configurableProductsDataBulk ['super_products_skus'];
                    $product = Mage::getModel ( 'marketplace/bulk' )->setConfigurableProductDataForBulkUpload ( $product, $attributeIds, $type, $attributeSetId, $configurableProductsData );
                    $product = Mage::getModel ( 'marketplace/bulk' )->setProductConfigData ( $productData, $product, $configurableAttributes, $key );
                    
                    /**
                     * Initialize configurable product options
                     */
                    $product->save ();
                    Mage::getModel ( 'marketplace/bulk' )->saveSimpleProductForBulkUpload ( $type, $superProductsSkus, $product );
                    
                    Mage::getSingleton ( 'catalog/product_option' )->unsetOptions ();
                    $importProductsCount = $importProductsCount + 1;
                }
            }
        }
        /**
         * Initilize rpoduct count array
         */
        $productCountArray ['import_products_count'] = $importProductsCount;
        $productCountArray ['exist_sku_counts'] = $existSkuCounts;
        return $productCountArray;
    }
}