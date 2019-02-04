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

/**
 * Function written in this file are used for download product manipulation
 */
class Apptha_Marketplace_Model_Download extends Mage_Core_Model_Abstract {
    
    /**
     * Save download product sample
     *
     * @param array $downloadableData            
     * @param number $downloadProductId            
     * @param array $sampleTpath            
     *
     * @return void
     *
     */
    public function saveDownLoadProductSample($downloadableData, $downloadProductId, $sampleTpath, $store) {
        /**
         * Storing Downloadable product sample data
         */
        if (isset ( $downloadableData ['sample'] )) {
            foreach ( $downloadableData ['sample'] as $sampleItem ) {
                $sampleId = '';
                $sample = array ();
                $sampleId = $sampleItem ['sample_id'];
                if (isset ( $sampleTpath [$sampleId] )) {
                    $sample [] = $sampleTpath [$sampleId];
                }
                $sampleModel = Mage::getModel ( 'downloadable/sample' );
                $sampleModel->setData ( $sample )->setSampleType ( $sampleItem ['type'] )->setProductId ( $downloadProductId )->setStoreId ( 0 )->setWebsiteIds ( array (
                        Mage::app ()->getStore ( $store )->getWebsiteId () 
                ) )->setTitle ( $sampleItem ['title'] )->setDefaultTitle ( false )->setSortOrder ( $sampleItem ['sort_order'] );
                if ($sampleItem ['type'] == 'url') {
                    $sampleModel->setSampleUrl ( $sampleItem ['sample_url'] );
                }
                if (! empty ( $sampleTpath [$sampleId] ) && $sampleItem ['type'] == 'file') {
                    if ($sampleModel->getSampleType () == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                        $sampleFileName = Mage::helper ( 'downloadable/file' )->moveFileFromTmp ( Mage_Downloadable_Model_Sample::getBaseTmpPath (), Mage_Downloadable_Model_Sample::getBasePath (), $sample );
                    }
                    $sampleModel->setSampleFile ( $sampleFileName );
                } else {
                    if (! empty ( $sampleItem ['sample_file'] )) {
                        $sampleFileName = $sampleItem ['sample_file'];
                        $sampleModel->setSampleFile ( $sampleFileName );
                    }
                }
                Mage::helper ( 'marketplace/common' )->saveDownLoadLink ( $sampleModel );
            }
        }
    }
    
    /**
     * Save download product link
     *
     * @param array $downloadableData            
     * @param number $downloadProductId            
     * @param array $linkTpath            
     * @param array $slinkTpath            
     *
     * @return void
     *
     */
    public function saveDownLoadProductLink($downloadableData, $downloadProductId, $linkTpath, $slinkTpath, $store) {
        /**
         * Storing Downloadable product sample data
         */
        foreach ( $downloadableData ['link'] as $linkItem ) {
            $linkId = '';
            $linkFile = $sampleFile = array ();
            $linkId = $linkItem ['link_id'];
            
            $linkFile = $this->assignLinkFile ( $linkTpath, $linkId, $linkFile );
            $sampleFile = $this->assignLinkFile ( $slinkTpath, $linkId, $sampleFile );
            
            $linkModel = Mage::getModel ( 'downloadable/link' )->setData ( $linkFile )->setLinkType ( $linkItem ['type'] )->setProductId ( $downloadProductId )->setWebsiteIds ( array (
                    Mage::app ()->getStore ( $store )->getWebsiteId () 
            ) )->setStoreId ( 0 )->setSortOrder ( $linkItem ['sort_order'] )->setTitle ( $linkItem ['title'] )->setIsShareable ( $linkItem ['is_shareable'] );
            if ($linkItem ['type'] == 'url') {
                $linkModel->setLinkUrl ( $linkItem ['link_url'] );
            }
            $linkModel->setPrice ( $linkItem ['price'] );
            $linkModel->setNumberOfDownloads ( $linkItem ['number_of_downloads'] );
            if (isset ( $linkItem ['sample'] ['type'] )) {
                if ($linkItem ['sample'] ['type'] == 'url') {
                    $linkModel->setSampleUrl ( $linkItem ['sample'] ['url'] );
                }
                $linkModel->setSampleType ( $linkItem ['sample'] ['type'] );
            }
            
            $sampleFile = '';
            $sampleFile = Zend_Json::decode ( json_encode ( $sampleFile ) );
            if (! empty ( $linkTpath [$linkId] ) && $linkItem ['type'] == 'file') {
                $linkFileName = Mage::helper ( 'downloadable/file' )->moveFileFromTmp ( Mage_Downloadable_Model_Link::getBaseTmpPath (), Mage_Downloadable_Model_Link::getBasePath (), $linkFile );
                $linkModel->setLinkFile ( $linkFileName );
            } else {
                if (! empty ( $linkItem ['link_file'] )) {
                    $linkFileName = $linkItem ['link_file'];
                    $linkModel->setLinkFile ( $linkFileName );
                }
            }
            
            if (! empty ( $slinkTpath [$linkId] ) && isset ( $sampleFile ) && $linkItem ['sample'] ['type'] = 'file') {
                $linkSampleFileName = Mage::helper ( 'downloadable/file' )->moveFileFromTmp ( Mage_Downloadable_Model_Link::getBaseSampleTmpPath (), Mage_Downloadable_Model_Link::getBaseSamplePath (), $sampleFile );
                $linkModel->setSampleFile ( $linkSampleFileName );
            } else {
                if (! empty ( $linkItem ['link_sample_file'] )) {
                    $linkSampleFileName = $linkItem ['link_sample_file'];
                    $linkModel->setSampleFile ( $linkSampleFileName );
                }
            }
            
            Mage::helper ( 'marketplace/common' )->saveDownLoadLink ( $linkModel );
        }
    }
    
