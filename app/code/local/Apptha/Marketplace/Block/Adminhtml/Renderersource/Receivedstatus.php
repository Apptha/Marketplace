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
 * Renderer to get the payment received status os seller
 */
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Receivedstatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
 
 /**
  * Function to render the received status of payment from seller
  *
  * Return the payment received status
  * 
  * @return varchar
  */
 public function render(Varien_Object $row) {
  $receivedStatus = '';
  $id = $row->getData ();
  foreach ( $id as $_id ) {
   $paymentStatus = Mage::getModel ( 'marketplace/transaction' )->getPaymentstatus ( $_id );
   foreach ( $paymentStatus as $_paymentStatus ) {
    if (isset ( $_paymentStatus ['received_status'] )) {
     $receivedStatus = $_paymentStatus ['received_status'];
    }
    /**
     * Checking the payment received status
     */
    if ($receivedStatus == 1) {
     return 'Yes';
    } else {
     return 'No';
    }
   
   }
  }
 }

}

