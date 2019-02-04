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
 * Function written in this file are globally accessed
 */
class Apptha_Marketplace_Helper_Market extends Mage_Core_Helper_Abstract {
    /**
     * Set assign product
     *
     * @param number $newAssignProductId            
     * @param array $assignProducts            
     * @return void
     */
    public function setAssignProduct($newAssignProductId, $assignProducts) {
    /**
     * Check whether assign product is not null
     */
        if (! empty ( $newAssignProductId )) {
            $data = array (
                    'assign_product_id' => $newAssignProductId 
            );
            /**
             * Increment foreach loop
             */
            foreach ( $assignProducts as $assignProduct ) {
            /**
             * Get Product Id
             */
                if ($assignProduct->getEntityId ()) {
                /**
                 * Load Product object
                 * @var int
                 */
                    $model = Mage::getModel ( 'catalog/product' )->load ( $assignProduct->getEntityId () )->addData ( $data );
                    $model->setId ( $assignProduct->getEntityId () )->save ();
                }
            }
        }
    }
    
    /**
     * Checking option type value
     *
     * @param string $optionType            
     * @return number $checkOptionTypeValue
     */
    public function checkOptionTypeValue($optionType) {
        $checkOptionTypeValue = 0;
        /**
         * Check option type
         */
        if ($optionType == 'drop_down' || $optionType == 'radio' || $optionType == 'checkbox' || $optionType == 'multiple') {
            $checkOptionTypeValue = 1;
        }
        return $checkOptionTypeValue;
    }
    
    /**
     *
     *
     *
     * Checking for required field
     *
     * @param array $productData            
     * @param array $key            
     * @return number $checkRequiredField
     */
    public function checkRequiredField($productData, $key) {
        $checkRequiredField = 0;
        /**
         * Check whether sku not empty or not
         */
        if (! empty ( $productData ['sku'] [$key] ) && ! empty ( $productData ['name'] [$key] ) && ! empty ( $productData ['description'] [$key] )) {
            $checkRequiredField = 1;
        }
        return $checkRequiredField;
    }
    /**
     * Check required for product save
     *
     * @param aray $productData            
     * @return number $checkRequiredForProductSave
     */
    public function checkRequiredForProductSave($productData) {
        $checkRequiredForProductSave = 0;
        /**
         * Check whether product name empty or not
         */
        if (! empty ( $productData ['name'] ) && ! empty ( $productData ['description'] ) && ! empty ( $productData ['short_description'] )) {
            $checkRequiredForProductSave = 1;
        }
        return $checkRequiredForProductSave;
    }
    
    /**
     * Checking for product required fields for save
     *
     * @param string $sku            
     * @param number $productId            
     * @param string $name            
     * @param string $description            
     * @return number $checkingForProductRequiredFields
     */
    public function checkingForProductRequiredFields($sku, $productId, $name, $description) {
        $checkingForProductRequiredFields = 0;
        /**
         * Check whether sku and name are not empty
         */
        if (! empty ( $sku ) && ! empty ( $productId ) && ! empty ( $name ) && ! empty ( $description )) {
            $checkingForProductRequiredFields = 1;
        }
        return $checkingForProductRequiredFields;
    }
    
    /**
     * Delete configurable product
     *
     * @param array $selectedAssociateProductIds            
     * @return bool
     */
    public function deleteconfigurableproduct($selectedAssociateProductIds) {
        $productDeleteCount = 0;
        /**
         * Increment foreach loop
         */
        foreach ( $selectedAssociateProductIds as $selectedAssociateProductId ) {
            $productSellerId = Mage::getModel ( 'catalog/product' )->load ( $selectedAssociateProductId )->getSellerId ();
            /**
             * Get Seller Id
             */
            if ($productSellerId == Mage::getSingleton ( 'customer/session' )->getCustomerId ()) {
                Mage::getModel ( 'catalog/product' )->setId ( $selectedAssociateProductId )->delete ();
                $productDeleteCount = $productDeleteCount + 1;
            }
        }
        return $productDeleteCount;
    }
    
    /**
     * Create upload product data
     *
     * @param array $createProductData            
     * @param array $productData            
     * @return array $productData
     */
    public function createUploadProductData($createProductData, $productData) {
        if (! empty ( $createProductData )) {
            $productData [] = $createProductData;
        }
        return $productData;
    }
    
