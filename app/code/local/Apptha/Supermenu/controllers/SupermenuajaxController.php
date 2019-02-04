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
 * Loading header menu using ajax for web
 */
class Apptha_Supermenu_SupermenuajaxController extends Mage_Core_Controller_Front_Action {
 
 /**
  * Render json response as menu in header for web
  *
  * Return the menu content as json response
  * return array
  */
 public function indexAction() {
  $menu = Mage::helper ( 'supermenu' )->getSuperMenuContent ();
  $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $menu ) );
 }
}