    /**
     * Assign link file
     *
     * @param array $linkTpath            
     * @param array $linkId            
     * @param array $linkFile            
     */
    public function assignLinkFile($linkTpath, $linkId, $linkFile) {
        if (isset ( $linkTpath [$linkId] )) {
            $linkFile [] = $linkTpath [$linkId];
        }
        return $linkFile;
    }
    
    /**
     * Get sample path
     *
     * @param string $type            
     * @param number $key            
     * @param array $sampleTpath            
     * @param string $filePath            
     * @param string $fileName            
     * @param number $fileSize            
     * @return array $sampleTpath
     */
    public function getSampleTpath($type, $filePath, $fileName, $fileSize) {
        $sampleTpath = array ();
        if ($type == 'samples') {
            $sampleTpath = array (
                    'file' => $filePath,
                    'name' => $fileName,
                    'size' => $fileSize,
                    'status' => 'new' 
            );
        }
        return $sampleTpath;
    }
    
    /**
     * Get link path
     *
     * @param string $type            
     * @param number $key            
     * @param array $linkTpath            
     * @param string $filePath            
     * @param string $fileName            
     * @param number $fileSize            
     * @return array $sampleTpath
     */
    public function getLinkTpath($type, $filePath, $fileName, $fileSize) {
        $linkTpath = array ();
        if ($type == 'links') {
            $linkTpath = array (
                    'file' => $filePath,
                    'name' => $fileName,
                    'size' => $fileSize,
                    'status' => 'new' 
            );
        }
        
        return $linkTpath;
    }
    
    /**
     * Get sample link path
     *
     * @param string $type            
     * @param number $key            
     * @param array $slinkTpath            
     * @param string $filePath            
     * @param string $fileName            
     * @param number $fileSize            
     * @return array $sampleTpath
     */
    public function getSLinkTpath($type, $filePath, $fileName, $fileSize) {
        $slinkTpath = array ();
        if ($type == 'link_samples') {
            $slinkTpath = array (
                    'file' => $filePath,
                    'name' => $fileName,
                    'size' => $fileSize,
                    'status' => 'new' 
            );
        }
        return $slinkTpath;
    }
    
    /**
     * Save full path
     *
     * @param array $result            
     * @param string $tmpPath            
     * @return void
     */
    public function saveFullPath($result, $tmpPath) {
        if (isset ( $result ['file'] )) {
            $fullPath = rtrim ( $tmpPath, DS ) . DS . ltrim ( $result ['file'], DS );
            Mage::helper ( 'core/file_storage_database' )->saveFile ( $fullPath );
        }
    }
    
