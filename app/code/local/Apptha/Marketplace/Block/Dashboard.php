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
 * This file is used to display seller dashboard with functionalities like Total sales, Average orders,
 * Last five orders, Most viewed products and Sales report
 */
class Apptha_Marketplace_Block_Dashboard extends Mage_Core_Block_Template{  
    /**
     * Function to get profile url
     * 
     * Return the seller profile url
     * @return string
     */
    function profileUrl(){     
        return  Mage::getUrl('marketplace/seller/addprofile');
    }
   /**
    * Function to get most viewed product information
    * 
    * Return the Most viewed products as array
    * @return array
    */
   public function mostViewed(){
       $storeId    = Mage::app()->getStore()->getId();
       /**
        * Get Resource model product collection
        * @var obj
        */
       $products   = Mage::getResourceModel('reports/product_collection')
                    ->addOrderedQty()
                    ->addAttributeToSelect('*')           
                    ->setStoreId($storeId)
                    ->addStoreFilter($storeId)
                    ->addViewsCount();
       /**
        * Filter by status
        */
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        /**
         * Filter by visibility
         */
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products); 
        /**
         * Set page limit
         */
        $products->setPageSize(5)->setCurPage(1);
        return $products;       
   }
   /**
    * Getting sales report collection
    * 
    * Passed the From date as $dbFrom to sort the sales orders 
    * @param int $dbFrom
    * 
    * Passed the To date as $dbTo to sort the sales orders 
    * @param int $dbTo
    * 
    * Passed the seller id as $id to get particular seller orders 
    * @param int $id
    * 
    * Return commission collection as array
    * @return array
    * 
    */ 
    public function advancedSalesReportCollection($dbFrom,$dbTo,$id) { 
        $selectFilter = '';
        $data           = $this->getRequest()->getPost();
        if(isset($data['select_filter'])){
            $selectFilter  = $data['select_filter'];
        }
        /**
         * Check whether date is today
         */
        if($selectFilter=='today'){
        /**
         * Get From date
         * 
         */
          $from    = date("Y-m-d", strtotime("-1 days"));
          /**
           * Get to date
           * 
           */
          $to      = date("Y-m-d", strtotime("mid"));
          $dbFrom    = Mage::getModel('core/date')->gmtDate(null, strtotime($from));
          $dbTo      = Mage::getModel('core/date')->gmtDate(null, strtotime($to));
        }
        /**
         * Return commission collection object
         * Filter by order status,
         * seller id,
         * created at
         */
        return Mage::getModel('marketplace/commission')->getCollection()
                        ->addFieldToFilter('order_status','complete')
                        ->addFieldToFilter('seller_id',$id)
                        ->addFieldToFilter('created_at', array('from' =>$dbFrom, 'to'=>$dbTo));     
    } 
} 



