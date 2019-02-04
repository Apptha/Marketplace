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
 * This class contains functions for commission collection
 * 
 **/
class Apptha_Marketplace_Model_Mysql4_Commission_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    /**
     * Load Default Constructor
     */
    public function _construct() {
        /**
         * Load Parent Constructor
         */
        parent::_construct ();
        /**
         * Initialize commission Block
         */
        $this->_init ( 'marketplace/commission' );
    }
    
    /**
     * Get SQL for get record count
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql() {
        $this->_renderFilters ();
        /**
         * Filter by order,limit,columns
         * @var db
         */
        $countSelect = clone $this->getSelect ();
        $countSelect->reset ( Zend_Db_Select::ORDER );
        $countSelect->reset ( Zend_Db_Select::LIMIT_COUNT );
        $countSelect->reset ( Zend_Db_Select::LIMIT_OFFSET );
        $countSelect->reset ( Zend_Db_Select::COLUMNS );
        
        if (count ( $this->getSelect ()->getPart ( Zend_Db_Select::GROUP ) ) > 0) {
            $countSelect->reset ( Zend_Db_Select::GROUP );
            $countSelect->distinct ( true );
            $group = $this->getSelect ()->getPart ( Zend_Db_Select::GROUP );
            $countSelect->columns ( "COUNT(DISTINCT " . implode ( ", ", $group ) . ")" );
        } else {
            $countSelect->columns ( 'COUNT(*)' );
        }
        return $countSelect;
    }
} 