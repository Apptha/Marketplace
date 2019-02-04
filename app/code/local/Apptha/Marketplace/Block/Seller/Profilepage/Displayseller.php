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
 * Seller information
 * This class used to display the seller information
 * Display the seller products
 * Display review form
 * Display the Ratings
 * Display the reviews those have been already submitted by users
 */
class Apptha_Marketplace_Block_Seller_Profilepage_Displayseller extends Mage_Core_Block_Template {
    /**
     * Function to load layout
     *
     * @return array
     */
    protected function _prepareLayout() {
        $id = $this->getRequest ()->getParam ( 'id' );
        $limit = $this->getRequest ()->getParam ( 'limit' );
        $sellerPage = Mage::getModel ( 'marketplace/sellerprofile' )->collectprofile ( $id );
        /**
         * set Meta information for the seller
         */
        $head = $this->getLayout ()->getBlock ( 'head' );
        /**
         * get store title
         */
        $head->setTitle ( $sellerPage->getStoreTitle () );
        /**
         * get meta keyword
         */
        $head->setKeywords ( $sellerPage->getMetaKeyword () );
        /**
         * set description
         */
        $head->setDescription ( $sellerPage->getMetaDescription () );
        /**
         * get category products
         */
        $displayCollection = $this->categoryProducts ();
        /**
         * Set Collection
         */
        $this->setCollection ( $displayCollection );
        /**
         * Get layout
         */
        $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $displayCollection );
        /**
         * Setting avialable limit for the pager
         */
        $pager->setAvailableLimit ( array (
                10 => 10,
                20 => 20,
                30 => 30,
                50 => 50 
        ) );
        $pager->setLimit ( $limit );
        /**
         * Set child
         */
        $this->setChild ( 'pager', $pager );
        return $this;
    }
    /**
     * Function to get the collection according to pagination
     *
     * Return the Seller product collection
     *
     * @return array
     */
    public function getPagerHtml() {
        /**
         * get childhtml
         */
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Function to get the Seller products
     *
     * Return the Seller product collection
     *
     * @return array
     */
    function sellerproduct($sellerid) {
        /**
         * Get Product Collection
         */
        $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerid )->joinField ( 'category_id', Mage::getConfig ()->getTablePrefix () . 'catalog_category_product', 'category_id', 'product_id=entity_id', null, 'right' );
        return $collection->getData ( 'category_id' );
    }
    
    /**
     * Get category products
     *
     * Return the category product collection
     *
     * @return array
     */
    function categoryProducts() {
        /**
         * Get Category Name
         */
        $displayCatProduct = $this->getRequest ()->getParam ( 'category_name' );
        /**
         * Get Sorting Detail
         */
        $sortProduct = $this->getRequest ()->getParam ( 'sorting' );
        /**
         * Get Id
         */
        $id = $this->getRequest ()->getParam ( 'id' );
        /**
         * Get Category Collection
         */
        $catagoryModel = Mage::getModel ( 'catalog/category' )->load ( $displayCatProduct );
        $collection = Mage::getResourceModel ( 'catalog/product_collection' );
        $collection->addCategoryFilter ( $catagoryModel );
        /**
         * Filter by Status
         */
        $collection->addAttributeToFilter('status',1);
        /**
         * Filter By all
         */
        $collection->addAttributeToSelect ( '*' );
        /**
         * Filter by seller id
         */
        $collection->addAttributeToFilter ( 'seller_id', $id );
        /**
         * Filter by visibilty
         */
        $collection->addAttributeToFilter ( 'visibility', array (1,
                2,
                3,
                4 
        ) ); 
        
         $collection->getSelect()->joinLeft(array('link_table' => 'catalog_product_super_link'),
        'link_table.product_id = e.entity_id',
        array('product_id')
        );
        $collection->getSelect()->where('link_table.product_id IS NULL'); 
        $collection->addStoreFilter ();
        $collection->addAttributeToSort ( $sortProduct );
        
        
        
        return $collection;
    }
    
    /**
     * Get category Url
     *
     * Return the category link url
     *
     * @return string
     */
    function getCategoryUrl($customerId, $id) {
        return Mage::getUrl ( 'marketplace/seller/categorylist', array (
                'id' => $id,
                'cat' => $customerId 
        ) );
    }
    
    /**
     * Get url for review form
     *
     * Passed the seller id to get the review collection
     *
     * @param int $id
     *            Customer id is passed to get the particular customer reviews
     * @param int $customerId
     *            Product id is passed to get the particular products reviews
     * @param int $productId
     *            Return the average rating of particular seller
     * @return int
     */
    function reviewUrl($customerId, $id, $productId) {
        return Mage::getUrl ( 'marketplace/seller/reviewform', array (
                'id' => $id,
                'cus' => $customerId,
                'product' => $productId 
        ) );
    }
    
    /**
     * Get login url if customer not logged in
     *
     * Return the customer login url
     *
     * @return string
     */
    function loginUrl() {
        return Mage::getUrl ( 'customer/account/login/' );
    }
    
    /**
     * Get all reviews link
     *
     * Passed the seller id to get the review collection
     *
     * @param int $id
     *            Customer id is passed to get the particular customer reviews
     * @param int $customerId
     *            Product id is passed to get the particular products reviews
     * @param int $productId
     *            Return the average rating of particular seller
     * @return int
     */
    function getAllreview($customerId, $id, $productId) {
        return Mage::getUrl ( 'marketplace/sellerreview/allreview', array (
                'id' => $id,
                'cus' => $customerId,
                'product' => $productId 
        ) );
    }
    /**
     * Calculating average rating for each seller
     *
     * Passed the seller id to get the review collection
     *
     * @param int $id
     *            Return the average rating of particular seller
     * @return int
     */
    public function averageRatings($id) {
        /**
         * Review Collection to retrive the ratings of the seller
         */
        /**
         * Get Store ID
         */
        $storeId = Mage::app ()->getStore ()->getId ();
        $reviews = Mage::getModel ( 'marketplace/sellerreview' )->getCollection ()->addFieldToFilter ( 'seller_id', $id )->addFieldToFilter ( 'status', 1 )->addFieldToFilter ( 'store_id', $storeId );
        /**
         * Calculate average ratings
         */
        $ratings = array ();
        $avg = 0;
        if (count ( $reviews ) > 0) {
            foreach ( $reviews as $review ) {
                $ratings [] = $review->getRating ();
            }
            $count = count ( $ratings );
            $avg = array_sum ( $ratings ) / $count;
        }
        return $avg;
    }
    public function reviewCount($id) {
        /**
         * Review Collection to retrive the ratings of the seller
         */
        /**
         * Get Store Id
         */
        $storeId = Mage::app ()->getStore ()->getId ();
        /**
         * Get Marketplace Review
         */
        $reviews = Mage::getModel ( 'marketplace/sellerreview' )->getCollection ()->addFieldToFilter ( 'seller_id', $id )->addFieldToFilter ( 'status', 1 )->addFieldToFilter ( 'store_id', $storeId );
        return count ( $reviews );
    }
    /**
     * Best seller product Collection
     *
     * @return array
     */
    public function bestSellerCollection($sellerId) {
        /**
         * get store id
         */
        $storeId = Mage::app ()->getStore ()->getId ();
        /**
         * get reports based on the seller
         */
        $_productCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->setDisableFlat ( true )->addAttributeToSelect ( '*' )->addFieldToFilter ( "seller_id", $sellerId )->load ();
        $productId = array ();
        foreach ( $_productCollection as $proColletion ) {
            $productId [] = $proColletion ['entity_id'];
        }
        /**
         * get reports colletion for Most viewed product
         */
        $products = Mage::getResourceModel ( 'reports/product_collection' )->addAttributeToSelect ( '*' )->addAttributeToSelect ( array (
                'name',
                'price' 
        ) )->setStoreId ( $storeId )->addStoreFilter ( $storeId )->addViewsCount ()->addAttributeToFilter ( "entity_id", array (
                'in' => $productId 
        ) );
        
       	Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
        Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $products );
        $configValueStockStatus = Mage::getStoreConfig('cataloginventory/options/show_out_of_stock', $storeId);
        if($configValueStockStatus == 0){
        Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($products);
        }
        
        $products->setPageSize ( 5 )->setCurPage ( 1 );
        
        return $products;
    }
    /**
     * Contact seller url action
     */
    public function getContactSellerUrl($id) {
        /**
         * redirect url
         */
        return Mage::getUrl ( 'marketplace/seller/contactseller', array (
                'id' => $id 
        ) );
    }
} 
