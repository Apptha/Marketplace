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
 * category listing page
 * This file is used the list the catogories with their subcategories
 */
class Apptha_Marketplace_Block_Categorydisplay extends Mage_Core_Block_Template {
/**
 * Get category collection
 *
 * Return the category data collection
 *
 * @return array
 */
public function getCategory($catId) {
return Mage::getModel ( 'catalog/category' )->load ( $catId );
}
/**
 * Get sub category collection of the particular category
 *
 * @return array
 */
public function getSubCategories($catId) {
$subCatId = array ();
/**
 * Get All Categories
 * 
 * @var obj
 */
$children = Mage::getModel ( 'catalog/category' )->getCategories ( $catId );
/**
 * Increment foreach loop
 */
foreach ( $children as $_children ) {
$subCatId [] = $_children->getId ();
}
return $subCatId;
}
/**
 * Get discounted category display details
 *
 * @return array
 */
public function getDiscountedCategory($catId) {
$trendIds = array ();
/**
 * Get Subcategories
 * 
 * @param
 *        cat id
 */
$subCategoryIds = $this->getSubCategories ( $catId );
/**
 * Check whether sub category empty or not
 */
if (count ( $subCategoryIds ) > 0) {
/**
 * Increment foreach loop
 */
foreach ( $subCategoryIds as $_subCategoryIds ) {
/**
 * load category object
 *
 * @var category id
 */
$categoryData = Mage::getModel ( 'catalog/category' )->load ( $_subCategoryIds );
if ($categoryData->getDiscountedCategoryListings () == 1) {
$trendIds [] = $categoryData->getId ();
}
}
}
return $trendIds;
}
}