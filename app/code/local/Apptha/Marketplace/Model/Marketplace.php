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
class Apptha_Marketplace_Model_Marketplace extends Mage_Core_Model_Abstract{
/**
 * model:marketplace
 * {@inheritDoc}
 * @see Varien_Object::_construct()
 */
    public function _construct(){
        parent::_construct();
        $this->_init('marketplace/marketplace');
    }
    /**
     * Get Product Collection
     * 
     * Return product collection as array
     * @return array
     */
    public function getProductCollection(){
    /**
     * getting product model
     * Filter by status,banner
     */
       $model = Mage::getModel('catalog/product') ;
       $collection = $model->getCollection();
       $collection->addAttributeToFilter('status', array('eq'=> 1));
       $collection->addAttributeToFilter('banner',array('neq'=>'no_selection'));
       if(Mage::getStoreConfig('marketplace/product/set_banner')==1){
            $collection->addAttributeToFilter('setbanner',array('eq'=>'1'));
       }
       return $collection;
    }
    
}