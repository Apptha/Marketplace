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
 * Function written in this file are used for bulk upload product manipulation
 */
class Apptha_Marketplace_Model_Bulk extends Mage_Core_Model_Abstract {
    /**
     * Get countries values
     *
     * @return array $countries
     */
    public function getContriesValue() {
        /**
         * Setting country name and code
         */
        $countries = array ();
        /**
         * Get Directory Collection
         * @var obj
         */
        $collection = Mage::getModel ( 'directory/country' )->getCollection ();
        /**
         * Increment foreach loop
         */
        foreach ( $collection as $country ) {
        /**
         * Get Country name and id
         */
            $countries [$country->getName ()] = $country->getId ();
        }
        /**
         * Return countries
         */
        return $countries;
    }
    /**
     * Get weight for bulk upoload
     */
    public function getWeightForBulkUpload($productData, $key, $type) {
    /**
     * Check whether product type is simple and weight not equal to null
     */
        if (! isset ( $productData ['weight'] [$key] ) && $type == 'simple') {
            $weight = 0;
        } else {
            $weight = $productData ['weight'] [$key];
        }
        /**
         * end if
         */
        return $weight;
    }
    
    /**
     * Get Images for Bulk product upload
     *
     * @param number $key            
     * @param array $productData            
     * @return array images
     */
    public function getImagesForBulkProduct($key, $productData) {
        $images = array ();
        $nextKey = $key;
        while ( isset ( $productData ['sku'] [$nextKey] ) ) {
            if (empty ( $productData ['sku'] [$nextKey] ) || $nextKey == $key) {
                if (! empty ( $productData ['image'] [$nextKey] )) {
                    $parts = explode ( '/', rtrim ( $productData ['image'] [$nextKey], '/' ) );
                    $images [] = array_pop ( $parts );
                }
            } else {
                break;
            }
           /**
            * end if
            * 
            */
            $nextKey = $nextKey + 1;
        }
        /**
         * end while
         */
        return $images;
    }
    
    /**
     * Get categoryids for Bulk product upload
     *
     * @param number $key            
     * @param array $productData            
     * @return array
     */
    public function getCategoryIdsForBulk($key, $productData) {
        $categoryNames = array ();
        /**
         * Fetch category info for product
         */
        $nextKey = $key;
        /**
         * Increment while loop
         */
        while ( isset ( $productData ['sku'] [$nextKey] ) ) {
            if (empty ( $productData ['sku'] [$nextKey] ) || $nextKey == $key) {
                if (! empty ( $productData ['_category'] [$nextKey] )) {
                    $categoryNames [] = trim ( $productData ['_category'] [$nextKey] );
                }
            } else {
                break;
            }
            /**
             * end if
             * 
             */
            $nextKey = $nextKey + 1;
        }
        /**
         * end while
         * @var obj
         */
        
        /**
         * Get Category Collection
         * Filter by name
         */
        $categoryCollection = Mage::getResourceModel ( 'catalog/category_collection' )->addFieldToFilter ( 'name', array (
                'in' => $categoryNames 
        ) );
        /**
         * Array unique of category ids
         */
        return array_unique ( $categoryCollection->getColumnValues ( 'entity_id' ) );
    }
    
    /**
     * Get custom options for bulk product upload
     *
     * @param number $key            
     * @param array $productData            
     * @param number $rowcountForImport            
     * @return array $customOptions
     */
    public function getCustomOptionsForBulk($key, $productData, $rowcountForImport) {
        /**
         * Fetch product options
         */
        $customOptions = array ();
        $nextKey = $key;
        $customOptionCount = 0;
        /**
         * Increment while loop
         */
        while ( isset ( $productData ['sku'] [$nextKey] ) && $rowcountForImport > $nextKey ) {
            $isSkuExist = Mage::getModel ( 'marketplace/customoption' )->isSkuExist ( $productData, $nextKey, $key );
            if ($isSkuExist == 1) {
                if (! empty ( $productData ['_custom_option_type'] [$nextKey] )) {
                    $customOptions = Mage::getModel ( 'marketplace/customoption' )->getCustomOptionSortOrder ( $customOptions, $customOptionCount, $productData, $nextKey );
                    $createCustomOptionData = Mage::helper ( 'marketplace/market' )->createCustomOption ( $customOptions, $customOptionCount, $productData, $nextKey, $rowcountForImport );
                    $customOptions = $createCustomOptionData ['custom_options'];
                    $customOptionCount = $createCustomOptionData ['custom_option_count'];
                }
            } else {
                break;
            }
            /**
             * end if
             * 
             */
            $nextKey = $nextKey + 1;
        }
        /**
         * end while
         */
        return $customOptions;
    }
    
