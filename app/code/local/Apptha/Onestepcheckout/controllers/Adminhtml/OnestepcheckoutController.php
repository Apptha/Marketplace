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
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
/**
 * Manage one step checkout functions
 * This class is used for controlling the one step checkout functions in admin side
 */
class Apptha_Onestepcheckout_Adminhtml_OnestepcheckoutController extends Mage_Adminhtml_Controller_Action {
    
    /**
     * Initialize the onestepcheckout controller action
     *
     * @return void
     */
    protected function _initAction() {
        $this->loadLayout ()->_setActiveMenu ( 'Onestepcheckout/items' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Items Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ) );
        
        return $this;
    }
    /**
     * Load the phtml layout
     *
     * @return void
     */
    public function indexAction() {
        /**
         * Render Layout
         */
        $this->_initAction ()->renderLayout ();
    }
    /**
     * Cart edit action
     *
     * @return void
     */
    public function editAction() {
        /**
         * get post id value
         */
        $id = $this->getRequest ()->getParam ( 'id' );
        /**
         * load model data using id
         */
        $model = Mage::getModel ( 'Onestepcheckout/Onestepcheckout' )->load ( $id );
        /**
         * check condition if id is not empty and equal to 0
         */
        if ($model->getId () || $id == 0) {
            $data = Mage::getSingleton ( 'adminhtml/session' )->getFormData ( true );
            /**
             * check condition data is not empty
             */
            if (! empty ( $data )) {
                $model->setData ( $data );
            }
            Mage::register ( 'Onestepcheckout_data', $model );
            /**
             * Load Layout
             */
            $this->loadLayout ();
            $this->_setActiveMenu ( 'Onestepcheckout/items' );
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ) );
            $this->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Item News' ), Mage::helper ( 'adminhtml' )->__ ( 'Item News' ) );
            $this->getLayout ()->getBlock ( 'head' )->setCanLoadExtJs ( true );
            $this->_addContent ( $this->getLayout ()->createBlock ( 'Onestepcheckout/adminhtml_Onestepcheckout_edit' ) )->_addLeft ( $this->getLayout ()->createBlock ( 'Onestepcheckout/adminhtml_Onestepcheckout_edit_tabs' ) );
            $this->renderLayout ();
        } else {
            $this->sessionErrorMessage ();
            $this->_redirect ( '*/*/' );
        }
    }
    
    /**
     * get Error message
     *
     * @return void
     */
    public function sessionErrorMessage() {
        /**
         * Get Session
         */
        Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'Onestepcheckout' )->__ ( 'Item does not exist' ) );
    }
    
    /**
     * Cart edit action
     *
     * @return void
     */
    public function newAction() {
        $this->_forward ( 'edit' );
    }
    /**
     * Cart save action
     *
     * @return void
     */
    public function saveAction() {
        /**
         * check condition post data is not empty
         */
        if ($data = $this->getRequest ()->getPost ()) {
            
            /**
             * check condition username is not equal to empty
             */
            if (isset ( $_FILES ['filename'] ['name'] ) && $_FILES ['filename'] ['name'] != '') {
                
                /**
                 * Starting upload
                 */
                $uploader = new Varien_File_Uploader ( 'filename' );
                /**
                 * Any extention would work
                 */
                $uploader->setAllowedExtensions ( array (
                        'jpg',
                        'jpeg',
                        'gif',
                        'png' 
                ) );
                $uploader->setAllowRenameFiles ( false );
                
                /**
                 * Set the file upload model
                 * false -> get the file directly in the specified folder
                 * true -> get the file in the product like folders
                 * (file.jpg will go in something like /media/f/i/file.jpg)
                 */
                $uploader->setFilesDispersion ( false );
                
                /**
                 * We set media as the upload dir
                 */
                $path = Mage::getBaseDir ( 'media' ) . DS;
                $uploader->save ( $path, $_FILES ['filename'] ['name'] );
                
                /**
                 * this way the name is saved in DB
                 */
                $data ['filename'] = $_FILES ['filename'] ['name'];
            }
            /**
             * load data from table
             */
            $model = Mage::getModel ( 'Onestepcheckout/Onestepcheckout' );
            /**
             * set id value using post data
             */
            $model->setData ( $data )->setId ( $this->getRequest ()->getParam ( 'id' ) );
            
            try {
                /**
                 * check condition created time is equal to null or update time is equal to null
                 */
                $validateModel = $this->validateModel ( $model );
                if ($validateModel == 1) {
                    $model->setCreatedTime ( now () )->setUpdateTime ( now () );
                } else {
                    $model->setUpdateTime ( now () );
                }
                /**
                 * Model save
                 */
                $model->save ();
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'Onestepcheckout' )->__ ( 'Item was successfully saved' ) );
                Mage::getSingleton ( 'adminhtml/session' )->setFormData ( false );
                /**
                 * check condition post back value is not empty
                 */
                if ($this->getRequest ()->getParam ( 'back' )) {
                    $this->_redirect ( '*/*/edit', array (
                            'id' => $model->getId () 
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
        Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'Onestepcheckout' )->__ ( 'Unable to find item to save' ) );
        $this->_redirect ( '*/*/' );
    }
    
    /**
     * Validate model
     *
     * @param array $model            
     * @return number $validateModel
     */
    public function validateModel($model) {
        $validateModel = 0;
        if ($model->getCreatedTime == NULL || $model->getUpdateTime () == NULL) {
            $validateModel = 1;
        }
        return $validateModel;
    }
    
    /**
     * Cart delete action
     *
     * @return void
     */
    public function deleteAction() {
        /**
         * check condition post id value greaterthan 0
         */
        if ($this->getRequest ()->getParam ( 'id' ) > 0) {
            try {
                $onestepModel = Mage::getModel ( 'Onestepcheckout/Onestepcheckout' );
                /**
                 * Delete Product
                 */
                $onestepModel->setId ( $this->getRequest ()->getParam ( 'id' ) )->delete ();
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__('Item was successfully deleted!') );
                /**
                 * Redirect into current url
                 */
                $this->_redirect ('*/*/');
            } 
            catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
                /**
                 * Redirect into edit url
                 */
                $this->_redirect ( '*/*/edit', array ('id' => $this->getRequest ()->getParam ( 'id' ) ) );
            }
        }
        /**
         * Redirect into current url
         */ 
        $this->_redirect ('*/*/');
    }
    /**
     * Cart mass delete action
     *
     * @return void
     */
    public function massDeleteAction() {
        $onepagecheckoutIds = $this->getRequest ()->getParam ( 'Onestepcheckout' );
        /**
         * check condition one step ids is not array
         */
        if (! is_array ( $onepagecheckoutIds )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                foreach ( $onepagecheckoutIds as $onepagecheckoutId ) {
                    $onepagecheckout = Mage::getModel ( 'Onestepcheckout/Onestepcheckout' )->load ( $onepagecheckoutId );
                    $onepagecheckout->delete ();
                }
                Mage::getSingleton ( 'adminhtml/session' )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were successfully deleted', count ( $onepagecheckoutIds ) ) );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'adminhtml/session' )->addError ( $e->getMessage () );
            }
        }
        /**
         * Redirect into index controller url
         */
        $this->_redirect ( '*/*/index' );
    }
    /**
     * Change cart status action
     *
     * @return void
     */
    public function massStatusAction() {
        $onepagecheckoutIds = $this->getRequest ()->getParam ( 'Onestepcheckout' );
        /**
         * check condition one step ids is not array
         */
        if (! is_array ( $onepagecheckoutIds )) {
            Mage::getSingleton ( 'adminhtml/session' )->addError ( $this->__ ( 'Please select item(s)' ) );
        } else {
            try {
                foreach ( $onepagecheckoutIds as $onepagecheckoutId ) {
                    Mage::getSingleton ( 'Onestepcheckout/Onestepcheckout' )->load ( $onepagecheckoutId )->setStatus ( $this->getRequest ()->getParam ( 'status' ) )->setIsMassupdate ( true )->save ();
                }
                $this->_getSession ()->addSuccess ( $this->__ ( 'Total of %d record(s) were successfully updated', count ( $onepagecheckoutIds ) ) );
            } catch ( Exception $e ) {
                $this->_getSession ()->addError ( $e->getMessage () );
            }
        }
        /**
         * Redirect into index controller url
         */
        $this->_redirect ( '*/*/index' );
    }
    /**
     * Export data as csv file
     *
     * @return void
     */
    public function exportCsvAction() {
        $fileName = 'Onestepcheckout.csv';
        $content = $this->getLayout ()->createBlock ( 'Onestepcheckout/adminhtml_Onestepcheckout_grid' )->getCsv ();
        
        $this->_sendUploadResponse ( $fileName, $content );
    }
    /**
     * Export data as xml file
     *
     * @return void
     */
    public function exportXmlAction() {
        $fileName = 'Onestepcheckout.xml';
        $content = $this->getLayout ()->createBlock ( 'Onestepcheckout/adminhtml_Onestepcheckout_grid' )->getXml ();
        
        $this->_sendUploadResponse ( $fileName, $content );
    }
    /**
     * Load the data for export option
     *
     * @return void
     */
    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse ();
        $response->setHeader ( 'HTTP/1.1 200 OK', '' );
        $response->setHeader ( 'Pragma', 'public', true );
        $response->setHeader ( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true );
        $response->setHeader ( 'Content-Disposition', 'attachment; filename=' . $fileName );
        $response->setHeader ( 'Last-Modified', date ( 'r' ) );
        $response->setHeader ( 'Accept-Ranges', 'bytes' );
        $response->setHeader ( 'Content-Length', strlen ( $content ) );
        $response->setHeader ( 'Content-type', $contentType );
        $response->setBody ( $content );
        $response->sendResponse ();
    }
}