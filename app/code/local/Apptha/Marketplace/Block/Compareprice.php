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
 * Compare Product Prices
 * This file is used to compare seller product price with others seller products
 */
class Apptha_Marketplace_Block_Compareprice extends Mage_Core_Block_Template {
   /**
     * Get Product Collection with 'compare_product_id' attribute filter
     *
     * Passed the product id for which we need to compare price
     *
     * @param int $productId
     *            Return product collection as array
     * @return array
     */
    public function getComparePrice($productId) {
    
    /**
     * Load Product collection
     * Filter by visibility,
     * assign product id,
     * entity id
     */
        $productCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'is_assign_product', array (
                'eq' => 1 
        ) )->addAttributeToFilter ( 'visibility', array (
                'eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        ) )->addFieldToFilter ( 'assign_product_id', array (
                'eq' => $productId 
        ) )->addFieldToFilter ( 'entity_id', array (
                'neq' => $productId 
        ) );
        /**
         * load product object
         * @var product id
         */
        $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
        /**
         * Check whether product type is configurable
         */
        if ($product->getTypeId () == 'configurable') {
            $productCollection->addAttributeToFilter ( 'type_id', array (
                    'eq' => 'configurable' 
            ) );
        } else {
        /**
         * Set order by ascending order
         */
            $productCollection->setOrder ( 'price', 'ASC' );
        }
        return $productCollection;
    }
    
    /**
     * Get Review Collection of the particular seller
     *
     * Passed the seller id to get the review collection
     *
     * @param int $sellerId
     *            Return the reviews count of particular seller
     * @return int
     */
    public function getReviewsCount($sellerId) {
    /**
     * Get Store id
     * 
     */
        $storeId = Mage::app ()->getStore ()->getId ();
        /**
         * Get Review Collection
         * Filter by status,store id
         */
        $reviewsCollection = Mage::getModel ( 'marketplace/sellerreview' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'status', 1 )->addFieldToFilter ( 'store_id', $storeId );
       /**
         *Return  review collection size 
         */
        return $reviewsCollection->getSize ();
    }
    
    /**
     * Calculating average rating for each seller
     *
     * Passed the seller id to get the review collection
     *
     * @param int $sellerId
     *            Return the average rating of particular seller
     * @return int
     */
    public function averageRatings($sellerId) {
        /**
         * Review Collection to retrive the ratings of the seller
         */
        $storeId = Mage::app ()->getStore ()->getId ();
        $reviews = Mage::getModel ( 'marketplace/sellerreview' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'status', 1 )->addFieldToFilter ( 'store_id', $storeId );
        /**
         * Calculate average ratings
         */
        $ratingsVal = array ();
        $avg = 0;
        /**
         * Check whether review count is empty or not
         */
        if (count ( $reviews ) > 0) {
        /**
         * foreach increment loop
         */
            foreach ( $reviews as $review ) {
                $ratingsVal [] = $review->getRating ();
            }
            /**
             * Calcualte count of ratings
             */
            $count = count ( $ratingsVal );
            /**
             * Calculate average ratings from count
             */
            $avg = array_sum ( $ratingsVal ) / $count;
        }
        return round ( $avg, 1 );
    }
}