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
class Apptha_Superdeals_Model_Slideorder {
    /**
     * Notify mail details by getting number of days
     */
    public function toOptionArray() {
        /**
         * Declare repeted string variable
         */
        $value = 'value';
        $label = 'label';
        $superdeals = 'superdeals';
        return array (
                array (
                        $value => 1,
                        $label => Mage::helper ( $superdeals )->__ ( 'Lower to High Price' ) 
                ),
                array (
                        $value => 2,
                        $label => Mage::helper ( $superdeals )->__ ( 'Higher to Low Price' ) 
                ),
                array (
                        $value => 3,
                        $label => Mage::helper ( $superdeals )->__ ( 'Random Display' ) 
                ) 
        );
    }
}
