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
require_once Mage::getModuleDir ( 'controllers', 'Mage_CatalogSearch' ) . DS . 'ResultController.php';
/**
 * Catalog Search Controller
 */
class Apptha_Marketplace_CatalogSearch_ResultController extends Mage_Core_Controller_Front_Action{
/**
 * Retrieve catalog session
 *
 * @return Mage_Catalog_Model_Session
 */
protected function _getSession() {
return Mage::getSingleton ( 'catalog/session' );
}
/**
 * Display search result
 */
public function indexAction() {
$query = Mage::helper ( 'catalogsearch' )->getQuery ();
/**
 * Get Store Id
 */

$query->setStoreId ( Mage::app ()->getStore ()->getId () );
/**
 * Register core variable
 */
Mage::register ( 'is_search_request', true );
if ($query->getQueryText () != '') {
if (Mage::helper ( 'catalogsearch' )->isMinQueryLength ()) {
$query->setId ( 0 )->setIsActive ( 1 )->setIsProcessed ( 1 );
} else {
if ($query->getId ()) {
/**
 * Set Popularity
 */
$query->setPopularity ( $query->getPopularity () + 1 );
} else {
$query->setPopularity ( 1 );
}

if ($query->getRedirect ()) {
$query->save ();
$this->getResponse ()->setRedirect ( $query->getRedirect () );
return;
} else {
$query->prepare ();
}
}
/**
 * End if
 */
Mage::helper ( 'catalogsearch' )->checkNotes ();
/**
 * load and render layout
 */
$this->loadLayout ();
$this->_initLayoutMessages ( 'catalog/session' );
$this->_initLayoutMessages ( 'checkout/session' );
$this->renderLayout ();

if (! Mage::helper ( 'catalogsearch' )->isMinQueryLength ()) {
$query->save ();
}
} else {
$this->_redirectReferer ();
}
}
}