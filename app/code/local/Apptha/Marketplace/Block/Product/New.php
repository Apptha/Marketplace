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
 * New product
 * This Class is used for add new product functionality
 */
class Apptha_Marketplace_Block_Product_New extends Mage_Core_Block_Template {
    
    /**
     * Initilize layout and set page title
     *
     * Return the page title
     *
     * @return varchar
     */
    protected function _prepareLayout() {
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( Mage::helper ( 'marketplace' )->__ ( 'New Product' ) );
        return parent::_prepareLayout ();
    }
    
    /**
     * New product add action
     *
     * Return new product add action url
     *
     * @return string
     */
    public function addProductAction() {
        return Mage::getUrl ( 'marketplace/product/newpost' );
    }
    
    /**
     * Getting website id
     *
     * Return the product website id
     *
     * @return int
     */
    public function getWebsiteId() {
        return Mage::app ()->getStore ( true )->getWebsite ()->getId ();
    }
    
    /**
     * Getting store id
     *
     * Return product store id
     *
     * @return int
     */
    public function getStoreId() {
        return Mage::app ()->getStore ()->getId ();
    }
    
    /**
     * Getting attributeset id
     *
     * Return the product attribute set id
     *
     * @return int
     */
    public function getAttributeSetId() {
        return Mage::getModel ( 'catalog/product' )->getResource ()->getEntityType ()->getDefaultAttributeSetId ();
    }
    
    /**
     * Sort the categories in alphabatical order
     */
    public function alphabaticalOrder($categories) {
        $array = array ();
        $customerName = array ();
        foreach ( $categories as $category ) {
            $catagoryId = $category->getId ();
            if ($category->hasChildren ()) {
                $catagoryId = $category->getId () . 'sub';
            }
            $customerName [$catagoryId] = $category->getName ();
        }
        asort ( $customerName );
        
        return $array = $this->show_categories_tree ( $customerName );
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
    public function show_categories_tree($customerName) {
        $array = '<ul class="category_ul">';
        foreach ( $customerName as $key => $catname ) {
            
            $catagory = Mage::helper ( 'marketplace/common' )->getCategoryData ( $key );
            $count = $catagory->getProductCount ();
            if (strstr ( $key, 'sub' )) {
                $key = str_replace ( 'sub', '', $key );
                $array .= '<li class="level-top  parent" id="' . $key . '"><a href="javascript:void(0);"><span class="end-plus" id="' . $key . '"></span></a><span class="last-collapse"><input id="cat' . $key . '" type="checkbox" name="category_ids[]" value="' . $key . '"><label for="cat' . $key . '">' . $catname . '<span>(' . $count . ')</span>' . '</label></span>';
            } else {
                $array .= '<li class="level-top  parent"><a href="javascript:void(0);"><span class="empty_space"></span></a><input id="cat' . $key . '" type="checkbox" name="category_ids[]" value="' . $key . '"><label for="cat' . $key . '">' . $catname . '<span>(' . $count . ')</span>' . '</label>';
            }
        }
        $array .= '</li>';
        return $array . '</ul>';
    }
}