    /**
     * Get data for bulk product upload
     *
     * @param number $key            
     * @param array $productData            
     * @return array $dataForBulkUpload
     */
    public function getDataForBulkProduct($key, $productData) {
        $dataForBulkUpload = array ();
        
        if (isset ( $productData ['tax_class_id'] [$key] )) {
            $taxClassId = $productData ['tax_class_id'] [$key];
        } else {
            $taxClassId = 0;
        }
        if (isset ( $productData ['visibility'] [$key] )) {
            $visibility = $productData ['visibility'] [$key];
        } else {
            $visibility = 4;
        }
        
        if (isset ( $productData ['meta_description'] [$key] )) {
            $metaDescription = $productData ['meta_description'] [$key];
        } else {
            $metaDescription = '';
        }
        
        if (isset ( $productData ['meta_keyword'] [$key] )) {
            $metaKeyword = $productData ['meta_keyword'] [$key];
        } else {
            $metaKeyword = '';
        }
        
        if (isset ( $productData ['meta_title'] [$key] )) {
            $metaTitle = $productData ['meta_title'] [$key];
        } else {
            $metaTitle = '';
        }
        /**
         * end if
         */
        $dataForBulkUpload ['tax_class_id'] = $taxClassId;
        $dataForBulkUpload ['visibility'] = $visibility;
        $dataForBulkUpload ['meta_description'] = $metaDescription;
        $dataForBulkUpload ['meta_keyword'] = $metaKeyword;
        $dataForBulkUpload ['meta_title'] = $metaTitle;
        
        return $dataForBulkUpload;
    }
    
    /**
     * Set product data for bulk product upload
     *
     * @param array $product            
     * @param number $specialPrice            
     * @param string $specialFromDate            
     * @param string $specialToDate            
     * @param string $type            
     * @param number $weight            
     * @param number $attributeSetId            
     * @param array $categoryIds            
     * @return array $product
     *        
     */
    public function setProductDataForBulkProductUpload($product, $specialPrice, $specialDate, $type, $weight, $attributeSetId, $categoryIds) {
        if (! empty ( $specialPrice )) {
            $product->setSpecialPrice ( $specialPrice );
        }
        if (! empty ( $specialDate ['special_from_date'] )) {
            $specialFromDate = $specialDate ['special_from_date'];
            $product->setSpecialFromDate ( $specialFromDate );
        }
        if (! empty ( $specialDate ['special_to_date'] )) {
            $specialToDate = $specialDate ['special_to_date'];
            $product->setSpecialToDate ( $specialToDate );
        }
        $product->setTypeId ( $type );
        if ($type == 'simple') {
            $product->setWeight ( $weight );
        }
        if (empty ( $attributeSetId )) {
            $attributeSetId = Mage::getModel ( 'catalog/product' )->getDefaultAttributeSetId ();
        }
        $product->setAttributeSetId ( $attributeSetId );
        
        if (count ( $categoryIds ) >= 1) {
            $product->setCategoryIds ( $categoryIds );
        }
        if (Mage::helper ( 'marketplace' )->getProductApproval () == 1) {
            $product->setStatus ( 1 );
        } else {
            $product->setStatus ( 2 );
        }
        /**
         * end if
         */
        return $product;
    }
    
