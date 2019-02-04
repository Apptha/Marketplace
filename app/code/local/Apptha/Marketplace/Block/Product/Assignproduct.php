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
 * This class contains manage seller products functionality
 */
class Apptha_Marketplace_Block_Product_Assignproduct extends Mage_Core_Block_Template {
    /**
     * Collection for manage products
     *
     * @return \Apptha_Marketplace_Block_Product_Manage
     */
    protected function _prepareLayout() {
        parent::_prepareLayout ();
        $manageAssignProductCollection = $this->manageProducts ();
        $this->setCollection ( $manageAssignProductCollection );
        $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $manageAssignProductCollection );
        $pager->setAvailableLimit ( array (
                10 => 10,
                20 => 20,
                50 => 50 
        ) );
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
        $filterName = $this->getRequest ()->getParam ( 'filter_name' );
        /**
         * Get Product Collection
         * @var obj
         */
        $products = Mage::getModel ( 'catalog/product' )->getCollection ();
        $products->addAttributeToSelect ( '*' );
        $getFilterName = Mage::getSingleton ( 'core/session' )->getFilterNameForPagination ();
        /**
         * Check filter name and session pagination filter name are not empty
         */
        if (! empty ( $filterName ) || ! empty ( $getFilterName )) {
            if ($filterName != '') {
                $products->addAttributeToFilter ( 'name', array (
                        'like' => '%' . $filterName . '%' 
                ) );
                $getFilterName = Mage::getSingleton ( 'core/session' )->setFilterNameForPagination ( $filterName );
            } else {
                $products->addAttributeToFilter ( 'name', array (
                        'like' => '%' . $getFilterName . '%' 
                ) );
            }
        } else {
            $products->addAttributeToFilter ( 'entity_id', 0 );
        }
        /**
         * Get Customer id
         */
        $cusId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        /**
         * Filter by seller id
         */
        $products->addFieldToFilter ( array (
                array (
                        'attribute' => 'seller_id',
                        array (
                                'null' => true 
                        ) 
                ),
                array (
                        'attribute' => 'seller_id',
                        array (
                                'neq' => $cusId 
                        ) 
                ) 
        ) );
        /**
         * Filter by assign product
         */
        $products->addFieldToFilter ( array (
                array (
                        'attribute' => 'is_assign_product',
                        array (
                                'null' => true 
                        ) 
                ),
                array (
                        'attribute' => 'is_assign_product',
                        array (
                                'neq' => 1 
                        ) 
                ) 
        ) );
        /**
         * Filter by status
         */
        $products->addFieldToFilter ('status',1);
        /**
         * Filter by visibility
         */
        $products->addAttributeToFilter ( 'visibility', array (
                2,
                3,
                4 
        ) );
        /**
         * Sort by entity id -desc
         */
        $products->addAttributeToSort ( 'entity_id', 'DESC' );
        return $products;
    }
    
    /**
     * Function to display pagination
     *
     * Return collection with pagination
     *
     * @return array
     */
    public function getPagerHtml() {
        return $this->getChildHtml ( 'pager' );
    }
    
    /**
     * Function to get multi select url
     *
     * Return the multi select option url
     *
     * @return string
     */
    public function getmultiselectUrl() {
        return Mage::getUrl ( 'marketplace/sellerproduct/assignproduct' );
    }
    /**
     * Function to check already assigned or not
     * @return object
     * @params sellerid ,productid
     */
    public function getAssignedProducts($sellerId,$productId){
        $productCollection= Mage::getModel ('catalog/product' )->getCollection ();
        $productCollection->addAttributeToSelect ( '*' );
        $productCollection->addAttributeToFilter('seller_id',$sellerId);
        $productCollection->addAttributeToFilter('assign_product_id',$productId);
        return $productCollection;
    }
}