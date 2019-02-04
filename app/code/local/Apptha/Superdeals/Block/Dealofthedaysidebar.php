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
 * This Class is used to display the Deal Of the day products
 */
class Apptha_Superdeals_Block_Dealofthedaysidebar extends Mage_Catalog_Block_Product_Abstract {
    
    /**
     * Function to get product limit
     *
     * This Function will return the product count
     * 
     * @return int
     */
    public function getProductsLimit() {
        return 1;
    }
    
    /**
     * Function to get product initial collection
     *
     * This Function will return the product collection
     * 
     * @return array
     */
    public function getProductCollectionInitial() {
        /**
         * get store id
         */
        $storeId = Mage::app ()->getStore ()->getId ();
        /**
         * get reports based on the seller
         */
        $currentDate = Mage::app ()->getLocale ()->date ()->toString ( Varien_Date::DATETIME_INTERNAL_FORMAT );
        $tomorrow = mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + 1, date ( 'y' ) );
        $dateTomorrow = date ( 'm/d/y', $tomorrow );
        $_productCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addAttributeToFilter ( 'special_price', array (
                'neq' => '' 
        ) )->addAttributeToFilter ( 'special_from_date', array (
                'date' => true,
                'to' => $currentDate 
        ) )->addAttributeToFilter ( 'special_to_date', array (
                'or' => array (
                        0 => array (
                                'date' => true,
                                'from' => $dateTomorrow 
                        ),
                        1 => array (
                                'is' => new Zend_Db_Expr ( 'null' ) 
                        ) 
                ) 
        ), 'left' )->addAttributeToFilter ( 'status', '1' )->load ();
        
        $productId = array ();
        foreach ( $_productCollection as $proColletion ) {
            $productId [] = $proColletion ['entity_id'];
        }
        /**
         * get reports colletion for Most viewed product
         */
        $storeId = Mage::app ()->getStore ()->getId ();
        $products = Mage::getResourceModel ( 'reports/product_collection' )->addOrderedQty ()->setStoreId ( $storeId )->addStoreFilter ( $storeId )->addAttributeToFilter ( "entity_id", array (
                'in' => $productId 
        ) );
        $prefix = Mage::getConfig ()->getTablePrefix ();
        
        Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
        Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $products );
        return $products;
    }
    
    /**
     * Function to get product random collection
     *
     * This Function will return the product collection
     * 
     * @return array
     */
    public function getProductCollectionRandom() {
        $currentDate = Mage::app ()->getLocale ()->date ()->toString ( Varien_Date::DATETIME_INTERNAL_FORMAT );
        $tomorrow = mktime ( 0, 0, 0, date ( 'm' ), date ( 'd' ) + 1, date ( 'y' ) );
        $dateTomorrow = date ( 'm/d/y', $tomorrow );
        $products = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'special_price', array (
                'neq' => '' 
        ) )->addAttributeToFilter ( 'special_from_date', array (
                'date' => true,
                'to' => $currentDate 
        ) )->addAttributeToFilter ( 'special_to_date', array (
                'or' => array (
                        0 => array (
                                'date' => true,
                                'from' => $dateTomorrow 
                        ),
                        1 => array (
                                'is' => new Zend_Db_Expr ( 'null' ) 
                        ) 
                ) 
        ), 'left' );
        Mage::getSingleton ( 'catalog/product_status' )->addVisibleFilterToCollection ( $products );
        Mage::getSingleton ( 'catalog/product_visibility' )->addVisibleInCatalogFilterToCollection ( $products );
        
        return $products;
    }
    
    /**
     * Function to get product collection
     *
     * This Function will return the product collection
     * 
     * @return array
     */
    public function getProductCollection() {
        $storeId = Mage::app ()->getStore ()->getId ();
        $productCollection = $this->getProductCollectionInitial ();
        $count = count ( $productCollection );
        if ($count <= 0) {
            $productCollection = $this->getProductCollectionRandom ();
        }
        $sameProduct = array ();
        $checkedProducts = new Varien_Data_Collection ();
        foreach ( $productCollection as $prod ) {
            $parentId = $this->getParentId ( $prod );
            /**
             * Check the parent id is not equal to empty
             */
            if ($parentId == '') {
                continue;
            }
            $productDetail = Mage::getModel ( 'catalog/product' )->setStoreId ( $storeId )->load ( $this->getParentId ( $prod ) );
            
            /**
             * if the product is not visible or is disabled
             */
            if (! $productDetail->isVisibleInCatalog ()) {
                continue;
            }
            
            /**
             * if two or more simple products of the same configurable product are ordered
             */
            if (in_array ( $productDetail->getId (), $sameProduct )) {
                continue;
            }
            $sameProduct [] = $productDetail->getId ();
            if (! $checkedProducts->getItemById ( $parentId )) {
                $checkedProducts->addItem ( $productDetail );
            }
            if (count ( $checkedProducts ) >= $this->getProductsLimit ()) {
                break;
            }
        }
        return $checkedProducts;
    }
    
    /**
     * Function to get product parent id
     *
     * This Function will return the product parent id
     * 
     * @return int
     */
    public function getParentId($productDetail) {
        $parentId = '';
        /**
         * checking the product visibility is not equal to 1
         */
        if ($productDetail->getVisibility () != '1') {
            $parentId = $productDetail->getId ();
        } else {
            /**
             * get parent id if the product is not visible
             * this means that the product is associated with a configurable product
             */
            $parentIdArray = $productDetail->loadParentProductIds ()->getData ( 'parent_product_ids' );
            /**
             * checking the parent id is not empty
             */
            if (! empty ( $parentIdArray )) {
                $parentId = $parentIdArray [0];
            }
        }
        return $parentId;
    }
}
