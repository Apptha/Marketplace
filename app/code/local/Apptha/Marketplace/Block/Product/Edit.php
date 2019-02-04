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
 * Edit product information
 */
class Apptha_Marketplace_Block_Product_Edit extends Mage_Core_Block_Template {
    /**
     * Initilize layout and set page title
     *
     * Return the page title
     *
     * @return varchar
     */
    protected function _prepareLayout() {
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper ( 'marketplace' )->__ ( 'Edit Product' ) );
        return parent::_prepareLayout ();
    }
    /**
     * Product edit action
     *
     * Return edit post action url
     *
     * @return string
     */
    public function editProductAction() {
        return Mage::getUrl ( 'marketplace/product/editpost' );
    }
    /**
     * Get product data collection
     *
     * Passed the product id to get product details
     *
     * @param int $productId
     * Return product details as array
     * @return obj
     */
    public function getProductData($productId, $storeId) {
    /**
      * return product object
    */
  return  Mage::getModel('catalog/product')
          ->setStoreId($storeId)
          ->load($productId);
    }
    /**
     * Getting selected product categories id
     *
     * Category data are passed to display the category id
     *
     * @param array $categoryArray
     *            Return category id
     * @return array
     */
    public function getCategoryIds($categoryArray) {
        $categoryIds = array ();
        foreach ( $categoryArray as $key ) {
            foreach ( $key as $value ) {
                $categoryIds [] = $value;
            }
        }
        return $categoryIds;
    }
    /**
     * Sort the categories in alphabatical order
     */
    public function alphabaticalOrder($categories, $categoryid) {
        $array = array ();
        $customerName = array ();
        foreach ( $categories as $category ) {
            $catagoryId = $category->getId ();
            /**
             * Checking category has children
             */
            if ($category->hasChildren ()) {
                $catagoryId = $category->getId () . 'sub';
            }
            $customerName [$catagoryId] = $category->getName ();
        }
        /**
         * Sorting in alphabatical order
         */
        asort ( $customerName );
        
        return $array = $this->show_categories_tree ( $customerName, $categoryid );
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
    public function show_categories_tree($customerName, $categoryid) {
        $array = '<ul class="category_ul">';
        /**
         * Increment foreach loop
         */
        foreach ( $customerName as $key => $catname ) {
            $catChecked = '';
            $catagory = Mage::helper ( 'marketplace/common' )->getCategoryData ( $key );
            $count = $catagory->getProductCount ();
            /**
             * Condition to check if sub string is present , if so the string is replaced.
             */
            if (strstr ( $key, 'sub' )) {
                $key = str_replace ( 'sub', '', $key );
                $catChecked = $this->checkSelectedCategory ( $key, $categoryid );
                $array .= '<li class="level-top  parent" id="' . $key . '"><a href="javascript:void(0);"><span class="end-plus" id="' . $key . '"></span></a><span class="last-collapse"><input id="cat' . $key . '" type="checkbox" name="category_ids[]"' . $catChecked . ' value="' . $key . '"><label for="cat' . $key . '">' . $catname . '<span>(' . $count . ')</span>' . '</label></span>';
            } else {
                $catChecked = $this->checkSelectedCategory ( $key, $categoryid );
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="empty_space"></span></a><input id="cat' . $key . '" type="checkbox" name="category_ids[]"' . $catChecked . ' value="' . $key . '"><label for="cat' . $key . '">' . $catname . '<span>(' . $count . ')</span>' . '</label>';
            }
        }
        $array .= '</li>';
        return $array . '</ul>';
    }
    /**
     * Function to get the selected category
     *
     * @param array $key            
     * @param array $categoryid            
     */
    public function checkSelectedCategory($key, $categoryid) {
        /**
         * Condition to check the selected category
         */
        $catChecked = '';
        if (in_array($key,$categoryid)) {
        $catChecked = 'checked';
        }  
        return $catChecked;
    }
}