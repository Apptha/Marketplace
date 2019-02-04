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
class Apptha_Superdeals_Adminhtml_Report_DealzController extends Mage_Adminhtml_Controller_Action {
    
    /**
     * fucntion to view deals order details
     *
     * @return void
     */
    public function indexAction() {
        $superDealOrder = 'Super Deal Orders';
        $this->_title ( $this->__ ( 'Reports' ) )->_title ( $this->__ ( $superDealOrder ) );
        
        $this->loadLayout ()->_setActiveMenu ( 'superdeals/dealz' )->_addBreadcrumb ( Mage::helper ( 'reports' )->__ ( $superDealOrder ), Mage::helper ( 'reports' )->__ ( $superDealOrder ) )->_addContent ( $this->getLayout ()->createBlock ( 'superdeals/adminhtml_report_dealz' ) )->renderLayout ();
    }
    
    /**
     * fucntion to export records as csv file
     *
     * @return void
     */
    public function exportDealzCsvAction() {
        $fileName = 'superdeals_order.csv';
        $content = $this->getLayout ()->createBlock ( 'superdeals/adminhtml_report_dealz_grid' )->getCsv ();
        $this->_prepareDownloadResponse ( $fileName, $content );
    }
    
    /**
     * fucntion to export records as Excel file
     *
     * @return void
     */
    public function exportDealzExcelAction() {
        $xmlfileName = 'superdeals_order.xml';
        $content = $this->getLayout ()->createBlock ( 'superdeals/adminhtml_report_dealz_grid' )->getExcel ( $xmlfileName );
        $this->_prepareDownloadResponse ( $xmlfileName, $content );
    }
    
    /**
     * fucntion to delete deals order details
     *
     * @return void
     */
    public function massDeleteAction() {
        $adminhtmlSession = 'adminhtml/session';
        $serialIdDatas = $this->getRequest ()->getParam ( 'superdeals' );
        if (! is_array ( $serialIdDatas )) {
            Mage::getSingleton ( $adminhtmlSession )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                foreach ( $serialIdDatas as $serialRequestIds ) {
                    $sRequest = Mage::getModel ( 'superdeals/dealz' )->load ( $serialRequestIds );
                    $sRequest->delete ();
                }
                Mage::getSingleton ( $adminhtmlSession )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were successfully deleted', count ( $serialIdDatas ) ) );
            } catch ( Exception $e ) {
                Mage::getSingleton ( $adminhtmlSession )->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
}

