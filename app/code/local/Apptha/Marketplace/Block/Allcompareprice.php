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
 * Compare products price
 * This file is used to compare seller product price with others seller products
 */
class Apptha_Marketplace_Block_Allcompareprice extends Mage_Core_Block_Template {
    /**
     * Function to Load Layout
     */
    protected function _prepareLayout() {
        parent::_prepareLayout ();
        $productId = $this->getRequest ()->getParam ( 'id' );
        if (is_numeric ( $productId ) || $productId == '') {
            $collection = $this->getComparePrice ( $productId );
            $this->setCollection ( $collection );
            $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $collection );
            /**
             * Set pager available limit
             */
            $pager->setAvailableLimit ( array (
                    10 => 10,
                    20 => 20,
                    30 => 30,
                    50 => 50 
            ) );
            $pager->setLimit ( 10 );
            $this->setChild ( 'pager', $pager );
        }
        return $this;
    }
    /**
     * Function to get Pagination
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Get Product Collection with 'compare_product_id' attribute filter
     *
     * Passed the product id for which we need to compare price
     *
     * @param int $productId
     *            Return the product collection as array
     * @return array
     */
    public function getComparePrice($productId) {
        $products = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'is_assign_product', array (
                'eq' => 1 
        ) )->addAttributeToFilter ( 'visibility', array (
                'eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        ) )->addFieldToFilter ( 'assign_product_id', array (
                'eq' => $productId 
        ) );
        $products->setOrder ( 'price', 'ASC' );
        return $products;
    }
    
    /**
     * Get Review Collection of the particular seller
     *
     * Passed the seller id to get the review collection
     *
     * @param int $sellerId
     *            Return the seller reviews count
     * @return int
     */
    public function getReviewsCount($sellerId) {
        $storeId = Mage::app ()->getStore ()->getId ();
        $reviewsCollection = Mage::getModel ( 'marketplace/sellerreview' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToFilter ( 'status', 1 )->addFieldToFilter ( 'store_id', $storeId );
        return $reviewsCollection->getSize ();
    }
    
    /**
     * Calculating average rating for each seller
     *
     * Passed the seller id to get the review collection
     *
     * @param int $sellerId
     *            Return the average ratings
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
        $ratings = array ();
        $avg = 0;
        if (count ( $reviews ) > 0) {
            foreach ( $reviews as $review ) {
                $ratings [] = $review->getRating ();
            }
            $count = count ( $ratings );
            $avg = array_sum ( $ratings ) / $count;
        }
        return round ( $avg, 1 );
    }
}