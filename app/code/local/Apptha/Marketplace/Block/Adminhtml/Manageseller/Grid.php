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
 * Manage Sellers Grid
 */
class Apptha_Marketplace_Block_Adminhtml_Manageseller_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * Construct the inital display of grid information
     * Set the default sort for collection
     * Set the sort order as "DESC"
     *
     * Return array of data to display seller information
     *
     * @return array
     */
    public function __construct() {
        parent::__construct ();
        /**
         * set id
         */
        $this->setId ( 'managesellerGrid' );
        /**
         * set default sort
         */
        $this->setDefaultSort ( 'entity_id' );
        /**
         * set default order
         */
        $this->setDefaultDir ( 'DESC' );
        /**
         * save parameters
         */
        $this->setSaveParametersInSession ( true );
    }
    
    /**
     * Function to get seller collection
     *
     * Return the seller information
     * return array
     */
    protected function _prepareCollection() {
        /**
         * get groupid
         */
        $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
        /**
         * Get Customer Collection
         */
        $collection = Mage::getResourceModel ( 'customer/customer_collection' )->addNameToSelect ()->addAttributeToSelect ( 'email' )->addAttributeToSelect ( 'created_at' )->addAttributeToSelect ( 'group_id' )->addAttributeToSelect ( 'customerstatus' )->addFieldToFilter ( 'group_id', $groupId );
        /**
         * set Collection
         */
        $this->setCollection ( $collection );
        return parent::_prepareCollection ();
    }
    
    /**
     * Function to create column to grid
     *
     * @param string $id            
     * @return string colunm value
     */
    public function addCustomColumn($id) {
        switch ($id) {  
        /**
         * Case using Id
         * Field:Entity id
         * index:entity_id
         */
            case 'ID' :
                $entityId = array ('header' => Mage::helper ( 'customer' )->__ ( 'ID' ),'width' => '40px','index' => 'entity_id');
                $value = $this->addColumn ( 'entity_id', $entityId );
                break;  
            case 'Store Name' :
                $storeTitle = array ('header' => Mage::helper ( 'customer' )->__ ( 'Store Name' ),'width' => '150px','index' => 'store_title',
                        'filter' => false,'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Storetitle');
                $value = $this->addColumn ( 'store_title', $storeTitle );
                break; 
            case 'email' :
                $email = array ('header' => Mage::helper ( 'customer' )->__ ( 'email' ),'width' => '160px','index' => 'email');
                $value = $this->addColumn ( 'email', $email );
                break;   
            case 'Name' :
                $name = array ('header' => Mage::helper ( 'customer' )->__ ( 'Name' ),'width' => '200px','index' => 'name');
                $value = $this->addColumn ( 'name', $name );
                break; 
            case 'Contact' :
                $contact = array ('header' => Mage::helper ( 'customer' )->__ ( 'Contact' ),'width' => '150px','index' => 'contact',
                        'filter' => false,'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Contact');
                $value = $this->addColumn ( 'contact', $contact );
                break;  
            case 'customer' :
                $customerSince = array ('header' => Mage::helper ( 'customer' )->__ ( 'customer' ),'type' => 'datetime',
                        'width' => '150px','align' => 'center','index' => 'created_at','gmtoffset' => true);
                $value = $this->addColumn ( 'customer_since', $customerSince );
                break;     
            case 'Total Sales' :
                $totalSales = array ('header' => Mage::helper ( 'customer' )->__ ( 'Total Sales' ),
                        'width' => '80px','index' => 'total_sales','align' => 'right','filter' => false,
                        'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Totalsales');
                $value = $this->addColumn ( 'total_sales', $totalSales );
                break;    
            case 'Received' :
                $received = array ('header' => Mage::helper ( 'sales' )->__ ( 'Received' ),'width' => '80px',
                        'align' => 'right','index' => 'entity_id','filter' => false,
                        'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Amountreceived');
                $value = $this->addColumn ( 'amount_received', $received );
                break;  
            case 'Remaining' :
                $remaining = array ('header' => Mage::helper ( 'sales' )->__ ( 'Remaining' ),'width' => '80px',
                        'align' => 'right','index' => 'entity_id','filter' => false,
                        'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Amountremaining');
                $value = $this->addColumn ( 'amount_remaining', $remaining );
                break;         
            default :
                $customerStatus = array ('header' => Mage::helper ( 'customer' )->__ ( 'Status' ),'width' => '150px',
                        'type' => 'options','index' => 'customerstatus',
                        'options' => Mage::getSingleton ( 'marketplace/status_status' )->getOptionArray ());
                $value = $this->addColumn ( 'customerstatus', $customerStatus );
        }
        return $value;
    }    
    /**
     * Function to display fields with data
     *
     * Display information about Seller
     *
     * @return void
     */
    protected function _prepareColumns() {
        /**
         * Add custom column id
         */
        $this->addCustomColumn ( 'ID' );
        /**
         * Add custom column store name
         */
        $this->addCustomColumn ( 'Store Name' );
        /**
         * Add custom column email
         */
        $this->addCustomColumn ( 'email' );
        /**
         * Add custom column name
         */
        $this->addCustomColumn ( 'Name' );
        /**
         * Add custom column contact
         */
        $this->addCustomColumn ( 'Contact' );
        /**
         * Add custom column customer
         */
        $this->addCustomColumn ( 'customer' );
        /**
         * Add custom column total products
         */
        $this->addColumn ( 'total_products', array (
                'header' => Mage::helper ( 'customer' )->__ ( '#Products' ),
                'width' => '100px',
                'index' => 'total_products',
                'align' => 'right',
                'filter' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Totalproducts' 
        ) );
        /**
         * Add custom column commission
         */
        $this->addColumn ( 'commission', array (
                'header' => Mage::helper ( 'customer' )->__ ( 'Commission(%)' ),
                'width' => '70px',
                'index' => 'commission',
                'align' => 'right',
                'filter' => false,
                'renderer' => 'Apptha_Marketplace_Block_Adminhtml_Renderersource_Commissionrate' 
        ) );
        /**
         * Add custom column total sales
         */
        $this->addCustomColumn ( 'Total Sales' );
        /**
         * Add custom column received
         */
        $this->addCustomColumn ( 'Received' );
        /**
         * Add custom column remaning
         */
        $this->addCustomColumn ( 'Remaining' );
        /**
         * Add custom column status
         */
        $this->addCustomColumn ( 'Status' );
        /**
         *Field:Action
         *type:action
         *index:id
         *
         */
        $this->addColumn ( 'action', array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'action' ),
                'type' => 'action',
                'width' => '200px',
                'getter' => 'getId',
                'actions' => array (
                        array (
                                'caption' => Mage::helper ( 'marketplace' )->__ ( 'Approve' ),
                                'url' => array (
                                        'base' => '*/*/approve/' 
                                ),
                                'field' => 'id',
                                'title' => Mage::helper ( 'marketplace' )->__ ( 'Approve' ) 
                        ),
                        array (
                                'caption' => Mage::helper ( 'marketplace' )->__ ( 'Disapprove' ),
                                'url' => array (
                                        'base' => "*/*/disapprove" 
                                ),
                                'field' => 'id' 
                        ),
                        array (
                                'caption' => Mage::helper ( 'marketplace' )->__ ( 'Delete' ),
                                'url' => array (
                                        'base' => "*/*/delete" 
                                ),
                                'field' => 'id',
                                'confirm' => Mage::helper ( 'marketplace' )->__ ( 'Are you sure?' ) 
                        ) 
                ),
                'sortable' => false 
        ) );
        /**
         * set commission
         * type:Action
         * index:id
         */
        $this->addColumn ( 'set_commission', array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Set Commission' ),
                'width' => '80',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                        array (
                                'caption' => Mage::helper ( 'marketplace' )->__ ( 'Commission' ),
                                'url' => array (
                                        'base' => '*/*/setcommission/' 
                                ),
                                'field' => 'id',
                                'title' => Mage::helper ( 'marketplace' )->__ ( 'Commission' ) 
                        ) 
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true 
        ) );
        $order = array (
                'header' => Mage::helper ( 'marketplace' )->__ ( 'Order' ),
                'width' => '80',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array (
                        array (
                                'caption' => Mage::helper ( 'marketplace' )->__ ( 'Order' ),
                                'url' => array (
                                        'base' => 'marketplaceadmin/adminhtml_order/index/' 
                                ),
                                'field' => 'id' 
                        )),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true 
        );
        $this->addColumn ( 'order', $order );
        return parent::_prepareColumns ();
    }
    /**
     * Function for Mass edit action(approve,disapprove or delete)
     *
     * Will change the status of the seller
     * return void
     */
    protected function _prepareMassaction() {
        /**
         * set Entity Id
         */
        $this->setMassactionIdField ( 'entity_id' );
        /**
         * Set Form Field
         */
        $this->getMassactionBlock ()->setFormFieldName ( 'marketplace' );
        /**
         * Add custom column delete
         */
        $this->getMassactionBlock ()->addItem ( 'delete', array (
                'label' => Mage::helper ( 'marketplace' )->__ ( 'Delete' ),
                'url' => $this->getUrl ( '*/*/massDelete' ),
                'confirm' => Mage::helper ( 'marketplace' )->__ ( 'Are you sure ?' ) 
        ) );
        /**
         * Add custom column approve
         */
        $this->getMassactionBlock ()->addItem ( 'Approve', array (
                'label' => Mage::helper ( 'customer' )->__ ( 'Approve' ),
                'url' => $this->getUrl ( '*/*/massApprove' ) 
        ) );
        /**
         * Add custom column disapprove
         */
        $this->getMassactionBlock ()->addItem ( 'disapprove', array (
                'label' => Mage::helper ( 'customer' )->__ ( 'Disapprove' ),
                'url' => $this->getUrl ( '*/*/massDisapprove' ) 
        ) );
        /**
         * Add custom column for email to sellers option
         */
        $this->getMassactionBlock ()->addItem ( 'emailToSellersAdmin', array (
                'label' => Mage::helper ( 'customer' )->__ ( 'Email to seller(s)' ),
                'url' => $this->getUrl ( '*/*/emailToSellersAdmin' ) 
        ) );
        return $this;
    }
    
    /**
     * Function for link url
     *
     * Return the seller profile edit url
     * return string
     */
    public function getRowUrl($row) {
        return $this->getUrl ( 'adminhtml/customer/edit/', array (
                'id' => $row->getId () 
        ) );
    }
}