    /**
     * Prepare downloadable product data
     *
     * @param array $filesDataArray            
     * @param number $key            
     * @param array $result            
     * @return array
     *
     */
    public function prepareDownloadProductData($filesDataArray, $key, $result) {
        $downloadData = array ();
        if ($this->checkingForFilesDataArray ( $filesDataArray, $key )) {
            $type = '';
            $tmpPathResult = $this->getTmpPathForDownloadable ( $key );
            $tmpPath = $tmpPathResult ['tmp_path'];
            $type = $tmpPathResult ['type'];
            if ($type == 'samples' || $type == 'links' || $type == 'link_samples') {
                $result = array ();
                /**
                 * Initilize validate flag
                 */
                $validateFlag = 0;
                /**
                 * Getting uploaded file extension type
                 */
                $uploaderExtension = pathinfo ( $filesDataArray [$key] ['name'], PATHINFO_EXTENSION );
                $validateImage = array (
                        'jpg',
                        'jpeg',
                        'gif',
                        'png' 
                );                
                $uploader = new Varien_File_Uploader ( $key );
                if (in_array ( $uploaderExtension, $validateImage )) {
                    $imgSize = getimagesize ( $filesDataArray [$key] ['tmp_name'] );
                    $uploaderArray = Mage::helper ( 'marketplace/general' )->getImageValidation ( $uploader, $imgSize, $validateFlag );
                    $uploader = $uploaderArray ['uploader'];
                    $validateFlag = $uploaderArray ['validate_flag'];
                }
                
                /**
                 * Disallow php file for downloadable product
                 */
                if ($uploaderExtension != 'php' && $validateFlag == 0) {                   
                    $uploader->setAllowRenameFiles ( true );
                    $result = $uploader->save ( $tmpPath );
                    $result ['tmp_name'] = str_replace ( DS, "/", $result ['tmp_name'] );
                    $result ['path'] = str_replace ( DS, "/", $result ['path'] );
                    
                    Mage::getModel ( 'marketplace/download' )->saveFullPath ( $result, $tmpPath );
                    
                    $fileName = $filePath = $fileSize = '';
                    $fileName = $uploader->getUploadedFileName ();
                    $filePath = ltrim ( $result ['file'], DS );
                    $fileSize = $result ['size'];
                    
                    $downloadData ['sample_tpath'] = Mage::getModel ( 'marketplace/download' )->getSampleTpath ( $type, $filePath, $fileName, $fileSize );
                    $downloadData ['link_tpath'] = Mage::getModel ( 'marketplace/download' )->getLinkTpath ( $type, $filePath, $fileName, $fileSize );
                    $downloadData ['slink_tpath'] = Mage::getModel ( 'marketplace/download' )->getSLinkTpath ( $type, $filePath, $fileName, $fileSize );
                } else {
                    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Disallowed file type.' ) );
                }
            }
        }
        return $downloadData;
    }
    
    /**
     * Get temporary path for downloadable product
     *
     * @param array $key            
     * @return array $tmpPathResult
     */
    public function getTmpPathForDownloadable($key) {
        $type = $tmpPath = '';
        $tmpPathResult = array ();
        if (substr ( $key, 0, 5 ) == 'sampl') {
            $tmpPath = Mage_Downloadable_Model_Sample::getBaseTmpPath ();
            $type = 'samples';
        }
        if (substr ( $key, 0, 5 ) == 'links') {
            $tmpPath = Mage_Downloadable_Model_Link::getBaseTmpPath ();
            $type = 'links';
        }
        if (substr ( $key, 0, 5 ) == 'l_sam') {
            $tmpPath = Mage_Downloadable_Model_Link::getBaseSampleTmpPath ();
            $type = 'link_samples';
        }
        
        $tmpPathResult ['type'] = $type;
        $tmpPathResult ['tmp_path'] = $tmpPath;
        
        return $tmpPathResult;
    }
    
    /**
     * Checking files data array for downloadable product
     */
    public function checkingForFilesDataArray($filesDataArray, $key) {
        if (isset ( $filesDataArray [$key] ['name'] ) && (file_exists ( $filesDataArray [$key] ['tmp_name'] ))) {
            return 1;
        }
        return 0;
    }
    
    /**
     * Delete downloadable sample
     *
     * @param
     *            $downloadableSample
     * @return void
     */
    public function deleteDownloadableSample($downloadableSample) {
        $sampleDeleteItems = array ();
        /**
         * Removing all sample data
         */
        foreach ( $downloadableSample as $sampleDelete ) {
            $sampleDeleteItems [] = $sampleDelete->getSampleId ();
        }
        if (! empty ( $sampleDeleteItems )) {
            Mage::getResourceModel ( 'downloadable/sample' )->deleteItems ( $sampleDeleteItems );
        }
    }
    
    /**
     * Delete downloadable link
     *
     * @param
     *            $downloadableLink
     * @return void
     */
    public function deleteDownloadableLinks($downloadableLink) {
        /**
         * Removing all link data
         */
        $linkDeleteItems = array ();
        foreach ( $downloadableLink as $linkDelete ) {
            $linkDeleteItems [] = $linkDelete->getLinkId ();
        }
        if (! empty ( $linkDeleteItems )) {
            Mage::getResourceModel ( 'downloadable/link' )->deleteItems ( $linkDeleteItems );
        }
    }
} 