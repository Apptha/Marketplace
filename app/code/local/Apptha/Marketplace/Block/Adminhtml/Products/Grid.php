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
 */

/**
 * Manage Products Grid
 */
class Apptha_Marketplace_Block_Adminhtml_Products_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    
    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of data to display order information
     *
     * @return array
     */
    public function __construct() {
        parent::__construct ();
        /**
         * Set Id
         */
        $this->setId ( 'productsGrid' );
        /**
         * Set sorting and direction
         */
        $this->setDefaultSort ( 'entity_id' );
        $this->setDefaultDir ( 'DESC' );
        $this->setSaveParametersInSession ( true );
    }
    
    /**
     * Get current store id
     *
     * Return current store id
     *
     * @return type
     */
    protected function _getStore() {
    /**
     * Get store id
     * @var int
     */
        $storeId = ( int ) $this->getRequest ()->getParam ( 'store', 0 );
        return Mage::app ()->getStore ( $storeId );
    }
    
    /**
     * Function to get seller product collection
     *
     * Return the seller product collection information
     * return array
     */
    protected function _prepareCollection() {
        /**
         * Get store details
         */
        $store = $this->_getStore ();
        /**
         * Get Seller Id
         * @var unknown
         */
        $sellerId = $this->getRequest ()->getParam ( 'id' );
        /**
         * Get product collection.
         * Filter by sku,seller id ,attribute set id
         */
        $collection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( 'sku' )->addAttributeToSelect ( 'name' )->addAttributeToSelect ( 'seller_id' )->addAttributeToSelect ( 'attribute_set_id' )->addAttributeToSelect ( 'type_id' );
        $getGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
       /**
        *Filter by group id 
        */
        $collection->addAttributeToFilter ( 'group_id', array (
                'eq' => $getGroupId 
        ) );
        /**
         * Filter seller id from product collction.
         */
        if ($sellerId != '') {
            $collection->addAttributeToFilter ( 'seller_id', array (
                    'eq' => $sellerId 
            ) );
        }
        if (Mage::helper ( 'catalog' )->isModuleEnabled ( 'Mage_CatalogInventory' )) {
            $collection->joinField ( 'qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left' );
        }
        /**
         * Filter fields added for product collection.
         */
        if ($store->getId ()) {
            $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
            $collection->addStoreFilter ( $store );
            $collection->joinAttribute ( 'name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore );
            $collection->joinAttribute ( 'custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId () );
            $collection->joinAttribute ( 'status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId () );
            $collection->joinAttribute ( 'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId () );
            $collection->joinAttribute ( 'price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId () );
        } else {
            /**
             * Price attribute is selceted from product collection.
             */
            $collection->addAttributeToSelect ( 'price' );
            $collection->joinAttribute ( 'status', 'catalog_product/status', 'entity_id', null, 'inner' );
            $collection->joinAttribute ( 'visibility', 'catalog_product/visibility', 'entity_id', null, 'inner' );
        }
        /**
         * Set Collection
         */
        $this->setCollection ( $collection );
        parent::_prepareCollection ();
        $this->getCollection ()->addWebsiteNamesToResult ();
        return $this;
    }
    
    /**
     * Function to filter product according to website
     *
     * Return the filter product collection
     * return array
     */
    protected function _addColumnFilterToCollection($column) {
    /**
     * Collection join query
     */
        if ($this->getCollection () && $column->getId () == 'websites') {
            $this->getCollection ()->joinField ( 'websites', 'catalog/product_website', 'website_id', 'product_id=entity_id', null, 'left' );
        }
        return parent::_addColumnFilterToCollection ( $column );
    }
    
    /**
     * Function to display fields with data
     *
     * Display information about orders
     *
     * @return void
     */
    protected function _prepareColumns() {
        /**
         * Add entity id.
         * Field:Entity id
         * index:entity_id
         */
        $this->addColumn ( 'entity_id', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'ID' ),
                'width' => '50px',
                'index' => 'entity_id' 
        ) );
        /**
         * Add name.
         * Field :name
         * index:name
         */
        $this->addColumn ( 'name', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'Name' ),
                'index' => 'name' 
        ) );
        $store = $this->_getStore ();
        /**
         * Get Store id
         */
        if ($store->getId ()) {
            /**
             * Add custom name.
             * Field:custom name
             * index:custom_name
             */
            $this->addColumn ( 'custom_name', array (
                    'header' => Mage::helper ( 'catalog' )->__ ( 'Name in %s', $store->getName () ),
                    'index' => 'custom_name' 
            ) );
        }
        /**
         * Add type.
         * Field:type id
         * index:type_id
         */
        $this->addColumn ( 'type', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'Type' ),
                'width' => '60px',
                'index' => 'type_id',
                'type' => 'options',
                'options' => Mage::helper ( 'marketplace' )->getProductTypes () 
        ) );
        /**
         * Add seller id.
         * Field:seller id
         * index:seller_id
         */
        $this->addColumn ( 'seller_id', array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Seller Id' ),
                'width' => '80px',
                'index' => 'seller_id' 
        ) );
        /**
         * Add seller email.
         * Field:seller email
         * index:seller_id
         * renderer:seller email
         */
        $this->addColumn ( 'sellerid', array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Seller Email' ),
                'width' => '150px',
                'index' => 'seller_id',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Ordersellerdetails' 
        ) );
        /**
         * Add product sku.
         * Field:Sku
         * index:sku
         */
        $this->addColumn ( 'sku', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'SKU' ),
                'width' => '80px',
                'index' => 'sku' 
        ) );
        /**
         * Add price.
         * Field:price
         * index:price
         */
        $this->addColumn ( 'price', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'price' ),
                'type' => 'price',
                'currency_code' => $store->getBaseCurrency ()->getCode (),
                'index' => 'price' 
        ) );
        /**
         * Add quantity.
         * Field:qty
         * index:qty
         */
        if (Mage::helper ( 'catalog' )->isModuleEnabled ( 'Mage_CatalogInventory' )) {
            $this->addColumn ( 'qty', array (
                    'header' => Mage::helper ( 'catalog' )->__ ( 'Qty' ),
                    'width' => '100px',
                    'type' => 'number',
                    'index' => 'qty' 
            ) );
        }
        /**
         * Add status.
         * Field:Status
         * index:status
         */
        $this->addColumn ( 'status', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'status' ),
                'width' => '70px',
                'index' => 'status',
                'type' => 'options',
                'options' => Mage::getSingleton ( 'catalog/product_status' )->getOptionArray () 
        ) );
        if (! Mage::app ()->isSingleStoreMode ()) {
           $this->addColumn ( 'websites', array (
                    'header' => Mage::helper ( 'catalog' )->__ ( 'websites' ),
                    'width' => '100px',
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options',
                    'options' => Mage::getModel ( 'core/website' )->getCollection ()->toOptionHash () 
            ) );
        }
      $this->addColumn ( 'action', array (
                'header' => Mage::helper ( 'catalog' )->__ ( 'Action' ),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                        array (
                                'caption' => Mage::helper ( 'catalog' )->__ ( 'View' ),
                                'url' => array (
                                        'base' => 'adminhtml/catalog_product/edit',
                                        'params' => array (
                                                'store' => $this->getRequest ()->getParam ( 'store' ) 
                                        ) 
                                ),
                                'field' => 'id' 
                        ) 
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores' 
        ) );
        return parent::_prepareColumns ();
    }
    
    /**
     * Function for Mass action
     *
     * return void
     */
    protected function _prepareMassaction() {
        return $this;
    }
    
    /**
     * Function for link url
     *
     * Return the product edit page url
     * return string
     */
    public function getRowUrl($row) {
    /**
     * Get store details
     */
        return $this->getUrl ( 'adminhtml/catalog_product/edit', array (
                'store' => $this->getRequest ()->getParam ( 'store' ),
                'id' => $row->getId () 
        ) );
    }
}
