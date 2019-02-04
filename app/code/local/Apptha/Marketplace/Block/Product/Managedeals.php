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
 * This Class is used for manage product deals functionality
 */
class Apptha_Marketplace_Block_Product_Managedeals extends Mage_Core_Block_Template {
    /**
     * Collection for manage product deals
     * 
     * @return \Apptha_Marketplace_Block_Product_Managedeals
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $manageCollection = $this->manageProducts();
        $this->setCollection($manageCollection);
        $pager = $this->getLayout()
                ->createBlock('page/html_pager', 'my.pager')
                ->setCollection($manageCollection);
        /**
        * Setting available limit for the pager
        */
        $pager->setAvailableLimit(array(10 => 10, 20 => 20, 30 => 30, 50 => 50));
        $pager->setLimit(20);
        $this->setChild('pager', $pager);
        return $this;
    }

    /**
     * Function to get the product details
     * 
     * This function will return the array of product collection
     * @return array
     */
    public function manageProducts() {
        $entityIds = $this->getRequest()->getParam('id');
        $delete = $this->getRequest()->getPost('multi');
        if (count($entityIds) > 0 && $delete == 'delete') {
            foreach ($entityIds as $_entity_ids) {
                Mage::helper('marketplace/general')->deleteDeal($_entity_ids);
                Mage::getSingleton('core/session')->addSuccess($this->__("Selected Products Deal are Deleted Successfully"));
            }
        }
        $filterId = $this->getRequest()->getParam('filter_id');
        $filterName = $this->getRequest()->getParam('filter_name');
        $filterPrice = $this->getRequest()->getParam('filter_price');
        $filterStatus = $this->getRequest()->getParam('filter_status');
        $todayDate = date('m/d/y');
        $tomorrow = mktime(0, 0, 0, date('m'), date('d') + 1, date('y'));
        $tomorrowDate = date('m/d/y', $tomorrow);
        $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $products = Mage::getModel('catalog/product')->getCollection();
        $products->addAttributeToSelect('*');
        $products->addAttributeToFilter('seller_id', array('eq' => $customerId));
        /**
         * Check Filter id is not empty
         */
        if ($filterId != '') {
            $products->addAttributeToFilter('entity_id', array('eq' => $filterId));
        }
        /**
         * Check Filter Name is not empty
         */
        if ($filterName != '') {
            $products->addAttributeToFilter('name', array('like' => '%' . $filterName . '%'));
        }
        /**
         * Check Filter Price is not equal to empty
         */
        if ($filterPrice != '') {
            $products->addAttributeToFilter('price', array('eq' => $filterPrice));
        }
        /**
         * Check Filter Status is not equal to zero
         */
        if ($filterStatus != 0) {
            $products->addAttributeToFilter('status', array('eq' => $filterStatus));
        }
        $products->addAttributeToSort('entity_id', 'DESC');
        $products->addAttributeToFilter('special_from_date', array('date' => true, 'to' => $todayDate));
        $products->addAttributeToFilter('special_to_date', array('or' => array(
                0 => array('date' => true, 'from' => $tomorrowDate),
                1 => array('is' => new Zend_Db_Expr('null')))
                ), 'left');
        return $products;
    }

    /**
     * Function to get pagination
     * 
     * Return pagination for collection
     * @return array
     */
    public function getPagerHtml() {
        return $this->getChildHtml('pager');
    }

    /**
     * Function to get product multi select action url 
     * 
     * This function will return the manage deals redirect url
     * return string
     */
    public function getmultiselectUrl() {
        return Mage::getUrl('marketplace/product/managedeals');
    }

}