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
* @version     1.9.0
* @author      Apptha Team <developers@contus.in>
* @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
* @license     http://www.apptha.com/LICENSE.txt
* 
*/

/**
 * This file is used to product quick view functionality
 */
class Apptha_Quickview_AjaxController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Product index action
     */
    public function indexAction() {
        $productId = ( int ) $this->getRequest ()->getParam ( 'id' );
        $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
        Mage::dispatchEvent ( 'catalog_controller_product_view', array (
                'product' => $product 
        ) );
        Mage::helper ( 'catalog/product' )->initProduct ( $productId, $this );
        Mage::getSingleton ( 'catalog/session' )->setLastViewedProductId ( $product->getId () );
        $this->loadLayout ();
        $this->renderLayout ();
    }
}