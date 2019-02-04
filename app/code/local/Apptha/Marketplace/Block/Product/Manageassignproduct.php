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
 * Manage seller products functionality
 */
class Apptha_Marketplace_Block_Product_Manageassignproduct extends Mage_Core_Block_Template {
    /**
     * Collection for manage products
     *
     * @return \Apptha_Marketplace_Block_Product_Manage
     */
    protected function _prepareLayout() {
        $manageCollection = $this->manageProducts ();
        
        parent::_prepareLayout ();
        $this->setCollection ( $manageCollection );
        $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $manageCollection );
        /**
         * Setting available limit for the pager
         */
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
        $deleteValue = $this->getRequest ()->getPost ( 'multi' );
        $entityIds = $this->getRequest ()->getParam ( 'id' );
        $multiSubmit = $this->getRequest ()->getPost ( 'multi_submit' );
        /**
         * Check if submit buttom submitted.
         */
        if ($multiSubmit) {
            if (count ( $entityIds ) > 0 && $deleteValue == 'delete') {
                foreach ( $entityIds as $entityidValue ) {
                    Mage::register ( 'isSecureArea', true );
                    Mage::helper ( 'marketplace/marketplace' )->deleteProduct ( $entityidValue );
                    Mage::unregister ( 'isSecureArea' );
                }
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "Selected Products are Deleted Successfully" ) );
                $urlVal = Mage::getUrl ( 'marketplace/sellerproduct/manageassignproduct' );
                Mage::app ()->getFrontController ()->getResponse ()->setRedirect ( $urlVal );
            }
            if (count ( $entityIds ) == 0 && $deleteValue == 'delete') {
                $errorMsg = $this->__ ( "Please select a product to delete" );
                Mage::getSingleton ( 'core/session' )->addError ( $errorMsg );
                $urlVal = Mage::getUrl ( 'marketplace/sellerproduct/manageassignproduct' );
                Mage::app ()->getFrontController ()->getResponse ()->setRedirect ( $urlVal );
            }
        }
        
        $filterStatus = $this->getRequest ()->getParam ( 'filter_status' );
        $filterQuantity = $this->getRequest ()->getParam ( 'filter_quantity' );
        $filterProductType = $this->getRequest ()->getParam ( 'filter_product_type' );
        $filterId = $this->getRequest ()->getParam ( 'filter_id' );
        $filterName = $this->getRequest ()->getParam ( 'filter_name' );
        $filterPrice = $this->getRequest ()->getParam ( 'filter_price' );
        
        $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
        
        /**
         * Get Product Collection
         * Filter by seller id
         */
        $products = Mage::getModel ( 'catalog/product' )->getCollection ();
        $products->addAttributeToSelect ( '*' );
        $products->addAttributeToFilter ( 'seller_id', array (
                'eq' => $customerId 
        ) );
        
        $products = Mage::helper ( 'marketplace/product' )->productFilterByAttribute ( 'entity_id', $filterId, $products );
        $products = Mage::helper ( 'marketplace/product' )->productFilterByAttribute ( 'price', $filterPrice, $products );
        $products = Mage::helper ( 'marketplace/product' )->productFilterByAttribute ( 'status', $filterStatus, $products );
        $products = Mage::helper ( 'marketplace/product' )->productFilterByAttribute ( 'name', $filterName, $products );
        /**
         * Check filter quantity is not equal to empty
         */
        
        if ($filterQuantity != '') {
            $products->joinField ( 'qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left' )->addAttributeToFilter ( 'qty', array (
                    'eq' => $filterQuantity 
            ) );
        }
       
        /**
         * confirming filter product type is not empty
         */
        if (! empty ( $filterProductType )) {
            $products->addAttributeToFilter('type_id', array(
                    'eq' => $filterProductType 
            ));
        }
       $products->addAttributeToFilter('is_assign_product', array(
                'eq' => 1 
        ) );
      $products->addAttributeToFilter ( 'visibility', array (
                'eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        ) );
       $products->getSelect()->joinLeft(array('link_table' => 'catalog_product_super_link'),
        'link_table.product_id = e.entity_id',
        array('product_id')
        );
        $products->getSelect()->where('link_table.product_id IS NULL');
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
        return Mage::getUrl ( 'marketplace/sellerproduct/manageassignproduct' );
    }

}