    /**
     * Set product info for buk product upload
     *
     * @param array $product            
     * @param number $taxClassId            
     * @param number $visibility            
     * @param string $metaDescription            
     * @param string $metaKeyword            
     * @param string $metaTitle            
     * @param number $isInStock            
     * @param number $qty            
     * @param array $websiteIds            
     * @param string $defaultCountry            
     * @param array $countries            
     * @param string $type            
     * @param string $sellerShippingOption            
     * @param number $nationalShippingPrice            
     * @param number $internationalShippingPrice            
     * @return array $product
     *        
     */
    public function setProductInfoForBulkProductUpload($dataForBulkUpload, $productData, $key, $product, $websiteIds, $countries) {
        /**
         * Get Product details
         * @var array
         */
    $type = $dataForBulkUpload ['type'];
        $taxClassId = $dataForBulkUpload ['tax_class_id'];
        $visibility = $dataForBulkUpload ['visibility'];
        $metaDescription = $dataForBulkUpload ['meta_description'];
        $metaKeyword = $dataForBulkUpload ['meta_keyword'];
        $metaTitle = $dataForBulkUpload ['meta_title'];
        $sellerShippingOption = $productData ['seller_shipping_option'] [$key];
        $nationalShippingPrice = $productData ['national_shipping_price'] [$key];
        $internationalShippingPrice = $productData ['international_shipping_price'] [$key];
        $defaultCountry = $productData ['default_country'] [$key];
        $qty = $productData ['qty'] [$key];
        $isInStock = $productData ['is_in_stock'] [$key];
        $product->setTaxClassId ( $taxClassId );
        $product->setVisibility ( $visibility );
        $product->setMetaDescription ( $metaDescription );
        $product->setMetaKeyword ( $metaKeyword );
        $product->setMetaTitle ( $metaTitle );
        $product->setStockData ( array (
                'is_in_stock' => $isInStock,
                'qty' => $qty 
        ) );
        $product->setStoreId ( 0 );
        $product->setWebsiteIds ( $websiteIds );
        $product = $this->getDefaultCountryValue ( $product, $defaultCountry, $countries );
        /**
         * Check product type is simple
         */
        if ($type == 'simple') {
            $attrForShipping = $product->getResource ()->getAttribute ( "seller_shipping_option" );
            if ($sellerShippingOption == 'Shipping Cost') {
                if ($attrForShipping->usesSource ()) {
                    $sellerShippingOptionValue = $attrForShipping->getSource ()->getOptionId ( "Shipping Cost" );
                }
            } else {
                if ($attrForShipping->usesSource ()) {
                    $sellerShippingOptionValue = $attrForShipping->getSource ()->getOptionId ( "Free" );
                }
            }
            if (! empty ( $sellerShippingOptionValue )) {
                $product->setSellerShippingOption ( $sellerShippingOptionValue );
            }
            
            if (! empty ( $sellerShippingOptionValue )) {
                $product->setNationalShippingPrice ( $nationalShippingPrice );
                $product->setInternationalShippingPrice ( $internationalShippingPrice );
            }
        }
        
        $sellerId = $groupId = '';
        /**
         * Check whether customer logged in  or not
         */
        if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
        /**
         * Get Seller id
         * @var int
         */
            $sellerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
            $product->setSellerId ( $sellerId );
        }
        /**
         * Getting group id
         */
        $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
        $product->setGroupId ( $groupId );
        /**
         * Set is assign product
         */
        $product->setIsAssignProduct ( 0 );
        return $product;
    }
    
    /**
     * Get default country value
     *
     * @param array $product            
     * @param string $defaultCountry            
     * @param array $countries            
     * @return array $product
     */
    public function getDefaultCountryValue($product, $defaultCountry, $countries) {
        if (! empty ( $defaultCountry ) && isset ( $countries [$defaultCountry] )) {
            $defaultCountryValue = $countries [$defaultCountry];
        }
        
        if (! empty ( $defaultCountryValue )) {
            $product->setDefaultCountry ( $defaultCountryValue );
        }
        /**
         * end if
         */
        return $product;
    }
    
    /**
     * Set images and custom options for bulk product upload
     *
     * @param array $product            
     * @param array $images            
     * @param
     *            $customOptions
     * @return array $product
     */
    public function setImagesAndCustomOptionForBulkProductUpload($product, $images, $imagePath, $customOptions) {
        /**
         * Getting image file recursively
         */
        $images = array_unique ( $images );
        if (! empty ( $imagePath )) {
            $DirectoryIterator = new RecursiveDirectoryIterator ( $imagePath );
            $IteratorIterator = new RecursiveIteratorIterator ( $DirectoryIterator, RecursiveIteratorIterator::SELF_FIRST );
            foreach ( $IteratorIterator as $file ) {
            /**
             * Increment foreach loop
             */
                if ($file->isFile () && in_array ( $file->getFilename (), $images )) {
                    $product->addImageToMediaGallery ( $file->getRealPath (), array (
                            'image',
                            'small_image',
                            'thumbnail' 
                    ), false, false );
                }
            }
        }
        
        /**
         * Initialize product options
         */
        if (! empty ( $customOptions ['options'] )) {
            $product->setProductOptions ( $customOptions ['options'] );
            $product->setCanSaveCustomOptions ( 1 );
        }
        
        return $product;
    }
    
    /**
     * Get configurable product data for bulk upload
     *
     * @param number $key            
     * @param string $type            
     * @param array $productData            
     */
    public function getConfigurableProductDataForBulkUpload($key, $type, $productData) {
        $configurableProductsDataBulk = array ();
        $attributeIds = array ();
        $configurableProductsData = $superProductsSkus = array ();
        $nextKey = $key;
        /**
         * Increment while loop
         */
        while ( isset ( $productData ['sku'] [$nextKey] ) && $type == 'configurable' ) {
            if (empty ( $productData ['sku'] [$nextKey] ) && ! empty ( $productData ['_super_products_sku'] [$nextKey] ) || $nextKey == $key && ! empty ( $productData ['_super_products_sku'] [$nextKey] )) {
                $superProductsSkus [] = trim ( $productData ['_super_products_sku'] [$nextKey] );
                $attributeCode = $productData ['_super_attribute_code'] [$nextKey];
                $attributeModel = Mage::getModel ( 'eav/entity_attribute' );
                $attributeId = $attributeModel->getIdByCode ( 'catalog_product', $attributeCode );
                /**
                 * Check whether attribute is empty
                 */
                if (! empty ( $attributeCode ) && $attributeId) {
                    $attributeDetails = Mage::getSingleton ( "eav/config" )->getAttribute ( 'catalog_product', $attributeCode );
                    $attribute = $attributeDetails->getData ();
                    
                    $attributeId = $attribute ['attribute_id'];
                    $attributeIds [] = $attributeId;
                    $label = $attribute ['frontend_label'];
                    $priceAndPercent = $this->getPriceAndPercentValue ( $productData, $nextKey );
                    $isPercent = $priceAndPercent ['is_percent'];
                    $pricingValue = $priceAndPercent ['pricing_value'];
                    $superAttributeOption = $productData ['_super_attribute_option'] [$nextKey];
                    $configurableProductsData = Mage::helper ( 'marketplace/market' )->prepareConfigurableProductData ( $configurableProductsData, $attributeCode, $superAttributeOption, $attributeId, $label, $isPercent, $pricingValue );
               }
            } else {
                break;
            }
            $nextKey = $nextKey + 1;
        }
        $configurableProductsDataBulk ['attribute_ids'] = $attributeIds;
        $configurableProductsDataBulk ['configurable_products_data'] = $configurableProductsData;
        $configurableProductsDataBulk ['super_products_skus'] = $superProductsSkus;
        return $configurableProductsDataBulk;
    }
    /**
     *
     *
     * Get price and percent value
     *
     * @param array $productData            
     * @param number $nextKey            
     * @return array $priceAndPercent
     */
    public function getPriceAndPercentValue($productData, $nextKey) {
        $priceAndPercent = array ();
        $isPercent = 0;
        $pricingValue = '';
        
        if (isset ( $productData ['_super_attribute_price_corr'] [$nextKey] )) {
            if (strpos ( $productData ['_super_attribute_price_corr'] [$nextKey], '%' ) !== false) {
                $isPercent = 1;
                $pricingValue = str_replace ( "%", "", $productData ['_super_attribute_price_corr'] [$nextKey] );
            } else {
                $isPercent = 0;
                $pricingValue = $productData ['_super_attribute_price_corr'] [$nextKey];
            }
        }
        /**
         * end if clause
         */
        $priceAndPercent ['is_percent'] = $isPercent;
        $priceAndPercent ['pricing_value'] = $pricingValue;
        return $priceAndPercent;
    }
    
    /**
     * Set configurable product data for bulk upload
     *
     * @param array $product            
     * @param array $attributeIds            
     * @param string $type            
     * @param number $attributeSetId            
     * @param array $configurableProductData            
     * @return array $product
     */
    public function setConfigurableProductDataForBulkUpload($product, $attributeIds, $type, $attributeSetId, $configurableProductsData) {
        if (count ( $attributeIds ) >= 1 && $type == 'configurable') {
            $attributeIds = array_unique ( $attributeIds );
            $attributeSetCollection = Mage::getModel ( 'catalog/product_attribute_api' )->items ( $attributeSetId );
            $allAttributeIdsForAttributeSetId = array ();
            /**
             * Increment foreach loop
             */
            foreach ( $attributeSetCollection as $attributeSetAttributeId ) {
                if (isset ( $attributeSetAttributeId ['attribute_id'] )) {
                    $allAttributeIdsForAttributeSetId [] = $attributeSetAttributeId ['attribute_id'];
                }
            }
            $attributeIdsForConfig = array_intersect ( $allAttributeIdsForAttributeSetId, $attributeIds );    
            /**
             * Check whether attribute is not empty
             */
            if (count ( $attributeIdsForConfig ) >= 1) {
                $product->getTypeInstance ()->setUsedProductAttributeIds ( $attributeIds );
                $configurableAttributesData = $product->getTypeInstance ()->getConfigurableAttributesAsArray ();
                $configurableAttributesData = Mage::helper ( 'marketplace/market' )->prepareToSetConfigAttributeData ( $configurableAttributesData, $configurableProductsData );
                $product->setCanSaveConfigurableAttributes ( true );
                $product->setConfigurableAttributesData ( $configurableAttributesData );
            }
        }
        return $product;
    }
    
    /**
     * Set product config data
     *
     * @param array $product            
     * @param array $configurableAttributes            
     */
    public function setProductConfigData ( $productData,$product, $configurableAttributes, $key ) {
        if (count ( $configurableAttributes ) >= 1) {
            $productConfigData = array ();
            /**
             * Adding data to product instanse
             */
            foreach ( $configurableAttributes as $configurableAttribute ) {
                if (! empty ( $productData [$configurableAttribute] [$key] )) {
                    $productAttributeOption = Mage::getModel ( 'catalog/product' );
                    $attr = $productAttributeOption->getResource ()->getAttribute ( $configurableAttribute );
                    $optionValueFoConfig = Mage::helper ( 'marketplace/market' )->getOptionValueForConfig ( $attr, $productData, $configurableAttribute, $key );
                    $productConfigData [$configurableAttribute] = $optionValueFoConfig;
                }
            }
            if (! empty ( $productConfigData )) {
                $product->addData ( $productConfigData );
            }
        }
        return $product;
    }
    
    /**
     * Save product for configurable product bulk upload
     */
    public function saveSimpleProductForBulkUpload($type, $superProductsSkus, $product) {
    /**
     * Check whether product type is configurable and count greater than zero
     */
        if ($type == 'configurable' && count ( $superProductsSkus ) >= 1) {
            $superProductsSkus = array_unique ( $superProductsSkus );
            /**
             * Load Product Collection
             *Filter by sku
             */
            $productCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'sku', array (
                    'in' => $superProductsSkus 
            ) );
            
            $simpleProductIds = array_unique ( $productCollection->getColumnValues ( 'entity_id' ) );
            
            if (count ( $simpleProductIds ) >= 1) {
                Mage::getResourceSingleton ( 'catalog/product_type_configurable' )->saveProducts ( $product, array_unique ( $simpleProductIds ) );
            }
            
            if ($type == 'configurable') {
                $stockItem = Mage::getModel ( 'cataloginventory/stock_item' );
                $stockItem->assignProduct ( $product );
                $stockItem->setData ( 'stock_id', 1 );
                $stockItem->setData ( 'qty', 0 );
                $stockItem->setData ( 'use_config_min_qty', 1 );
                $stockItem->setData ( 'use_config_backorders', 1 );
                $stockItem->setData ( 'min_sale_qty', 1 );
                $stockItem->setData ( 'use_config_min_sale_qty', 1 );
                $stockItem->setData ( 'use_config_max_sale_qty', 1 );
                $stockItem->setData ( 'is_in_stock', 1 );
                $stockItem->setData ( 'use_config_notify_stock_qty', 1 );
                $stockItem->setData ( 'manage_stock', 1 );
                $stockItem->save ();
                $stockStatus = Mage::getModel ( 'cataloginventory/stock_status' );
                $stockStatus->assignProduct ( $product );
                $stockStatus->saveProductStatus ( $product->getId (), 1 );
            }
        
        }
    }
    /**
     * Set email for admin while adding bulk product upload
     */
    public function sendEamilToAdminForBulkProductUpload() {
        if (Mage::getStoreConfig ( 'marketplace/product/bulkproductemailnotification' ) == 1) {
            $sellerId = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getId ();
            /**
             * Sending email for added new product
             */
            $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
            $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
            $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
            
            if (Mage::helper ( 'marketplace' )->getProductApproval () == 1) {
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/bulkproductemailnotificationtemplate' );
                
                /**
                 * Selecting template id
                 */
                if ($templateId) {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_product_bulkproductemailnotificationtemplate' );
                }
            } else {
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/bulkproductapprovalemailnotificationtemplate' );
                
                /**
                 * Selecting template id
                 */
                if ($templateId) {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->load ( $templateId );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_product_bulkproductapprovalemailnotificationtemplate' );
                }
            }
            $customer = Mage::getModel ( 'customer/customer' )->load ( $sellerId );
            $selleremail = $customer->getEmail ();
            $recipient = $toMailId;
            $sellername = $customer->getName ();
            $emailTemplate->setSenderName ( $sellername );
            $emailTemplate->setSenderEmail ( $selleremail );
            $emailTemplateVariables = (array (
                    'ownername' => $toName,
                    'sellername' => $sellername,
                    'selleremail' => $selleremail 
            ));
            $emailTemplate->setDesignConfig ( array (
                    'area' => 'frontend' 
            ) );
            $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
            $emailTemplate->send ( $recipient, $toName, $emailTemplateVariables );
        }
    }
    /**
     * Checking required field for bulk upload
     *
     * @param array $productData            
     * @param number $key            
     * @return boolean $flag
     */
    public function checkRequiredFieldForBulkUpload($productData, $key) {
        $flag = 0;
        $checkRequiredField = Mage::helper ( 'marketplace/market' )->checkRequiredField ( $productData, $key );
        if ($checkRequiredField == 1 && isset ( $productData ['price'] [$key] ) && isset ( $productData ['qty'] [$key] ) && isset ( $productData ['_type'] [$key] )) {
            $flag = 1;
        }
        /**
         * end if
         */
        return $flag;
    }
    /**
     * Get check product type for bulk upload
     *
     * @param array $productData            
     * @param number $key            
     * @return boolean $orFlag
     */
    public function checkProductTypeForBulkUpload($productData, $key) {
        $orFlag = 0;
        if ($productData ['_type'] [$key] == 'simple' || $productData ['_type'] [$key] == 'virtual' || $productData ['_type'] [$key] == 'configurable') {
            $orFlag = 1;
        }
        /**
         * end if
         */
        return $orFlag;
    }
    
    /**
     * Display message for bulk upload
     *
     * @param number $importProductsCount            
     * @return void
     */
    public function displayMessageForBulkUpload($importProductsCount) {
        /**
         * Success message redirect to manage product page
         */
        if (Mage::helper ( 'marketplace' )->getProductApproval () == 1) {
        /**
         * Check whether product count is greater than one
         */
            if ($importProductsCount > 1) {
                Mage::getSingleton ( 'core/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Your' ) . " $importProductsCount " . Mage::helper ( 'marketplace' )->__ ( 'products are uploaded successfully' ) );
            } else {
                Mage::getSingleton ( 'core/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Your' ) . " $importProductsCount " . Mage::helper ( 'marketplace' )->__ ( 'products is uploaded successfully' ) );
            }
        } else {
            if ($importProductsCount > 1) {
                Mage::getSingleton ( 'core/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Your' ) . " $importProductsCount " . Mage::helper ( 'marketplace' )->__ ( 'products are awaiting moderation' ) );
            } else {
                Mage::getSingleton ( 'core/session' )->addSuccess ( Mage::helper ( 'marketplace' )->__ ( 'Your' ) . " $importProductsCount " . Mage::helper ( 'marketplace' )->__ ( 'product are awaiting moderation' ) );
            }
        }
        /**
         * end if
         */
    }
} 