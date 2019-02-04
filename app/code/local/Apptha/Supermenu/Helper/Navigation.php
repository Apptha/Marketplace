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
 * Super Menu extension Helper file
 */
class Apptha_Supermenu_Helper_Navigation extends Mage_Core_Helper_Abstract {
    /**
     * Checking for target and menu
     *
     * @param array $target            
     * @return $checkingForTargetAndMenu
     */
    public function checkingForTargetAndMenu($target) {
        /**
         * Assign the value initialy set zero
         */
        $checkingForTargetAndMenu = 0;
        if (( int ) Mage::getStoreConfig ( 'supermenu/columns/integrate' ) && count ( $target )) {
            $checkingForTargetAndMenu = 1;
        }
        /**
         * Return the $checkingForTargetAndMenu variable it is hold on boolean value
         */
        return $checkingForTargetAndMenu;
    }
    
    /**
     * Get maximum value
     *
     * @param number $max            
     * @param number $count            
     * @param number $max            
     */
    public function getMaxValue($max, $count) {
        if ($max < $count) {
            $max = $count;
        }
        /**
         * return the maximum number
         */
        return $max;
    }
    /**
     * Checking for max and count
     *
     * @param number $cnt            
     * @param number $max            
     * @param number $column            
     * @return number $checkingForMaxAndCount
     */
    public function checkingForMaxAndCount($cnt, $max, $column) {
        /**
         * Assign the value initialy set zero
         */
        $checkingForMaxAndCount = 0;
        if ($cnt > $max && count ( $column )) {
            $checkingForMaxAndCount = 1;
        }
        /**
         *  return the $checkingForMaxAndCount. 
         *  It is hold a boolean value
         */
        return $checkingForMaxAndCount;
    }
    
    /**
     * Check max and count
     *
     * @param number $max            
     * @param number $target            
     * @return number $checkMaxAndCount
     */
    public function checkMaxAndCount($max, $target) {
        /**
         * Assign the value initialy set zero
         */
        $checkMaxAndCount = 0;
        if ($max > 1 && count ( $target ) > 1) {
            $checkMaxAndCount = 1;
        }
        return $checkMaxAndCount;
    }
    /**
     * Get target columns value
     *
     * @param array $target            
     * @param number $nextKey            
     * @param array $xColumnsLength            
     * @param array $xColumns            
     * @return array $target
     */
    public function getTargetColumns($target, $nextKey, $xColumnsLength, $xColumns) {
        foreach ( $target as $key => $column ) {
            /**
             * Check the key if and next key is equal or not
             * If is true this iteration is continue next loop
             */
            if ($key == $nextKey) {
                continue;
            }
            /**
             * check condition colum length is equal to 1
             */
            if ($xColumnsLength [$key] == 1) {
                /**
                 * merge with next column
                 */
                $nextKey = $key + 1;
                if (isset ( $target [$nextKey] ) && count ( $target [$nextKey] )) {
                    $xColumns [] = array_merge ( $column, $target [$nextKey] );
                    continue;
                }
            }
            /**
             * Add the values to $xColumns array
             */
            $xColumns [] = $column;
        }
        /**
         * Return array value
         */
        return $xColumns;
    }
}
