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
 * This file is used to maintain seller payment information
 */
class Apptha_Marketplace_Adminhtml_PaymentinfoController extends Mage_Adminhtml_Controller_Action {
    protected function _initAction() {
        $this->loadLayout ()->_setActiveMenu ( 'marketplace/items' )->_addBreadcrumb ( Mage::helper ( 'adminhtml' )->__ ( 'Items Manager' ), Mage::helper ( 'adminhtml' )->__ ( 'Item Manager' ) );
        
        return $this;
    }
    /**
     * Load phtml file layout
     *
     * @return void
     */
    public function indexAction() {
        $this->_initAction ()->renderLayout ();
    }
    /**
     * Export transaction info as csv file
     *
     * @return void
     */
    public function exportCsvAction() {
        $fileName = 'transaction.csv';
        $content = $this->getLayout ()->createBlock ( 'marketplace/adminhtml_paymentinfo_grid' )->getCsvFile ();
        $this->_prepareDownloadResponse ( $fileName, $content );
    }
    /**
     * Export transaction info as xml file
     *
     * @return void
     */
    public function exportXmlAction() {
        $fileName = 'transaction.xml';
        $content = $this->getLayout ()->createBlock ( 'marketplace/adminhtml_paymentinfo_grid' )->getExcelFile ();
        $this->_prepareDownloadResponse ( $fileName, $content );
    }
    /**
     * Resolved acl issue while adding secure batch or magento 1.9.2
     */
    protected function _isAllowed() {
        return true;
    }
} 