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
class Apptha_Marketplace_Block_Product_Manage extends Mage_Core_Block_Template {
 /**
  * Collection for manage products
  *
  * @return \Apptha_Marketplace_Block_Product_Manage
  */
 protected function _prepareLayout() {
  parent::_prepareLayout ();
  $manageProductCollection = $this->manageProducts ();
  $this->setCollection ( $manageProductCollection );
  /**
   * set pagination
   * @var collection
   */
  $pager = $this->getLayout ()->createBlock ( 'page/html_pager', 'my.pager' )->setCollection ( $manageProductCollection );
  /**
   * setting available limits for pager
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
 /**
  * Get params multisubmit,multi,id
  * @var unknown
  */
  $multi_submit = $this->getRequest ()->getPost ( 'multi_submit' );
  $entityIds = $this->getRequest ()->getParam ( 'id' );
  $delete = $this->getRequest ()->getPost ( 'multi' );
  /**
   * Check if submit buttom submitted.
   */
  if ($multi_submit) {
   if (count ( $entityIds ) > 0 && $delete == 'delete') {
   /**
    * Increment foreach loop
    */
    foreach ( $entityIds as $entityIdData ) {
    /**
     * Register the variable as secure
     */ 
    Mage::register ( 'isSecureArea', true );
     /**
      * Delete the product  using product id
      */
     Mage::helper ( 'marketplace/marketplace' )->deleteProduct ( $entityIdData );
     Mage::unregister ( 'isSecureArea' );
    }
    /**
     * add success message
     */
    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "selected Products are Deleted Successfully" ) );
   /**
    * send response
    * @var url
    */
    $url = Mage::getUrl ( 'marketplace/product/manage' );
    Mage::app ()->getFrontController ()->getResponse ()->setRedirect ( $url );
   }
   /**
    * Get count of product ids
    */
   if (count ( $entityIds ) == 0 && $delete == 'delete') {
   /**
    * add error message
    */
    Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( "Please select a product to delete" ) );
    $url = Mage::getUrl ( 'marketplace/product/manage' );
    Mage::app ()->getFrontController ()->getResponse ()->setRedirect ( $url );
   }
  }
  /**
   * Get params
   * @var price,
   * status,
   * id,
   * name,
   * product type
   */
  $filterPrice = $this->getRequest ()->getParam ( 'filter_price' );
  $filterStatus = $this->getRequest ()->getParam ( 'filter_status' );
  $filterId = $this->getRequest ()->getParam ( 'filter_id' );
  $filterName = $this->getRequest ()->getParam ( 'filter_name' );
  $filterQuantity = $this->getRequest ()->getParam ( 'filter_quantity' );
  $filterProductType = $this->getRequest ()->getParam ( 'filter_product_type' );
  /**
   * load customer data
   * @var unknown
   */
  $customerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
  /**
   * Get product Collection
   * @var seller_id
   */
  $products = Mage::getModel ( 'catalog/product' )->getCollection ();
  $products->addAttributeToSelect ( '*' );
  $products->addAttributeToFilter ( 'seller_id', array (
    'eq' => $customerId 
  ) );  
  $products = Mage::helper('marketplace/product')->productFilterByAttribute('name',$filterName,$products);
  $products = Mage::helper('marketplace/product')->productFilterByAttribute('entity_id',$filterId,$products); 
  $products = Mage::helper('marketplace/product')->productFilterByAttribute('price',$filterPrice,$products);
  $products = Mage::helper('marketplace/product')->productFilterByAttribute('status',$filterStatus,$products);

  /**
   * confirming filter product type is not empty
   */
  if (! empty ( $filterProductType )) {
   $products->addAttributeToFilter ( 'type_id', array ('eq' => $filterProductType ) );
  }
  /**
   * Check filter quantity is not equal to empty
   */
  if ($filterQuantity != '') {
   $products->joinField ( 'qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left' )->addAttributeToFilter ( 'qty', array (
     'eq' => $filterQuantity 
   ) );
  }
 $products->addAttributeToFilter ( 'visibility', array ('eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH ) );
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
  return Mage::getUrl ( 'marketplace/product/manage' );
 }
 /**
  * Function to get multi select url
  *
  * Return the multi select option url
  * 
  * @return string
  */
 public function getBulkUploadUrl() {
  return Mage::getUrl ( 'marketplace/bulkupload/bulkupload' );
 }
}