    /**
     * Create configurable product data
     *
     * @param array $configurableProductsData            
     * @param number $pricingValue            
     * @param number $attribute            
     * @param number $valueIndex            
     * @param string $pricingLabel            
     * @param number $isPercentValue            
     * @return array $configurableProductsData
     */
    public function createCongirurableProductData($configurableProductsData, $pricingValue, $attribute, $valueIndex, $pricingLabel, $isPercentValue) {
        $configurableProductsData [$attribute] [$valueIndex] = array (
                'product_super_attribute_id' => $attribute,
                'value_index' => $valueIndex,
                'label' => $pricingLabel,
                'default_label' => $pricingLabel,
                'store_label' => $pricingLabel,
                'is_percent' => $isPercentValue,
                'pricing_value' => $pricingValue,
                'use_default_value' => $pricingValue 
        );
        
        return $configurableProductsData;
    }
    
    /**
     * Create config attributes for product
     *
     * @param string $configAttribute            
     * @param array $configAttributes            
     * @param number $arrayCount            
     * @param array $configurableProductsData            
     * @param number $configAttributeIdValue            
     * @return array $configAttributes
     */
    public function createConfigAttributesForProudct($configAttribute, $configAttributes, $arrayCount, $configurableProductsData, $configAttributeIdValue) {
        if (! empty ( $configAttributes [$arrayCount] ['values'] )) {
            $arrayInnerCount = 0;
            $udpatedValueIndex = array ();
            /**
             * Increment foreach loop
             */
            foreach ( $configAttributes [$arrayCount] ['values'] as $optionValue ) {
                $valueIndex = $optionValue ['value_index'];
                $udpatedValueIndex [] = $valueIndex;
                
                /**
                 * Get configurable attribute price value
                 */
                $configAttributes = Mage::helper ( 'marketplace/product' )->getConfigAttributePriceValue ( $configAttributes, $configurableProductsData, $configAttributeIdValue, $valueIndex, $arrayCount, $arrayInnerCount );
                
                if (isset ( $configurableProductsData [$configAttributeIdValue] [$valueIndex] ['is_percent'] )) {
                    $configAttributes [$arrayCount] ['values'] [$arrayInnerCount] ['is_percent'] = $configurableProductsData [$configAttributeIdValue] [$valueIndex] ['is_percent'];
                }
                $arrayInnerCount = $arrayInnerCount + 1;
            }
            /**
             * Increment Foreach loop
             */
            foreach ( $configurableProductsData [$configAttributeIdValue] as $key => $addNewOptionValue ) {
                if (! in_array ( $key, $udpatedValueIndex )) {
                    $addNewOptionValue ['product_super_attribute_id'] = $configAttribute ['id'];
                    $configAttributes [$arrayCount] ['values'] [] = $addNewOptionValue;
                }
            }
        } else {
            foreach ( $configurableProductsData [$configAttributeIdValue] as $configurableProductOptionValue ) {
                $configurableProductOptionValue ['product_super_attribute_id'] = $configAttribute ['id'];
                $configAttributes [$arrayCount] ['values'] [] = $configurableProductOptionValue;
            }
        }
        /**
         * end if clause
         */
        return $configAttributes;
    }
    
    /**
     * Prepare custom option values
     *
     * @param array $customOptions            
     * @param number $customOptionCount            
     * @param array $productData            
     * @param number $nextKey            
     * @param number $customOptionCount            
     * @param number $rowcountForImport            
     * @return array $createCustomOptionData
     */
    public function createCustomOption($customOptions, $customOptionCount, $productData, $nextKey, $rowcountForImport) {
        $createCustomOptionData = array ();
        $optionValues = array ();
        $isDateType = Mage::getModel ( 'marketplace/customoption' )->isDateType ( $productData, $nextKey );
        $isFieldType = Mage::getModel ( 'marketplace/customoption' )->isFieldType ( $productData, $nextKey );
        if ($isFieldType == 1) {
            $customOptions = Mage::getModel ( 'marketplace/customoption' )->getCustomOptionFieldData ( $customOptions, $customOptionCount, $productData, $nextKey, 'field' );
            $customOptionCount = $customOptionCount + 1;
        } elseif ($productData ['_custom_option_type'] [$nextKey] == 'file') {
            $customOptions = Mage::getModel ( 'marketplace/customoption' )->getCustomOptionFileData ( $customOptions, $customOptionCount, $productData, $nextKey );
            $customOptionCount = $customOptionCount + 1;
        } elseif ($isDateType == 1) {
            $customOptions = Mage::getModel ( 'marketplace/customoption' )->getCustomOptionFieldData ( $customOptions, $customOptionCount, $productData, $nextKey, 'date' );
            $customOptionCount = $customOptionCount + 1;
        } else {
            $customOptionValueKey = $nextKey;
            $customOptionValueCount = 0;
            while ( empty ( $productData ['_custom_option_type'] [$customOptionValueKey] ) && empty ( $productData ['sku'] [$customOptionValueKey] ) && $rowcountForImport > $customOptionValueKey || $customOptionValueKey == $nextKey ) {
                $optionValues = array ();
                $customOptions = Mage::getModel ( 'marketplace/customoption' )->getCustomOptionData ( $productData, $customOptionValueKey, $customOptions, $customOptionCount, $customOptionValueCount );
                $customOptionValueKey = $customOptionValueKey + 1;
                $customOptionValueCount = $customOptionValueCount + 1;
            }
            $customOptionCount = $customOptionCount + 1;
        }
        
        $createCustomOptionData ['custom_options'] = $customOptions;
        $createCustomOptionData ['custom_option_count'] = $customOptionCount;
        return $createCustomOptionData;
    }
    
