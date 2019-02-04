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
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */
?>
<?php
/**
 * This class contains banner slider layout
 */
class Apptha_Marketplace_Block_Sidebar extends Mage_Core_Block_Template {
    /**
     * Set template for banner
     */
    protected function _beforeToHtml() {
         parent::_beforeToHtml ();
         if (! $this->getTemplate ()) {
            $this->setTemplate ( 'marketplace/sidebar.phtml' );
         }
        return $this;
    }
    
    /**
     * Prepare layout
     */
    protected function _prepareLayout() {
        /**
         * Seting js and css file
         */
        
        return parent::_prepareLayout ();
    }
    
    /**
     * Retrieve a collection of banner sliders
     */
    public function getBannerSliders() {
        return Mage::getModel('marketplace/sidebar')->getCollection()->addFieldToFilter('status',1)->setOrder('sort','ASC');
    }
} 