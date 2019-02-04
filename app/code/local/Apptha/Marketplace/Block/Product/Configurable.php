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
 * This Class is used for add product functionality
 */
class Apptha_Marketplace_Block_Product_Configurable extends Mage_Core_Block_Template {
    
    /**
     * Collection for manage products
     *
     * @return \Apptha_Marketplace_Block_Product_Manage
     */
    protected function _prepareLayout() {
        /**
         * Get Product Id
         */
        $productId = $this->getRequest ()->getParam ( 'id' );
        /**
         * Get Product Name
         */
        $productName = Mage::getModel ( 'catalog/product' )->load ( $productId )->getName ();
        /**
         * Load Layout
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $productName );
        parent::_prepareLayout ();
        $manageConfigurableProductCollection = $this->manageProducts ();
        /**
         * Set Collection
         */
        $this->setCollection ( $manageConfigurableProductCollection );
        /**
         * Get Layout
         */
        $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $manageConfigurableProductCollection );
        /**
         * set Available limit
         */
        $pager->setAvailableLimit ( array (
                10 => 10,
                20 => 20,
                50 => 50 
        ) );
        /**
         * Set child
         */
        $this->setChild ( 'pager', $pager );
        return $this;
    }
    
    /**
     * Function to get the product details
     *
     * Return product collection
     *
     * @return array
     */
    public function manageProducts() {
        /**
         * Get Customer Id
         */
        $sellerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * get Product Id
         */
        $productId = $this->getRequest ()->getParam ( 'id' );
        /**
         * load product id
         */
        $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
        $attributes = $product->getTypeInstance ()->getConfigurableAttributesAsArray ();
        /**
         * Get Product Collection
         */
        $associatedProducts = Mage::getModel ( 'catalog/product' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId );
        if ($this->getRequest ()->getParam ( 'set' )) {
            $associatedProducts->addFieldToFilter ( 'attribute_set_id', $this->getRequest ()->getParam ( 'set' ) );
        }
        
        $attributeFilters = $this->getRequest ()->getParam ( 'attribute_filter' );
        /**
         * Checking atribute filters are not empty
         */
        if (! empty ( $attributeFilters )) {
            foreach ( $attributeFilters as $key => $attributeFilter ) {
                if (! empty ( $attributeFilter )) {
                    $associatedProducts->addFieldToFilter ( $key, array (
                            'in' => $attributeFilter 
                    ) );
                }
            }
        }
        /**
         * Getting Associated Products
         */
        $associatedProducts = $this->configurableProductFilter ( $productId, $product, $associatedProducts );
        
        if ($this->getRequest ()->getParam ( 'reset' ) == '') {
            if ($this->getRequest ()->getParam ( 'filter_id' ) != '') {
                $fitlerId = $this->getRequest ()->getParam ( 'filter_id' );
                $associatedProducts->addFieldToFilter ( 'entity_id', array (
                        'eq' => $fitlerId 
                ) );
            }
            $filterName = $this->getRequest ()->getParam ( 'filter_name' );
            /**
             * Checking filtername is not empty
             */
            if (! empty ( $filterName )) {
                $associatedProducts->addFieldToFilter ( 'name', array (
                        'like' => '%' . $filterName . '%' 
                ) );
            }
        }
        
        foreach ( $attributes as $attribute ) {
            if (isset ( $attribute ['attribute_code'] )) {
                $associatedProducts->addFieldToFilter ( $attribute ['attribute_code'], array (
                        'neq' => '' 
                ) );
            }
        }
        /**
         * Filter By Type
         */
        $associatedProducts->addFieldToFilter ( 'type_id', 'simple' );
        /**
         * Filter by All
         */
        $associatedProducts->addAttributeToSelect ( '*' );
        /**
         * Sort Order By Desc
         */
        $associatedProducts->addAttributeToSort ( 'entity_id', 'DESC' );
        
        return $associatedProducts;
    }
    
    /**
     * Configurable product filter in associate product page
     *
     * @param object $associatedProducts            
     */
    public function configurableProductFilter($productId, $product, $associatedProducts) {
        if ($this->getRequest ()->getParam ( 'reset' ) == '') {
            $childProductIds = $fitlerId = array ();
            /**
             * Checking product id is not empty and also reset request is not equal to 1
             */
            if (! empty ( $productId ) && $this->getRequest ()->getParam ( 'reset' ) != 1) {
                $childProductIds = $product->getTypeInstance ()->getUsedProductIds ();
                if (count ( $childProductIds ) >= 1) {
                    $fitlerId = $childProductIds;
                }
            }
            $configurableOptionFilter = $this->getRequest ()->getParam ( 'configurable_option_filter' );
            /**
             * Check configurable option filter yes or empty
             */
            if ($configurableOptionFilter == 'yes' || $configurableOptionFilter == '') {
                $associatedProducts->addFieldToFilter ( 'entity_id', array (
                        'in' => $fitlerId 
                ) );
            } else {
                if ($configurableOptionFilter == 'no' && ! empty ( $fitlerId )) {
                    $associatedProducts->addFieldToFilter ( 'entity_id', array (
                            'nin' => $fitlerId 
                    ) );
                }
            }
        }
        
        return $associatedProducts;
    }
    
    /**
     * Function to display pagination
     *
     * Return collection with pagination
     *
     * @return array
     */
    public function getPagerHtml() {
        /**
         * Get ChildHtml
         */
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Getting website id
     *
     * Return the product website id
     *
     * @return int
     */
    public function getWebsiteId() {
        /**
         * Get Id
         */
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
        /**
         * Get Store Id
         */
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
        /**
         * Get Product
         */
        return Mage::getModel ( 'catalog/product' )->getResource ()->getEntityType ()->getDefaultAttributeSetId ();
    }
    
    /**
     * Get configurable product url
     *
     * Return configurable product url
     *
     * @return string
     */
    public function saveconfigurabletUrl() {
        return Mage::getUrl ( 'marketplace/sellerproduct/saveconfigurable' );
    }
}