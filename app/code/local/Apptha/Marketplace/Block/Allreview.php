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
 * This file is used get seller reviews 
 */
class Apptha_Marketplace_Block_Allreview extends Mage_Core_Block_Template {
/**
     * Collection for manage reviews
     * 
     * @return \Apptha_Marketplace_Block_Allreview
     */
    protected function _prepareLayout() {
        parent::_prepareLayout();
        $id = $this->getRequest()->getParam('id');
        if (is_numeric($id) || $id == '') {
            $reviewCollection = $this->getallreview($id);
            /**
             * Set Collection
             */
            $this->setCollection($reviewCollection);
           /**
            * Set pagination for collection
            * 
            */
            $pager = $this->getLayout()
                    ->createBlock('page/html_pager', 'my.pager')
                    ->setCollection($reviewCollection);
            /**
             * Set limit for views
             */
            $pager->setAvailableLimit(array(10 => 10, 20 => 20, 50 => 50));       
            $this->setChild('pager', $pager);
        }
        return $this;
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
     * Function to get all review collection
     * 
     * Passed seller id to get the particular seller reviews 
     * @param int $id
     * 
     * Return the review collection
     * @return array
     */
    function getallreview($id) {
    /**
     * Get Store Id
     * @var unknown
     */
        $storeId = Mage::app()->getStore()->getId();
        /**
         * return Seller review model
         * filter by status,
         * store id,
         * seller id
         */
        return Mage::getModel('marketplace/sellerreview')
                ->getCollection()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('seller_id', $id);    
    }
/**
     * Function to get save review url
     * 
     * Return the save review action url
     * @return string
     */
    function saveReviewUrl() {
        return Mage::getUrl('marketplace/sellerreview/savereview');
    }
}