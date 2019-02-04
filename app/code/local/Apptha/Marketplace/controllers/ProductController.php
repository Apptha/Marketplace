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
 * This file is used to add/edit seller products
 */
class Apptha_Marketplace_ProductController extends Mage_Core_Controller_Front_Action {

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        return Mage::getSingleton ( 'customer/session' );
    }

    /**
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {
        if (! $this->_getSession ()->isLoggedIn ()) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        $this->loadLayout ();
        $this->renderLayout ();
    }

    /**
     * Add New Products Form
     *
     * @return void
     */
    public function newAction() {
        /**
         * Initilize customer and seller group id
         */
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();

        $this->loadLayout ();
        $this->renderLayout ();
    }

    /**
     * Save New Products
     *
     * @return void
     */
    public function newpostAction() {
        $this->checkWhetherSellerOrNot ();
        Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
        $set = $setBase = $type = $store = $sellerId = $isInStock = '';
        /**
         * Getting product type, set, setbase, store, group id and product
         */
        $type = $this->getRequest ()->getPost ( 'type' );
        $set = $this->getRequest ()->getPost ( 'set' );
        $setBase = $this->getRequest ()->getPost ( 'setbase' );
        $store = $this->getRequest ()->getPost ( 'store' );
        $sellerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
        $productData = $this->getRequest ()->getPost ( 'product' );
        $delivery = $this->getRequest ()->getPost ( 'delivery' );
        if(isset($delivery)) {
            foreach($delivery as $location){
                $_location [] = $location;
            }
        }
        $_location = implode(",",$_location);
        $skuProductId = '';
        $skuProductId = Mage::getModel ( 'marketplace/product' )->checkWhetherSkuExistOrNot ( $productData );
        if ($skuProductId == 0) {
            /**
             * Getting product categories from category_ids array
             */
            $categoryIds = $this->getRequest ()->getPost ( 'category_ids' );

            $checkRequiredForProductSave = Mage::helper ( 'marketplace/market' )->checkRequiredForProductSave ( $productData );

            if ($checkRequiredForProductSave == 1 && isset ( $productData ['price'] ) && isset ( $productData ['stock_data'] ['qty'] ) && ! empty ( $type )) {

                /**
                 * Getting instance for catalog product collection
                 */
                $product = Mage::getModel ( 'catalog/product' );
                $imagesPath = array ();
                $uploadsData = new Zend_File_Transfer_Adapter_Http ();
                $filesDataArray = $uploadsData->getFileInfo ();
                $imagesPath = Mage::getModel ( 'marketplace/product' )->getProductImagePath ( $filesDataArray );
                $ids=array('seller_id'=>$sellerId,'group_id'=>$groupId);
                $product = Mage::getModel ( 'marketplace/product' )->setProductInfo ( $product, $set, $type, $categoryIds,$ids, $imagesPath);
                $productData = Mage::getModel ( 'marketplace/product' )->getProductDataArray ( $productData, $type);
                $productData['delivery'] = $_location;
                /**
                 * Assign configurable product data
                 */
                $attributeIds = $this->getRequest ()->getPost ( 'attributes' );
                Mage::getModel ( 'marketplace/product' )->assignConfigurableProductData ( $attributeIds, $type, $product );
                $product->addData ( $productData);

                if ($type == 'downloadable') {
                    $product->setStockData ( array (
                            'use_config_manage_stock' => 0,
                            'is_in_stock' => 1,
                            'manage_stock' => 0,
                            'use_config_notify_stock_qty' => 0
                    ) );
                }
                /**
                 * Saving new product
                 */
                try {
                    $product->save ();
                    $productId = $product->getId ();
                    /**
                     * Initialise seller language var
                     */
                    $sellerDefaultLangId = $this->getRequest ()->getParam ( 'seller_product_lang' );
                    Mage::getModel ( 'marketplace/sellerlanguage' )->addData ( array (
                            'seller_id' => $sellerId,
                            'product_id' => $productId,
                            'store_id' => $sellerDefaultLangId,
                            'created_at' => strtotime ( 'now' )
                    ) )->save ();
                    /**
                     * Save product in store view
                     */
                    $allStores = Mage::app ()->getStores ();
                    /**
                     * getting all store values
                     */
                    $storeName = $storeDesc = $storeShortDesc = null;
                     foreach ( $allStores as $_eachStoreId => $val ) {
                       Mage::helper('marketplace/outofstock')->newproductPost($productData,$_eachStoreId,$productId,$sellerDefaultLangId,$storeName,$storeDesc,$storeShortDesc);
                    }
                    Mage::getModel ( 'marketplace/product' )->setConfigurableProductStockData ( $type, $product, $productData, $isInStock );
                    Mage::getModel ( 'marketplace/product' )->setBaseImageForProduct ( $productId, $store, $setBase, $productData, 'new' );
                    Mage::getModel ( 'marketplace/product' )->deleteTempImageFiles ( $imagesPath );
                    /**
                     * Function for adding downloadable product sample and link data
                     */
                    $downloadProductId = $product->getId ();
                    $this->assignDataForDownloadableProduct ( $type, $downloadProductId, $store );
                    $msg = Mage::getModel ( 'marketplace/product' )->getMessageForNewProductAdd ();
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $msg );
                    Mage::helper ( 'marketplace/product' )->sentEmailToAdmin ( $sellerId, $product );
                    Mage::app ()->setCurrentStore ( Mage::app ()->getStore ()->getStoreId () );
                    $this->redirectToConfigurablePage ( $type, $productId, $set );
                } catch ( Mage_Core_Exception $e ) {
                    /**
                     * Error message redirect to create new product page
                     */
                    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                    $this->_redirect ( 'marketplace/sellerproduct/create/' );
                }
            } else {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please enter all required fields' ) );
                if ($type == 'configurable') {
                    $this->_redirect ( 'marketplace/sellerproduct/selectattributes/', array (
                            'set' => $set
                    ) );
                }
                $this->_redirect ( 'marketplace/sellerproduct/create' );
            }
        } else {
            /**
             * Error message redirect to create new product page
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'SKU Not Available' ) );
            $this->_redirect ( 'marketplace/sellerproduct/create/' );
        }
    }

    /**
     * Manage Seller Products
     *
     * @return void
     */
    public function manageAction() {
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();

        $this->loadLayout ();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Manage Products'));
        $this->renderLayout ();
    }

    /**
     * Edit Existing Products
     *
     * @return void
     */
    public function editAction() {
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();
        /**
         * Initilize product id , customer id and seller id
         */
        $entityId = ( int ) $this->getRequest ()->getParam ( 'id' );
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        $collection = Mage::getModel ( 'catalog/product' )->load ( $entityId );
        $sellerId = $collection->getSellerId ();
        /**
         * Checking for edit permission
         */
        if ($customerId != $sellerId) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You don't have enough permission to edit this product details." ) );
            $this->_redirect ( 'marketplace/product/manage' );
            return;
        }
        $customerStatus = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
        /**
         * Checking whether customer approved or not
         */
        if ($customerStatus != 1) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Admin Approval is required. Please wait until admin confirm your Seller Account' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return;
        }
        $this->loadLayout ();
        $this->renderLayout ();
    }

    /**
     * Save Edited Products
     *
     * @return void
     */
    public function editpostAction() {
        $isInStock = '';
        Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();
        $productId = $name = $description = $shortDescription = $price = $store = $sku = $storeId = '';
        $categoryIds = $deleteImages = array ();
        $type = $this->getRequest ()->getPost ( 'type' );
        $productData = $this->getRequest ()->getPost ( 'product' );
        $productId = $this->getRequest ()->getPost ( 'product_id' );
        $storeId = $this->getRequest ()->getPost ( 'store_id' );
        $categoryIds = $this->getRequest ()->getPost ( 'category_ids' );
        $store = Mage::app ()->getStore ()->getId ();
        $name = $productData ['name'];
        $sku = $productData ['sku'];
        $description = $productData ['description'];
        $shortDescription = $productData ['short_description'];
        $price = $productData ['price'];
        $deleteImages = $this->getRequest ()->getPost ( 'deleteimages' );
        $baseImage = $this->getRequest ()->getPost ( 'baseimage' );
        $productData['delivery'] = implode(',',$this->getRequest ()->getPost ( 'delivery' ));
        $checkingForProductRequiredFields = Mage::helper ( 'marketplace/market' )->checkingForProductRequiredFields ( $sku, $productId, $name, $description );
        if ($checkingForProductRequiredFields == 1 && ! empty ( $shortDescription ) && isset ( $price ) && ! empty ( $type )) {
            $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
            if (empty ( $productData ['weight'] )) {
                $productData ['weight'] = 0;
            }
            $product = Mage::getModel ( 'marketplace/product' )->setProductDataForUpdate ( $product, $categoryIds, $productData, $type, $isInStock );
            $imagesPath = array ();
            $uploadsData = new Zend_File_Transfer_Adapter_Http ();
            $filesDataArray = $uploadsData->getFileInfo ();
            $imagesPath = Mage::getModel ( 'marketplace/product' )->getProductImagePath ( $filesDataArray );
            /**
             * Adding Product images
             */
            $product = Mage::getModel ( 'marketplace/product' )->setImagesForProduct ( $product, $imagesPath );
            try {


                if ($type == 'downloadable') {
                    $product->setStockData ( array (
                            'use_config_manage_stock' => 0,
                            'is_in_stock' => 1,
                            'manage_stock' => 0,
                            'use_config_notify_stock_qty' => 0
                    ) );
                }

                if (Mage::helper ( 'marketplace/general' )->getCustomAttributeEnableOrNot ()) {
                    $product = Mage::helper ( 'marketplace/general' )->customAttributeSave($product,$productData);
                }

                $product->setStoreId ( $storeId )->save ();
                   /**
                 * Removing product images
                 */

                /**
                 * Function for adding downloadable product sample and link data
                 */
                $downloadProductId = $product->getId ();
                $this->assignDataForDownloadableProduct ( $type, $downloadProductId, $store );
                Mage::app ()->setCurrentStore ( $store );

                /**
                 * Initialise seller language var
                 */
                $sellerDefaultLangId = $this->getRequest()->getPost('seller_product_lang');
                /**
                 * Save product in store view
                 */
                $allStores = Mage::app()->getStores();
                /**
                 * getting each store values
                 */
                 foreach ($allStores as $_eachStoreId => $val){
                    Mage::helper('marketplace/outofstock')->editpostProduct($productData,$_eachStoreId,$productId,$sellerDefaultLangId);
                }
                $allStores = Mage::app()->getStores();
                foreach ($allStores as $_eachStoreId => $val){
                    $_storeId[] = Mage::app()->getStore($_eachStoreId)->getId();
                }
                for($i=0;$i<count($_storeId);$i++){
                    $storeId = $_storeId [$i];

                    if ($productData ['status'] == 1) {
                        Mage::getModel ( 'catalog/product_status' )->updateProductStatus ( $product->getId (), $storeId, Mage_Catalog_Model_Product_Status::STATUS_ENABLED );
                    } else {
                        Mage::getModel ( 'catalog/product_status' )->updateProductStatus ( $product->getId (), $storeId, Mage_Catalog_Model_Product_Status::STATUS_DISABLED );
                    }
                }
                Mage::getModel ( 'marketplace/product' )->deleteProductImagesForEdit($deleteImages, $productId, $baseImage );
                /**
                 * Set product images
                 */
                Mage::getModel ( 'marketplace/product' )->setProductImagesforProduct ( $baseImage, $productId, $store, $product, $productData );

                /**
                 * Checking whether image or not
                 */
                Mage::getModel ( 'marketplace/product' )->deleteTempImageFiles ( $imagesPath );
                /**
                 * Success message redirect to manage product page
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your product details are updated successfully.' ) );
                $this->_redirect ( 'marketplace/product/manage/' );
            } catch ( Mage_Core_Exception $e ) {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                $this->_redirect ( 'marketplace/product/edit/id/' . $productId );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                $this->_redirect ( 'marketplace/product/edit/id/' . $productId );
            }
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Please enter all required fields' ) );
            $this->_redirect ( 'marketplace/product/edit/id/' . $productId );
        }
    }

    /**
     * Delete Seller Products
     *
     * @return void
     */
    public function deleteAction() {
        $entityId = '';
        /**
         * Check whether seller or not
         */
        $this->checkWhetherSellerOrNot ();

        $entityId = ( int ) $this->getRequest ()->getParam ( 'id' );
        $productSellerId = Mage::getModel ( 'catalog/product' )->load ( $entityId )->getSellerId ();

        if (Mage::getSingleton ( 'customer/session' )->getCustomerId () == $productSellerId && Mage::getSingleton ( "customer/session" )->isLoggedIn ()) {
            /**
             * Checking whether customer approved or not
             */
            $this->loadLayout ();
            $this->renderLayout ();

            Mage::register ( 'isSecureArea', true );
            Mage::helper ( 'marketplace/general' )->changeAssignProductId ( $entityId );
            Mage::getModel ( 'catalog/product' )->setId ( $entityId )->delete ();

            /**
             * un set secure admin area
             */
            Mage::unregister ( 'isSecureArea' );
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Product Deleted Successfully" ) );
            $productId = $this->getRequest ()->getParam ( 'pid' );
            $set = $this->getRequest ()->getParam ( 'set' );
            if (! empty ( $productId ) && ! empty ( $set )) {
                $this->_redirect ( 'marketplace/sellerproduct/configurable/', array (
                        'id' => $productId,
                        'set' => $set
                ) );
                return;
            }
            $isAssign = $this->getRequest ()->getParam ( 'is_assign' );
            if (! empty ( $isAssign )) {
                $this->_redirect ( 'marketplace/sellerproduct/manageassignproduct/' );
                return;
            }
            $this->_redirect ( '*/product/manage/' );
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "You don't have enough permission to delete this product details." ) );
            $this->_redirect ( 'marketplace/seller/login' );
        }
    }

    /**
     * Manage Deals products by seller
     *
     * @return void
     */
    public function managedealsAction() {
        $this->loadLayout ();
        $this->getLayout()->getBlock('head')->setTitle($this->__('Manage Deals'));
        $this->renderLayout ();
    }

    /**
     * Manage Deals products by seller
     *
     * @return void
     */
    public function deletesingledealAction() {
        $entityId = $this->getRequest ()->getParam ( 'id' );
        Mage::getModel ( 'catalog/product' )->load ( $entityId )->setSpecialFromDate ( '' )->setSpecialToDate ( '' )->setSpecialPrice ( '' )->save ();
        Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Product Deal Deleted Successfully" ) );
        $this->_redirect ( '*/product/managedeals/' );
        return true;
    }

    /**
     * Function to check availability of sku
     *
     * @return int
     */
    public function checkskuAction() {
        $inputSku = trim ( $this->getRequest ()->getParam ( 'sku' ) );
        $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'sku', $inputSku );
        $count = count ( $collection );
        echo $count;
        return true;
    }

    /**
     * Function to display the view all compare price products
     *
     * @return void
     */
    public function comparesellerpriceAction() {
        $this->loadLayout ();
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'All Sellers' ) );
        $this->renderLayout ();
    }

    /**
     * Check whether seller or not
     */
    public function checkWhetherSellerOrNot() {
        /**
         * Initilize customer and seller group id
         */
        $customerGroupId = $sellerGroupId = $customerStatus = '';
        $customerGroupId = Mage::getSingleton ( 'customer/session' )->getCustomerGroupId ();
        $sellerGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
        $customerStatus = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
        if (! $this->_getSession ()->isLoggedIn () && $customerGroupId != $sellerGroupId) {
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

    /**
     * Assign data to downloadable product
     *
     * @param string $type
     * @param number $downloadProductId
     * @param number $store
     */
    public function assignDataForDownloadableProduct($type, $downloadProductId, $store) {
        if ($type == 'downloadable' && isset ( $downloadProductId ) && isset ( $store )) {
            $this->addDownloadableProductData ( $downloadProductId, $store );
        }
    }
    /**
     * Redirect to configurable page
     *
     * @param string $type
     * @return boolean
     */
    public function redirectToConfigurablePage($type, $productId, $set) {
        if ($type == 'configurable') {
            $this->_redirect ( 'marketplace/sellerproduct/configurable/', array (
                    'id' => $productId,
                    'set' => $set
            ) );
            return;
        } else {
            $this->_redirect ( 'marketplace/product/manage/' );
            return;
        }
    }

    /**
     * Save Downloadable Products
     *
     * Passed the downloadable product id to save files
     *
     * @param int $downloadProductId
     *            Passed the store id to save files
     * @param int $store
     *
     * @return void
     */
    public function addDownloadableProductData($downloadProductId, $store) {
        /**
         * Initilize downloadable product sample and link files
         */
        $sampleTpath = $linkTpath = $slinkTpath = array ();
        $uploadsData = new Zend_File_Transfer_Adapter_Http ();
        $filesDataArray = $uploadsData->getFileInfo ();
        foreach ( $filesDataArray as $key => $result ) {
            $downloadData = Mage::getModel ( 'marketplace/download' )->prepareDownloadProductData ( $filesDataArray, $key, $result );
            if (! empty ( $downloadData ['sample_tpath'] )) {
                $sampleNo = substr ( $key, 7 );
                $sampleTpath [$sampleNo] = $downloadData ['sample_tpath'];
            }
            if (! empty ( $downloadData ['link_tpath'] )) {
                $sampleNo = substr ( $key, 6 );
                $linkTpath [$sampleNo] = $downloadData ['link_tpath'];
            }
            if (! empty ( $downloadData ['slink_tpath'] )) {
                $sampleNo = substr ( $key, 9 );
                $slinkTpath [$sampleNo] = $downloadData ['slink_tpath'];
            }
        }

        /**
         * Getting downloadable product sample collection
         */
        $downloadableSample = Mage::getModel ( 'downloadable/sample' )->getCollection ()->addProductToFilter ( $downloadProductId )->addTitleToResult ( $store );

        Mage::getModel ( 'marketplace/download' )->deleteDownloadableSample ( $downloadableSample );

        /**
         * Getting downloadable product link collection
         */
        $downloadableLink = Mage::getModel ( 'downloadable/link' )->getCollection ()->addProductToFilter ( $downloadProductId )->addTitleToResult ( $store );

        Mage::getModel ( 'marketplace/download' )->deleteDownloadableLinks ( $downloadableLink );

        /**
         * Initilize Downloadable product data
         */
        $downloadableData = $this->getRequest ()->getPost ( 'downloadable' );
        try {
            /**
             * Storing Downloadable product sample data
             */
            Mage::getModel ( 'marketplace/download' )->saveDownLoadProductSample ( $downloadableData, $downloadProductId, $sampleTpath, $store );

            /**
             * Storing Downloadable product sample data
             */
            if (isset ( $downloadableData ['link'] )) {
                Mage::getModel ( 'marketplace/download' )->saveDownLoadProductLink ( $downloadableData, $downloadProductId, $linkTpath, $slinkTpath, $store );
            }
        } catch ( Exception $e ) {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
        }
    }
    /**
     * Function for sorting the sub level categories.
     * Sort the categories in alphabatical order
     */
    public function alphabaticalOrderAction() {
        /**
         * Get values from post data from ajax request for showing the sub level categories
         */
        $categoryId = json_decode ( trim ( $this->getRequest ()->getPost ( 'selectedCatIds' ) ) );
        $key = trim ( $this->getRequest ()->getPost ( 'cat' ) );
        $categories = Mage::getModel ( 'catalog/category' )->getCategories ( $key );
        foreach ( $categories as $category ) {
            $catId = $category->getId ();
            /**
             * Condition to check for sub category
             */
            if ($category->hasChildren ()) {
                $catId = $category->getId () . 'sub';
            }
            $customerName [$catId] = $category->getName ();
        }
        /**
         * Sort in alphabatical order.
         */
        asort ( $customerName );
        return $this->show_categories_tree ( $customerName, $categoryId );
    }
    /**
     * Getting store categories list
     *
     * Passed category information as array
     *
     * @param array $categories
     *            Return the category tree array
     * @return array
     */
    public function show_categories_tree($customerName, $categoryId) {
        $array = '<ul class="category_ul">';
        foreach ( $customerName as $key => $catagoryName ) {

            $cat = Mage::helper ( 'marketplace/common' )->getCategoryData ( $key );
            $count = $cat->getProductCount ();
            /**
             * Condition to check if sub string is present , if so the string is replaced.
             */
            if (strstr ( $key, 'sub' )) {
                $key = str_replace ( 'sub', '', $key );
                $catChecked = Mage::helper('marketplace/outofstock')->checkSelectedCategory ( $key, $categoryId );
                $array .= '<li class="level-top  parent" id="' . $key . '"><a href="javascript:void(0);"><span class="end-plus" id="' . $key . '"></span></a><span class="last-collapse"><input id="cat' . $key . '" type="checkbox" name="category_ids[]"' . $catChecked . ' value="' . $key . '"><label for="cat' . $key . '">' . $catagoryName . '<span>(' . $count . ')</span>' . '</label></span>';
            } else {
                $catChecked = Mage::helper('marketplace/outofstock')->checkSelectedCategory ( $key, $categoryId );

                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="empty_space"></span></a><input id="cat' . $key . '" type="checkbox" name="category_ids[]"' . $catChecked . ' value="' . $key . '"><label for="cat' . $key . '">' . $catagoryName . '<span>(' . $count . ')</span>' . '</label>';
            }
        }
        $array .= '</li>';
        echo $array . '</ul>';
    }


    /**
     * Update Quantity
     * @param int $id
     * Passed the product id to save the quantity
     * @param int $qty
     * @return void
     */
    public function quantityeditAction() {
    Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
    /**
     * Check whether seller or not
     */
    $this->checkWhetherSellerOrNot ();
    $id = $this->getRequest ()->getParam ( 'id' );
    $qty = $this->getRequest ()->getParam ( 'qty' );
    $product = Mage::getModel ( 'catalog/product' )->load($id);
    $stockData = $product->getStockData();
    $stockData ['qty'] = $qty;
    $stockData ['is_in_stock'] = '1';
    $product->setStockData ( $stockData )->save();
    }
    /**
     * To update seller default language
     */
    public function updatelangAction(){
    $productId = $this->getRequest()->getPost('productId');
    $sellerId = $this->getRequest()->getPost('sellerId');
    $storeId = $this->getRequest()->getPost('seller_product_lang');
    /**
     * update seller default language
     */
    $defaultLangData = Mage::getModel('marketplace/sellerlanguage')->load($productId,'product_id');
    if($defaultLangData->getData()){
    $defaultLangData->setData('seller_id',$sellerId)
    ->setData('store_id',$storeId)
    ->setData('created_at',strtotime ( 'now' ))
    ->save();
    }else{
    Mage::getModel('marketplace/sellerlanguage')->setData('seller_id',$sellerId)
    ->setData('store_id',$storeId)
    ->setData('product_id',$productId)
    ->setData('created_at',strtotime ( 'now' ))
    ->save();
    }
    $this->_redirect ( 'marketplace/product/edit',array('id'=>$productId,'_secure'=>true));
    }
}