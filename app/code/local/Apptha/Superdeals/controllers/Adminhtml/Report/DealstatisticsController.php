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
class Apptha_Superdeals_Adminhtml_Report_DealstatisticsController extends Mage_Adminhtml_Controller_Action {
    
    /**
     * fucntion to view deals statistics details
     *
     * return void
     */
    public function indexAction() {
        $SuperDealReports = 'Super Deal Reports';
        $this->_title ( $this->__ ( 'Reports' ) )->_title ( $this->__ ( $SuperDealReports ) );
        $this->loadLayout ()->_setActiveMenu ( 'superdeals/dealstatistics' )->_addBreadcrumb ( Mage::helper ( 'reports' )->__ ( $SuperDealReports ), Mage::helper ( 'reports' )->__ ( $SuperDealReports ) )->_addContent ( $this->getLayout ()->createBlock ( 'superdeals/adminhtml_report_dealstatistics' ) )->renderLayout ();
    }
    
    /**
     * fucntion to export records as csv file
     *
     * return void
     */
    public function exportDealstatisticsCsvAction() {
        $fileName = 'superdeals_report.csv';
        $content = $this->getLayout ()->createBlock ( 'superdeals/adminhtml_report_dealstatistics_grid' )->getCsv ();
        $this->_prepareDownloadResponse ( $fileName, $content );
    }
    
    /**
     * fucntion to export records as excel file
     *
     * return void
     */
    public function exportDealstatisticsExcelAction() {
        $fileName = 'superdeals_report.xml';
        $content = $this->getLayout ()->createBlock ( 'superdeals/adminhtml_report_dealstatistics_grid' )->getExcel ( $fileName );
        $this->_prepareDownloadResponse ( $fileName, $content );
    }
    
    /**
     * fucntion to Mass delete deals statistics details
     *
     * return void
     */
    public function massDeleteAction() {
        $adminhtmlSession = 'adminhtml/session';
        $serialIds = $this->getRequest ()->getParam ( 'superdeals' );
        /**
         * check condition serial ids array
         */
        if (! is_array ( $serialIds )) {
            Mage::getSingleton ( $adminhtmlSession )->addError ( Mage::helper ( 'adminhtml' )->__ ( 'Please select item(s)' ) );
        } else {
            try {
                /**
                 * load all serial ids data
                 */
                foreach ( $serialIds as $serialRequestIds ) {
                    $sRequest = Mage::getModel ( 'superdeals/dealstatistics' )->load ( $serialRequestIds );
                    $sRequest->delete ();
                }
                Mage::getSingleton ( $adminhtmlSession )->addSuccess ( Mage::helper ( 'adminhtml' )->__ ( 'Total of %d record(s) were successfully deleted', count ( $serialIds ) ) );
            } catch ( Exception $e ) {
                Mage::getSingleton ( $adminhtmlSession )->addError ( $e->getMessage () );
            }
        }
        $this->_redirect ( '*/*/index' );
    }
}

