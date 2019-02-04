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
 * Super menu in header on hover
 */
class Apptha_Supermenu_Block_Supermenu extends Mage_Core_Block_Template {
 
 /**
  * On page load header menu will be displayed
  *
  * Return the menu content(text)
  * 
  * @return string
  */
 public function _prepareLayout() {
  /**
   * check condition super genral menu is enable or not
   * @return empty
   */
  if (! Mage::getStoreConfig ( 'supermenu/general/enabled' ) || Mage::helper('supermenu')->isMobile()) {
   return;
  }
  $layout = $this->getLayout ();
  $topnav = $layout->getBlock ( 'catalog.topnav' );
  /**
   * check condition topnav value is object
   */
  if (is_object ( $topnav )) {
   $topnav->setTemplate ( 'supermenu/topmenu.phtml' );
   $head = $layout->getBlock ( 'head' );
   $head->addItem ( 'skin_js', 'js/supermenu/webtoolkit.base64.js' );
   $head->addItem ( 'skin_js', 'js/supermenu/supermenu.js' );
   $head->addItem ( 'skin_css', 'css/supermenu.css' );   
   
   /**
    * Insert menu content
    */
   if (! Mage::getStoreConfig ( 'supermenu/general/ajax_load_content' )) {
    $menu = $layout->getBlock ( 'supermenu-content' );
    /**
     * check condition menu content is object
     */
    if (! is_object ( $menu )) {
     $menu = $layout->createBlock ( 'core/template', 'supermenu' )->setTemplate ( 'supermenu/content.phtml' );
    }
    $position = $layout->getBlock ( 'before_body_end' );
    /**
     * check condition position target is object
     */
    if (is_object ( $position )) {
     $position->append ( $menu );
    }
   }
  }
 }
}
