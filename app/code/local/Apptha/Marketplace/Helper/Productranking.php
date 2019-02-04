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
 * Function written in this file are used for product ranking
 */
class Apptha_Marketplace_Helper_Productranking extends Mage_Core_Helper_Abstract {
/**
 * Function to update Totalsales for assigned products
 *
 * @param int $productId        
 * @param unknown $operation        
 */
public function updateTotalSalesForAssignedProducts($productId, $operation) {
/**
 * Get Product Collectiojn
 * Filter by assign product id
 * 
 * @var unknown
 */
$productCollection = Mage::getModel ( 'catalog/product' )->load ( $productId );
$assignProducts = array ();
/**
 * If clause
 */
if ($productCollection->getAssignProductId ()) {
$assignProducts = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addFieldToFilter ( 'assign_product_id', $productCollection->getAssignProductId () );
$this->updateTotalSales ( $productCollection->getAssignProductId (), $operation );
} else {
$assignProducts = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addFieldToFilter ( 'assign_product_id', $productId );
$this->updateTotalSales ( $productId, $operation );
}
/**
 * End if clause
 */
/**
 * Increment foreach loop
 */
foreach ( $assignProducts as $eachItem ) {
/**
 * load Product object
 * 
 * @var unknown
 */
$productData = Mage::getModel ( 'catalog/product' )->load ( $eachItem->getId () );
$totalsales = ($operation == '+' ? ($productData->getTotalSales () + 1) : ($productData->getTotalSales () - 1));
/**
 * set Total Sales
 */
$productData->setTotalSales ( $totalsales )->save ();
}
}
/**
 * Update total sales for parent product
 *
 * @param unknown $productId        
 * @param unknown $operation        
 */
public function updateTotalSales($productId, $operation) {
/**
 * Load Product object
 * 
 * @var unknown
 */
$productData = Mage::getModel ( 'catalog/product' )->load ( $productId );
$totalsales = ($operation == '+' ? ($productData->getTotalSales () + 1) : ($productData->getTotalSales () - 1));
/**
 * Set total sales
 */
$productData->setTotalSales ( $totalsales )->save ();
}
}