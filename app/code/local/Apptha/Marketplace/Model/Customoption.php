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
 * Function written in this file are used for product custom options manipulation
 */
class Apptha_Marketplace_Model_Customoption extends Mage_Core_Model_Abstract {
    
    /**
     * Get custom option file data
     *
     * @param array $customOptions            
     * @param number $customOptionCount            
     * @param array $productData            
     * @param number $nextKey            
     * @return array $customOptions
     */
    public function getCustomOptionFileData($customOptions, $customOptionCount, $productData, $nextKey) {
        $customOptions ['options'] [$customOptionCount] ['price'] = $productData ['_custom_option_price'] [$nextKey];
        $customOptions ['options'] [$customOptionCount] ['sku'] = $productData ['_custom_option_sku'] [$nextKey];
        if (isset ( $productData ['_custom_option_file_extension'] [$nextKey] )) {
            $customOptions ['options'] [$customOptionCount] ['file_extension'] = $productData ['_custom_option_file_extension'] [$nextKey];
        }
        if (isset ( $productData ['_custom_option_image_size_x'] [$nextKey] )) {
            $customOptions ['options'] [$customOptionCount] ['image_size_x'] = $productData ['_custom_option_image_size_x'] [$nextKey];
        }
        if (isset ( $productData ['_custom_option_image_size_y'] [$nextKey] )) {
            $customOptions ['options'] [$customOptionCount] ['image_size_y'] = $productData ['_custom_option_image_size_y'] [$nextKey];
        }
        if (strpos ( $customOptions ['options'] [$customOptionCount] ['price'], '%' ) !== false) {
            $customOptions ['options'] [$customOptionCount] ['price'] = str_replace ( "%", "", $customOptions ['options'] [$customOptionCount] ['price'] );
            $customOptions ['options'] [$customOptionCount] ['price_type'] = 'percent';
        } else {
            $customOptions ['options'] [$customOptionCount] ['price_type'] = 'fixed';
        }
        return $customOptions;
    }
    
    /**
     * Get custom option Field & date data
     *
     * @param array $customOptions            
     * @param number $customOptionCount            
     * @param array $productData            
     * @param number $nextKey            
     * @return array $customOptions
     */
    public function getCustomOptionFieldData($customOptions, $customOptionCount, $productData, $nextKey, $optionForVar) {
        $customOptions ['options'] [$customOptionCount] ['price'] = $productData ['_custom_option_price'] [$nextKey];
        $customOptions ['options'] [$customOptionCount] ['sku'] = $productData ['_custom_option_sku'] [$nextKey];
        
        if ($optionForVar = 'date') {
            $customOptions ['options'] [$customOptionCount] ['max_characters'] = $productData ['_custom_option_max_characters'] [$nextKey];
        }
        if (strpos ( $customOptions ['options'] [$customOptionCount] ['price'], '%' ) !== false) {
            $customOptions ['options'] [$customOptionCount] ['price_type'] = 'percent';
            $customOptions ['options'] [$customOptionCount] ['price'] = str_replace ( "%", "", $customOptions ['options'] [$customOptionCount] ['price'] );
        } else {
            $customOptions ['options'] [$customOptionCount] ['price_type'] = 'fixed';
        }
        return $customOptions;
    }
    
    /**
     * Get custom option data for bulk product
     *
     * @param array $productData            
     * @param number $customOptionValueKey            
     * @param array $customOptions            
     * @param number $customOptionCount            
     * @param number $customOptionValueCount            
     * @return array $customOptions
     */
    public function getCustomOptionData($productData, $customOptionValueKey, $customOptions, $customOptionCount, $customOptionValueCount) {
        if (! empty ( $productData ['_custom_option_row_title'] [$customOptionValueKey] ) && ! empty ( $productData ['_custom_option_row_price'] [$customOptionValueKey] )) {
            $customOptions ['options'] [$customOptionCount] ['values'] [$customOptionValueCount] ['title'] = $productData ['_custom_option_row_title'] [$customOptionValueKey];
            $customOptions ['options'] [$customOptionCount] ['values'] [$customOptionValueCount] ['sku'] = $productData ['_custom_option_row_sku'] [$customOptionValueKey];
            $customOptions ['options'] [$customOptionCount] ['values'] [$customOptionValueCount] ['sort_order'] = $productData ['_custom_option_row_sort'] [$customOptionValueKey];
            if (strpos ( $productData ['_custom_option_row_price'] [$customOptionValueKey], '%' ) !== false) {
                $customOptions ['options'] [$customOptionCount] ['values'] [$customOptionValueCount] ['price_type'] = 'percent';
                $customOptions ['options'] [$customOptionCount] ['values'] [$customOptionValueCount] ['price'] = str_replace ( "%", "", $productData ['_custom_option_row_price'] [$customOptionValueKey] );
            } else {
                $customOptions ['options'] [$customOptionCount] ['values'] [$customOptionValueCount] ['price_type'] = 'fixed';
                $customOptions ['options'] [$customOptionCount] ['values'] [$customOptionValueCount] ['price'] = $productData ['_custom_option_row_price'] [$customOptionValueKey];
            }
        }
        return $customOptions;
    }
    /**
     * Get custom option file data
     *
     * @param array $customOptions            
     * @param number $customOptionCount            
     * @param array $productData            
     * @param number $nextKey            
     * @return array $customOptions
     */
    public function getCustomOptionSortOrder($customOptions, $customOptionCount, $productData, $nextKey) {
        $customOptions ['options'] [$customOptionCount] ['type'] = $productData ['_custom_option_type'] [$nextKey];
        $customOptions ['options'] [$customOptionCount] ['title'] = $productData ['_custom_option_title'] [$nextKey];
        if (isset ( $productData ['_custom_option_is_required'] [$nextKey] )) {
            $customOptions ['options'] [$customOptionCount] ['is_require'] = $productData ['_custom_option_is_required'] [$nextKey];
        }
        if (isset ( $productData ['_custom_option_sort_order'] [$nextKey] )) {
            $customOptions ['options'] [$customOptionCount] ['sort_order'] = $productData ['_custom_option_sort_order'] [$nextKey];
        }
        return $customOptions;
    }
    /**
     * Is date type for custom option
     *
     * @param array $productData            
     * @param number $nextKey            
     * @return number $isDataType
     */
    public function isDateType($productData, $nextKey) {
        $isDateType = 0;
        if ($productData ['_custom_option_type'] [$nextKey] == 'date' || $productData ['_custom_option_type'] [$nextKey] == 'date_time' || $productData ['_custom_option_type'] [$nextKey] == 'time') {
            $isDateType = 1;
        }
        return $isDateType;
    }
    
    /**
     * Is sku exist or not
     *
     * @param array $productData            
     * @param number $nextKey            
     * @param number $key            
     * @return number $isSkuExist
     */
    public function isSkuExist($productData, $nextKey, $key) {
        $isSkuExist = 0;
        if (empty ( $productData ['sku'] [$nextKey] ) || $nextKey == $key) {
            $isSkuExist = 1;
        }
        return $isSkuExist;
    }
    /**
     * Is field type
     *
     * @param array $productData            
     * @param number $nextKey            
     * @return number $isFieldType
     */
    public function isFieldType($productData, $nextKey) {
        $isFieldType = 0;
        if ($productData ['_custom_option_type'] [$nextKey] == 'field' || $productData ['_custom_option_type'] [$nextKey] == 'area') {
            $isFieldType = 1;
        }
        return $isFieldType;
    }
} 