    /**
     * Prepare configurable product data
     *
     * @param array $configurableProductsData            
     * @param string $attributeCode            
     * @param string $superAttributeOption            
     * @param number $attributeId            
     * @param string $label            
     * @param number $isPercent            
     * @param number $pricingValue            
     */
    public function prepareConfigurableProductData($configurableProductsData, $attributeCode, $superAttributeOption, $attributeId, $label, $isPercent, $pricingValue) {
        $superAttributeOptionId = '';
        $productModel = Mage::getModel ( 'catalog/product' );
        $attr = $productModel->getResource ()->getAttribute ( $attributeCode );
        if ($attr->usesSource ()) {
            $superAttributeOptionId = $attr->getSource ()->getOptionId ( $superAttributeOption );
        }
        if (! empty ( $superAttributeOptionId )) {
            $configurableProductsData [$attributeId] [] = array (
                    'product_super_attribute_id' => $attributeId,
                    'label' => $label,
                    'value_index' => $superAttributeOptionId,
                    'is_percent' => $isPercent,
                    'pricing_value' => $pricingValue 
            );
        }
    }
    /**
     * Prepare to set config attribute data
     *
     * @param array $configurableAttributesData            
     * @param array $configurableProductsData            
     * @return array $configurableAttributesData
     */
    public function prepareToSetConfigAttributeData($configurableAttributesData, $configurableProductsData) {
        $countForAssignAttributeValues = 0;
        foreach ( $configurableAttributesData as $attributesData ) {
            if (isset ( $attributesData ['attribute_id'] )) {
                $attributeIdForConfig = $attributesData ['attribute_id'];
                if (array_key_exists ( $attributeIdForConfig, $configurableProductsData )) {
                    $configurableAttributesData [$countForAssignAttributeValues] ['values'] = $configurableProductsData [$attributeIdForConfig];
                }
            }
            $countForAssignAttributeValues = $countForAssignAttributeValues + 1;
        }
        return $configurableAttributesData;
    }
    /**
     *
     *
     *
     * Get option value for config
     *
     * @param array $attr            
     * @param array $productData            
     * @param string $configurableAttribute            
     * @param number $key            
     * @return string $optionValueFoConfig
     */
    public function getOptionValueForConfig($attr, $productData, $configurableAttribute, $key) {
        $optionValueFoConfig = '';
        if ($attr->usesSource ()) {
            $optionValueFoConfig = $attr->getSource ()->getOptionId ( $productData [$configurableAttribute] [$key] );
        }
        return $optionValueFoConfig;
    }
    /**
     * Get sample file name
     *
     * @param array $sampleModel            
     * @param array $sample            
     * @return string $sampleFileName
     */
    public function getSampleFileName($sampleModel, $sample) {
        $sampleFileName = '';
        if ($sampleModel->getSampleType () == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
            $sampleFileName = Mage::helper ( 'downloadable/file' )->moveFileFromTmp ( Mage_Downloadable_Model_Sample::getBaseTmpPath (), Mage_Downloadable_Model_Sample::getBasePath (), $sample );
        }
        return $sampleFileName;
    }
    
