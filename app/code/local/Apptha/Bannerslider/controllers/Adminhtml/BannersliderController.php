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
?>
<?php

/**
 * This class contains banner slider save and edit action
 * which used to admin grid banner slide
 */
class Apptha_Bannerslider_Adminhtml_BannersliderController extends Mage_Adminhtml_Controller_Action {
    /**
     * Init action for banner slider
     * return layout file
     */
    protected function _initAction() {
        /**
         * Return the admin grid layout
         */
        $this->loadLayout ()->_setActiveMenu ( 'marketplace/items' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Banner Sliders' ), Mage::helper ( 'adminhtml' )->__ ( 'Banner Sliders' ) );
        return $this;
    }
    /**
     * To set title and layout for banner slider
     */
    public function indexAction() {
        $this->_title ( $this->__ ( 'Banner slider' ) );
        $this->_initAction ()->renderLayout ();
    }
    /**
     * New action forward to exit funcation
     * Forword the edit action
     */
    public function newAction() {
        $this->_forward ( 'edit' );
    }
    
    /**
     * Edit action for banner slider
     * Set the edit action id
     */
    public function editAction() {
        $id = $this->getRequest ()->getParam ( 'id' );
        /**
         * collection for the banner slider.
         * load the banner slide model based on id
         */
        $bannerModel = Mage::getModel ( 'bannerslider/bannerslider' )->load ( $id );
        
        /**
         * Get the banner id.
         */
        if ($bannerModel->getId () || $id == 0) {
            $post = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            if (! empty ( $post )) {
                $bannerModel->setData ( $post );
            }
            /**
             * Set the register for bammer slide model
             */
            Mage::register ( 'bannerslider_data', $bannerModel );
            /**
             * Setting for grid title
             */
            $this->_title ( $this->__ ( 'Bannerslider' ) )->_title ( $this->__ ( 'Manage banner' ) );
            if ($bannerModel->getId ()) {
                $this->_title ( $bannerModel->getTitle () );
            } else {
                /**
                 * Set tittle as new banner.
                 */
                $this->_title ( $this->__ ( 'New Banner' ) );
            }
            
            /**
             * Set active button for marketplace
             */
            $this->loadLayout ();
            $this->_setActiveMenu ( 'marketplace/items' );
            
            /**
             * Set breadcrumb
             */
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ) );
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item News' ), Mage::helper ( 'adminhtml' )->__ ( 'Item News' ) );
            /**
             * Load layour header block for grid
             */
            $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
            
            /**
             * Edit link for the banner slider content etc.
             */
            $this->_addContent ( $this->getLayout ()->createBlock ( 'bannerslider/adminhtml_bannerslider_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'bannerslider/adminhtml_bannerslider_edit_tabs' ) );
            
            $this->renderLayout ();
        } else {
            /**
             * Show message as banner slider(Item) not exists.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'bannerslider' )->__ ( 'Item does not exist' ) );
            $this->_redirect ( '*/*/' );
        }
    }
    
    /**
     * Save banner slider funcation
     */
    public function saveAction() {
        /**
         * Get the params.
         */
        if ($post = $this->getRequest ()->getPost ()) {
            /**
             * Check file delete variable
             */
            if (isset ( $post ['file'] ['delete'] ) && $post ['file'] ['delete'] == 1) {
                $post ['file'] = '';
            } elseif (isset ( $post ['file'] ) && is_array ( $post ['file'] )) {
                $post ['file'] = $post ['file'] ['value'];
            }
            /**
             * Create a directory, if it is not exists.
             */
            $file = new Varien_Io_File ();
            $imageDir = Mage::getBaseDir ( 'media' ) . DS . 'sliderimages';
            /**
             * Folder for the thumb images.
             */
            $thumbimageyDir = Mage::getBaseDir ( 'media' ) . DS . 'sliderimages' . DS . 'thumbs';
            
            /**
             * Set folder permission as 777.
             */
            if (! is_dir ( $imageDir )) {
                $file->mkdir ( $imageDir, 0777 );
            }
            if (! is_dir ( $thumbimageyDir )) {
                $file->mkdir ( $thumbimageyDir, 0777 );
            }
            /**
             * Check file name is exist or not.
             */
            if (isset ( $_FILES ['file'] ['name'] ) && $_FILES ['file'] ['name'] != '') {
                try {
                    $uploaderObj = new Varien_File_Uploader ( 'file' );
                    
                    /**
                     * Set the allowed extention for the images into array format .
                     */
                    $uploaderObj->setAllowedExtensions ( array (
                            'jpg',
                            'jpeg',
                            'gif',
                            'png' 
                    ) );
                    
                    /**
                     * Set rename option true the files.
                     */
                    $uploaderObj->setAllowRenameFiles ( true );
                    /**
                     * Set the file descripotion true
                     */
                    $uploaderObj->setFilesDispersion ( true );
                    /**
                     * Set the image upload path
                     */
                    $path = $imageDir . DS;
                    $result = $uploaderObj->save ( $path, $_FILES ['file'] ['name'] );
                    $file = str_replace ( DS, '/', $result ['file'] );
                    /**
                     * Image url from storage path
                     */
                    $imageUrl = Mage::getBaseDir ( 'media' ) . DS . "sliderimages" . $file;
                    
                    /**
                     * Resized slider image path.
                     */
                    $imageResized = Mage::getBaseDir ( 'media' ) . DS . "sliderimages" . DS . "thumbs" . DS . "sliderimages" . $file;
                    /**
                     * Save slider images
                     */
                    if (! file_exists ( $imageResized ) && file_exists ( $imageUrl )) :
                        $imageObj = new Varien_Image ( $imageUrl );
                        $imageObj->constrainOnly ( TRUE );
                        $imageObj->keepAspectRatio ( FALSE );
                        $imageObj->keepFrame ( FALSE );
                        $imageObj->quality ( 100 );
                        $imageObj->resize ( 80, 50 );
                        $imageObj->save ( $imageResized );
                    
                    
                    endif;
                    $post ['file'] = 'sliderimages' . $file;
                } catch ( Exception $e ) {
                    $post ['file'] = 'sliderimages' . '/' . $_FILES ['file'] ['name'];
                    /**
                     * Error message for the slider image save option.
                     */
                    Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'bannerslider' )->__ ( 'Invalid file format upload attempt' ) );
                    $this->_redirect ( '*/*/' );
                    return;
                }
            }
            
            /**
             * Save slider data
             */
            $bannerModel = Mage::getModel ( 'bannerslider/bannerslider' );
            $bannerModel->setData ( $post )->setId ( $this->getRequest ()->getParam ( 'id' ) );
            
            try {
                /**
                 * Check the created and update time.
                 */
                if ($bannerModel->getCreatedTime == NULL || $bannerModel->getUpdateTime () == NULL) {
                    /**
                     * set banner created date.
                     */
                    $bannerModel->setCreatedTime ( date ( "Y-m-d H:i:s", Mage::getModel ( 'core/date' )->timestamp ( time () ) ) )->setUpdateTime ( date ( "Y-m-d H:i:s", Mage::getModel ( 'core/date' )->timestamp ( time () ) ) );
                } else {
                    /**
                     * Set banner updated date.
                     */
                    $bannerModel->setUpdateTime ( date ( "Y-m-d H:i:s", Mage::getModel ( 'core/date' )->timestamp ( time () ) ) );
                }
                
                $bannerModel->save ();
                /**
                 * Success message.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'bannerslider' )->__ ( 'Slider Image have been saved successfully' ) );
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( false );
                
                /**
                 * When click back button, page is redirected.
                 */
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', array (
                            'id' => $bannerModel->getId () 
                    ) );
                    return;
                }
                $this->_redirect ( '*/*/' );
                return;
            } catch ( Exception $e ) {
                /**
                 * Get the exception error.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( $post );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
                return;
            }
        }
        /**
         * Error message for the slider image save option.
         */
        Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'bannerslider' )->__ ( 'Unable to save Slider Image' ) );
        $this->_redirect ( '*/*/' );
    }
    
    /**
     * Mass delete action for banners
     */
    public function massDeleteAction() {
        $bannersliderIds = $this->getRequest ()->getParam ( 'bannerslider' );
        /**
         * Validate image is selected or not.
         */
        if (! is_array ( $bannersliderIds )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                /**
                 * Get banner slider collection.
                 */
                foreach ( $bannersliderIds as $bannersliderId ) {
                    $bannerslider = Mage::getModel ( 'bannerslider/bannerslider' )->load ( $bannersliderId );
                    $bannerslider->delete ();
                }
                /**
                 * Success message for delete image slider.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were deleted successfully', count ( $bannersliderIds ) ) );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
    
    /**
     * Delete action for banner
     */
    public function deleteAction() {
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                $bannerModel = Mage::getModel ( 'bannerslider/bannerslider' );
                /**
                 * Set the banner id
                 */
                $bannerModel->setId ( $this->getRequest ()->getParam ( 'id' ) )->delete ();
                /**
                 * Success message for slider images.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Slider Image have been deleted successfully !' ) );
                $this->_redirect ( '*/*/' );
            } catch ( Exception $e ) {
                /**
                 * Error message, When trying to delete slider images.
                 */
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                $this->_redirect ( '*/*/edit', array (
                        'id' => $this->getRequest ()->getParam ( 'id' ) 
                ) );
            }
        }
        /**
         * Redirect path.
         */
        $this->_redirect ( '*/*/' );
    }
    /**
     * Mass status update action for banner
     */
    public function massStatusAction() {
        $bannersliderIds = $this->getRequest ()->getParam ( 'bannerslider' );
        if (! is_array ( $bannersliderIds )) {
            /**
             * Validate image is selected or not.
             */
            Mage::getSingleton ( 'adminhtml/session' )->addError ( $this->__ ( 'Please select item(s)' ) );
        } else {
            try {
                /**
                 * GEt the banner slide based on id
                 */
                foreach ( $bannersliderIds as $bannersliderId ) {
                    Mage::getSingleton ( 'bannerslider/bannerslider' )->load ( $bannersliderId )->setStatus ( $this->getRequest ()->getParam ( 'status' ) )->save ();
                }
                /**
                 * Success messgae for delete the slider images.
                 */
                $this->_getSession ()->addSuccess ( $this->__ ( 'Total of %d record(s) were updated successfully', count ( $bannersliderIds ) ) );
            } catch ( Exception $e ) {
                $this->_getSession ()->addError ( $e->getMessage () );
            }
        }
        /**
         * Redirect to index
         */
        $this->_redirect ( '*/*/index' );
    }
}