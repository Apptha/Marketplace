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
 * This class contains assign product collection for showing as main product
 * 
 */
class Apptha_Marketplace_Helper_ProductStatus extends Mage_Catalog_Helper_Product{
    
    /**
     * Function to init product
     *
     * @return product object
     *        
     */
    public function initProduct($productId, $controller, $params = null) {
       if (! $params) {
            $params = new Varien_Object ();
        }
       
        Mage::dispatchEvent ( 'catalog_controller_product_init_before', array (
                'controller_action' => $controller,
                'params' => $params 
        ) );
        
        $product = Mage::getModel ( 'catalog/product' )->setStoreId ( Mage::app ()->getStore ()->getId () )->load ( $productId );
        $assignProductsCollection = Mage::helper ( 'marketplace/outofstock' )->CheckOutofstock ( $productId );
       
        if (! in_array ( Mage::app ()->getStore ()->getWebsiteId (), $product->getWebsiteIds () ) ||(! $productId) || (! count ( $assignProductsCollection ) > 0 && (! $this->canShow ( $product )))) {
            return false;
        }
        
        $categoryId = $params->getCategoryId ();
        if (! $categoryId && ($categoryId !== false)) {
            $lastId = Mage::getSingleton ( 'catalog/session' )->getLastVisitedCategoryId ();
            if ($product->canBeShowInCategory ( $lastId )) {
                $categoryId = $lastId;
            }
        } elseif (! $product->canBeShowInCategory ( $categoryId )) {
            $categoryId = null;
        }
        
        if ($categoryId) {
            $category = Mage::getModel ( 'catalog/category' )->load ( $categoryId );
            $product->setCategory ( $category );
            Mage::register ( 'current_category', $category );
        }
        
        Mage::register ( 'current_product', $product );
        Mage::register ( 'product', $product );
        
        try {
            Mage::dispatchEvent ( 'catalog_controller_product_init', array (
                    'product' => $product 
            ) );
            Mage::dispatchEvent ( 'catalog_controller_product_init_after', array (
                    'product' => $product,
                    'controller_action' => $controller 
            ) );
        } catch ( Mage_Core_Exception $e ) {
            Mage::logException ( $e );
            return false;
        }
        
        return $product;
    }
}