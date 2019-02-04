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
class Apptha_Superdeals_Model_Words {
    /**
     * Notify mail details by getting number of days
     */
    public function toOptionArray() {
        /**
         * Declare repeated string variable
         */
        $values = 'value';
        $labels = 'label';
        $superdeals = 'superdeals';
        return array (
                array (
                        $values => 1,
                        $labels => Mage::helper ( $superdeals )->__ ( '1 Day' ) 
                ),
                array (
                        $values => 2,
                        $labels => Mage::helper ( $superdeals )->__ ( '2 Days' ) 
                ),
                array (
                        $values => 3,
                        $labels => Mage::helper ( $superdeals )->__ ( '3 Days' ) 
                ),
                array (
                        $values => 4,
                        $labels => Mage::helper ( $superdeals )->__ ( '4 Days' ) 
                ) 
        );
    }
}
