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
class Apptha_Superdeals_Model_Mysql4_Dealstatistics_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    public function _construct() {
        $this->_init ( "superdeals/dealstatistics" );
    }
    
    /**
     * Join fields
     *
     * @param string $from            
     * @param string $to            
     * @return Mage_Reports_Model_Resource_Customer_Totals_Collection
     */
    protected function _joinFields() {
        return $this;
    }
    
    /**
     * Set date range
     *
     * @param string $from            
     * @param string $to            
     * @return Mage_Reports_Model_Resource_Customer_Totals_Collection
     */
    public function setDateRange($from, $to) {
        $this->_reset ()->_joinFields ( $from, $to );
        return $this;
    }
    
    /**
     * Set store filter collection
     *
     * @param array $storeIds            
     * @return Mage_Reports_Model_Resource_Customer_Totals_Collection
     */
    public function setStoreIds() {
        return $this;
    }
}