    /**
     * Get shipping price
     *
     * @param string $sellerDefaultCountry            
     * @param number $shippingCountryId            
     * @param number $orderPrice            
     * @param number $nationalShippingPrice            
     * @param number $productQty            
     * @return number $shippingPrice
     */
    public function getShippingPrice($sellerDefaultCountry, $shippingCountryId, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty) {
        if ($sellerDefaultCountry == $shippingCountryId) {
            $shippingPrice = $orderPrice + ($nationalShippingPrice * $productQty);
        } else {
            $shippingPrice = $orderPrice + ($internationalShippingPrice * $productQty);
        }
        return $shippingPrice;
    }
    
    /**
     * Get Seller shipping price
     * @param unknown $sellerDefaultCountry
     * @param unknown $shippingCountryId
     * @param unknown $orderPrice
     * @param unknown $nationalShippingPrice
     * @param unknown $internationalShippingPrice
     * @param unknown $productQty
     * @return number
     */
    public function getSellerShippingPrice($sellerDefaultCountry, $shippingCountryId, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty) {
        if ($sellerDefaultCountry == $shippingCountryId) {
            $shippingPrice =  ($nationalShippingPrice * $productQty);
            } else {
        $shippingPrice =  ($internationalShippingPrice * $productQty);
        }
       return $shippingPrice;
    }
    /**
     * Calculate shipping price
     *
     * @param number $sellerId            
     * @param string $sellerDefaultCountry            
     * @param string $shippingCountryId            
     * @param number $orderPrice            
     * @param number $nationalShippingPrice            
     * @param number $internationalShippingPrice            
     * @param number $productQty            
     * @return number $shippingPrice
     */
    public function calculateShippingPrice($sellerId, $sellerDefaultCountry, $shippingCountryId, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty) {
        $shippingPrice = '';
        if ($sellerId) {
            /**
             * Check seller Default Country and Shipping country Id are same
             * if so calculate shipping price using national shipping price
             */
            $shippingPrice = $this->getShippingPrice ( $sellerDefaultCountry, $shippingCountryId, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty );
        }
        return $shippingPrice;
    }
    
    /**
     * Udate commission data by id
     *
     * @param number $commissionId            
     * @param string $statusOrder            
     */
    public function updateCommissionDataById($commissionId, $statusOrder) {
        /**
         * Check commission Id is not empty
         */
        if (! empty ( $commissionId )) {
            Mage::helper ( 'marketplace/marketplace' )->updateCommissionData ( $statusOrder, $commissionId );
        }
    }
    /**
     * Get shipping details
     *
     * @param number $sellerShippingEnabled            
     * @param string $productType            
     * @param array $products            
     * @param array $orders            
     * @return array $getShippingDetails;
     */
    public function getShippingDetails($sellerShippingEnabled, $productType, $products, $orders) {
        $getShippingDetails = array ();
        $getShippingDetails ['national_shipping_price'] = '';
        $getShippingDetails ['international_shipping_price'] = '';
        $getShippingDetails ['seller_default_country'] = '';
        $getShippingDetails ['shipping_country_id'] = '';
        /**
         * Get Shipping enabled and type
         */
        if ($sellerShippingEnabled == 1 && $productType == 'simple') {
            /**
             * Get National shipping price
             */
            $nationalShippingPrice = $products->getNationalShippingPrice ();
            /**
             * Get InterNational shipping price
             */
            $internationalShippingPrice = $products->getInternationalShippingPrice ();
            /**
             * Get Seller Default country
             */
            $sellerDefaultCountry = $products->getDefaultCountry ();
            /**
             * Get Shipping country Id
             */
            $shippingCountryId = $orders->getShippingAddress ()->getCountry ();
            $getShippingDetails ['national_shipping_price'] = $nationalShippingPrice;
            $getShippingDetails ['international_shipping_price'] = $internationalShippingPrice;
            $getShippingDetails ['seller_default_country'] = $sellerDefaultCountry;
            $getShippingDetails ['shipping_country_id'] = $shippingCountryId;
        }
        return $getShippingDetails;
    }
    /**
     * Get email template
     *
     * @param number $productStatus            
     * @return string $emailTemplate
     */
    public function getEmailTemplate($productStatus) {
    /**
     * Check whether product status is enabled or disabled
     */
        if ($productStatus == 1) {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_product_addproductenabledemailnotificationtemplate' );
        } else {
            $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_product_addproductdisabledemailnotificationtemplate' );
        }
        return $emailTemplate;
    }
}