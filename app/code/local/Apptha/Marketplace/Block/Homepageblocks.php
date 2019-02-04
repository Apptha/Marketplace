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
 * This file is used display the banner slider in home page
 */
class Apptha_Marketplace_Block_Homepageblocks extends Mage_Core_Block_Template {
    /**
     * Function to get quick view category links
     *
     * Return the quick view category collection as array
     *
     * @return array
     */
    public function getQuickview() {
        /**
         * getting category collection
         */
        $categoryCollection = Mage::getModel ( 'catalog/category' )->getCollection ();
        /**
         * getting quick view
         */
        $categoryCollection->addAttributeToFilter ( 'quick_view', '1' );
        /**
         * getting include in menu
         */
        $categoryCollection->addAttributeToFilter ( 'include_in_menu', array (
                'eq' => 1 
        ) );
        /**
         * category collection is active
         */
        $categoryCollection->addAttributeToFilter ( 'is_active', array (
                'eq' => '1' 
        ) );
        $categoryCollection->addAttributeToSelect ( '*' );
        /**
         * setting pagination
         */
        $categoryCollection->setPageSize ( 10 )->setCurPage ( 1 );
        return $categoryCollection;
    }
    
    /**
     * Function to get product collection
     *
     * This Function will return the product collection
     *
     * @return array
     */
    public function getProductCollection() {
        $_productCollection = Mage::getResourceModel ( 'catalogsearch/advanced_collection' )->addAttributeToSelect ( Mage::getSingleton ( 'catalog/config' )->getProductAttributes () )->addMinimalPrice ()->addStoreFilter ();
        Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $_productCollection );
        /**
         * Filter by visibility
         */
        Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInSearchFilterToCollection ( $_productCollection );
        /**
         * todays date
         */
        $todayDate = date ( 'm/d/y' );
        /**
         * Tomorrow
         */
        $tomorrow = mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ), date ( 'y' ) );
        $tomorrowDate = date ( 'm/d/y', $tomorrow );
        /**
         * Getting Product Collection
         */
        $_productCollection->addAttributeToFilter ( 'special_from_date', array (
                'date' => true,
                'to' => $todayDate 
        ) )->addAttributeToFilter ( 'special_to_date', array (
                'or' => array (
                        0 => array (
                                'date' => true,
                                'from' => $tomorrowDate 
                        ),
                        1 => array (
                                'is' => new Zend_Db_Expr ( 'null' ) 
                        ) 
                ) 
        ), 'left' );
        
        $_productCollection->getSelect()->order(new Zend_Db_Expr('RAND()'));
        $_productCollection->getSelect()->limit(20);
        return $_productCollection;
    }
    
    /**
     * Function to get product parent id
     *
     * This Function will return the product parent id
     *
     * @return int
     */
    public function getParentId($product) {
        $parentId = '';
        /**
         * getting visibility
         */
        if ($product->getVisibility () != '1') {
            /**
             * getting parent id
             */
            $parentId = $product->getId ();
        } else {
            /**
             * get parent id if the product is not visible
             * this means that the product is associated with a configurable product
             */
            $parentIdArray = $product->loadParentProductIds ()->getData ( 'parent_product_ids' );
            if (! empty ( $parentIdArray )) {
                $parentId = $parentIdArray [0];
            }
        }
        return $parentId;
    }
    /**
     * Function to get category listings links
     *
     * Return the category listings links collection as array
     *
     * @return array
     */
    public function getSubCategoryListings() {
        /**
         * getting category collection
         */
        $categoryCollection = Mage::getModel ( 'catalog/category' )->getCollection ();
        $categoryCollection->addAttributeToFilter ( 'sub_category_listings', '1' );
        /**
         * include in menu
         */
        $categoryCollection->addAttributeToFilter ( 'include_in_menu', array (
                'eq' => 1 
        ) );
        $categoryCollection->addAttributeToFilter ( 'is_active', array (
                'eq' => '1' 
        ) );
        /**
         * Add Attribute to Select
         */
        $categoryCollection->addAttributeToSelect ( '*' );
        /**
         * Set Page Size
         */
        $categoryCollection->setPageSize ( 10 )->setCurPage ( 1 );
        return $categoryCollection;
    }
    /**
     * Get sub category collection of the particular category
     *
     * @return array
     */
    public function getSubCategories($categoryId) {
        $subCatId = array ();
        /**
         * Get Categories
         */
        $children = Mage::getModel ( 'catalog/category' )->getCategories ( $categoryId );
        foreach ( $children as $_children ) {
            $subCatId [] = $_children->getId ();
        }
        return $subCatId;
    }
    /**
     * Load category information
     *
     * @return array
     */
    public function loadCategoryInfo($categoryId) {
        /**
         * load category id
         */
        return Mage::getModel ( 'catalog/category' )->load ( $categoryId );
    }
    /**
     * Best seller product Collection
     *
     * @return array
     */
    public function bestSellerCollection() {
       $storeId = Mage::app ()->getStore ()->getId ();
        if(Mage::getStoreConfigFlag('catalog/frontend/flat_catalog_product')) {
        
        $products = Mage::getResourceModel ( 'reports/product_collection' )->addOrderedQty ()->setStoreId ( $storeId )->addStoreFilter ( $storeId )->setOrder ( 'ordered_qty', 'desc' );
        $prefix = Mage::getConfig ()->getTablePrefix ();
        $products->getSelect ()->joinInner ( array (
                'e2' => $prefix . 'catalog_product_flat_' . $storeId 
        ), 'e2.entity_id = e.entity_id' );
        
        Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
        Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $products );
        } else {
        $visibility = array (Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
        /**
         * Get Product Collection
         * Filter by name,small image,orderered qty,status
         */
        $products =  Mage::getResourceModel('reports/product_collection')->addOrderedQty()->addAttributeToSelect ( '*' )->addAttributeToSelect ( array (
        'name',
        'price',
        'small_image'
        ) )->setStoreId ( $storeId )->addStoreFilter ( $storeId )->addAttributeToFilter('visibility',$visibility )->addAttributeToFilter ( 'status', array (
        'eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED
        ) )->setOrder ( 'ordered_qty', 'desc' );
        }
        /**
         * Filter by in stock
         * @var unknown
         */
        $configValueStockStatus = Mage::getStoreConfig('cataloginventory/options/show_out_of_stock', $storeId);
        if($configValueStockStatus == 0){
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
        }
       return $products;
    }
    /**
     * Function to get the new products
     *
     * Return New product collection as array
     *
     * @return array
     */
    public function getNewproduct() {
        /**
         * getting store id
         */
        $storeId = Mage::app ()->getStore ()->getId ();
        /**
         * todays date
         */
        $visibility = array (
        Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH
        );
        $todayDate = Mage::app ()->getLocale ()->date ()->toString ( Varien_Date::DATETIME_INTERNAL_FORMAT );
        /**
         * Load product Collection
         * @var obj
         */
        $newProductCollection =  Mage::getModel ( 'catalog/product' )->getCollection ()->addStoreFilter ( $storeId )->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'news_from_date', array (
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
        ), 'left' )->addAttributeToSort ( 'entity_id', 'desc' )->addAttributeToFilter ( 'status', array (
                'eq' => 1 
        ) );
        /**
         * Filter by visibility
         */
       $newProductCollection->addAttributeToFilter ( 'visibility', $visibility );
        /**
         * Check whether product is in stock or not
         * @var unknown
         */
        $configValueStockStatus = Mage::getStoreConfig('cataloginventory/options/show_out_of_stock', $storeId);
        if($configValueStockStatus == 0){
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($newProductCollection);
        }
       return $newProductCollection;
    }
}