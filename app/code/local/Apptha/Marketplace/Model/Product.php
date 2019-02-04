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
 * Function written in this file are used for product manipulation
 */
class Apptha_Marketplace_Model_Product extends Mage_Core_Model_Abstract {
    
    /**
     * Get auto product name and sku
     *
     * @param array $attributes            
     * @return string $autoProductName
     */
    public function createAutoProductNameAndSku($attributes, $productData, $autoProductName, $sellerId) {
        
        /**
         * Initilize product weight
         */
        
        if (! isset ( $productData ['weight'] )) {
            $productData ['weight'] = 0;
        }
        
        $nameAutogenerate = $skuAutogenerate = '';
        $autoFlag = 'none';
        
        if (isset ( $productData ['name_autogenerate'] )) {
            $nameAutogenerate = $productData ['name_autogenerate'];
        }
        if (isset ( $productData ['sku_autogenerate'] )) {
            $skuAutogenerate = $productData ['sku_autogenerate'];
        }
        
        $autoProductName = $this->createAutoProductName ( $nameAutogenerate, $skuAutogenerate, $attributes, $productData );
        
        if ($nameAutogenerate == 1) {
            $autoFlag = 'name';
            $productData ['name'] = $autoProductName;
        }
        
        if ($skuAutogenerate == 1) {
            $autoFlag = 'sku';
            $skuProductId = Mage::getModel ( 'catalog/product' )->getIdBySku ( trim ( $autoProductName ) );
            if (empty ( $skuProductId )) {
                $productData ['sku'] = $autoProductName;
            } else {
                $productData ['sku'] = $autoProductName . '-' . $sellerId . '-' . rand ( 0, 1000000 );
            }
        }
        
        if ($nameAutogenerate == 1 && $skuAutogenerate == 1) {
            $autoFlag = 'both';
        }
        
        $productData ['auto_flag'] = $autoFlag;
        return $productData;
    }
    
    /**
     * Get configurable product name
     *
     * @param number $configProductId            
     * @return string $utoProductName
     */
    public function getConfigProductName($configProductId) {
        $autoProductName = '';
        if (! empty ( $configProductId )) {
            $autoProductName = Mage::getModel ( 'catalog/product' )->load ( $configProductId )->getName ();
            if ($autoProductName != '') {
                $autoProductName = $autoProductName . '-';
            }
        }
        return $autoProductName;
    }
    
    /**
     *
     *
     * Get configurable product data
     *
     * @param int $sellerId            
     * @param int $configProductSellerId            
     * @param array $attributes            
     * @param array $productData            
     * @return array $configurableProductsData
     */
    public function getconfigurableProductsDataForQuickCreate($attributes, $productData, $configProduct, $sellerId) {
        $configurableProductsData = array ();
        $configProductSellerId = $configProduct->getSellerId ();
        if ($sellerId == $configProductSellerId) {
            foreach ( $attributes as $key => $attribute ) {
                $valueIndex = $pricingLabel = $pricingValue = '';
                $isPercentValue = 0;
                if (isset ( $productData [$key] )) {
                    $valueIndex = $productData [$key];
                }
                if (isset ( $productData ['option_price'] [$valueIndex] ) && $valueIndex != '') {
                    $isPercentValue = Mage::helper ( 'marketplace/product' )->getIsPercentValue ( $productData, $valueIndex );
                    $pricingValue = Mage::helper ( 'marketplace/product' )->getPricingValue ( $productData, $valueIndex );
                    $pricingLabel = Mage::helper ( 'marketplace/product' )->getPricingLabel ( $productData, $valueIndex );
                    $configurableProductsData = Mage::helper ( 'marketplace/market' )->createCongirurableProductData ( $configurableProductsData, $pricingValue, $attribute, $valueIndex, $pricingLabel, $isPercentValue );
                }
            }
        }
        return $configurableProductsData;
    }
    
    /**
     *
     *
     * Set product info
     *
     * @param array $product            
     * @param array $productData            
     * @param number $set            
     * @param string $type            
     * @param array $categoryIds            
     * @param number $sellerId            
     * @param number $groupId            
     * @param number $ImagesPath            
     * @return array $product
     */
    public function setProductInfo($product, $set, $type, $categoryIds, $ids, $imagesPath ,$_location) {
        $sellerId=$ids['seller_id'];
        $groupId=$ids['group_id'];
        /**
         * Assign create at time
         */
        $createdAt = Mage::getModel ( 'core/date' )->gmtDate ();
         /**
         * Initialize product attribute set id
         */
        if (! empty ( $set )) {
            $product->setAttributeSetId ( $set );
        } else {
            $sDefaultAttributeSetId = Mage::getSingleton ( 'eav/config' )->getEntityType ( Mage_Catalog_Model_Product::ENTITY )->getDefaultAttributeSetId ();
            $product->setAttributeSetId ( $sDefaultAttributeSetId );
        }
        
        /**
         * Initialize product type
         */
        if (isset ( $type )) {
            $product->setTypeId ( $type );
        }
        
        /**
         * Initialize product categories
         */
        if (isset ( $categoryIds )) {
            $product->setCategoryIds ( $categoryIds );
        }
        
        /**
         * Storing product data's to all store view
         */
        $product->setStoreId ( 0 );
        
        /**
         * Initialize product create at time
         */
        if (isset ( $createdAt )) {
            $product->setCreatedAt ( $createdAt );
        }
        
        $product->setSellerId ( $sellerId );
        $product->setGroupId ( $groupId );
        
        /**
         * Set is assign product
         */
        $product->setIsAssignProduct ( 0 );
        
        return $this->setImagesForProduct ( $product, $imagesPath );
    }
    
    /**
     * Set images for product
     *
     * @param array $product            
     * @param string $imagesPath            
     * @return array
     */
    public function setImagesForProduct($product, $imagesPath) {
        /**
         * Adding Product images
         */
        if (! empty ( $imagesPath )) {
            $product->setMediaGallery ( array (
                    'images' => array (),
                    'values' => array () 
            ) );
            foreach($imagesPath as $value ) {
                $product->addImageToMediaGallery ( $value, array (
                        'image',
                        'small_image',
                        'thumbnail' 
                ), false, false );
            }
        }
        
        return $product;
    }
    
    /**
     * Get product image path
     *
     * @param unknown $filesDataArray            
     * @return array $imagesPath
     */
    public function getProductImagePath($filesDataArray) {
        $imagesPath = array ();
        /**
         * Checking whether image exist or not
         */
        if (! empty ( $filesDataArray )) {
            foreach ( $filesDataArray as $key => $value ) {
                /**
                 * Initilize file name
                 */
                $filename = $key;
                
                if (substr ( $key, 0, 5 ) == 'image' && $filesDataArray [$filename] ['name'] && (file_exists ( $filesDataArray [$filename] ['tmp_name'] ))) {
                    $imagesPath [] = Mage::helper ( 'marketplace/image' )->uploadImage ( $filename, $filesDataArray );
                }
            }
        }
        return $imagesPath;
    }
    
    /**
     *
     *
     * Set configurable stock data
     *
     * @param string $type            
     * @param array $product            
     * @param array $productData            
     * @param number $isInStock            
     */
    public function setConfigurableProductStockData($type, $product, $productData, $isInStock) {
        if ($type == 'configurable') {
            
            $isInStock = 1;
            if (isset ( $productData ['stock_data'] ['is_in_stock'] )) {
                $isInStock = $productData ['stock_data'] ['is_in_stock'];
            }
            $productData ['stock_data'] = array ();
            
            $stockItem = Mage::getModel ( 'cataloginventory/stock_item' );
            $stockItem->assignProduct ( $product );
            $stockItem->setData ( 'stock_id', 1 );
            if (isset ( $productData ['stock_data'] ['qty'] )) {
                $stockItem->setData ( 'qty', $productData ['stock_data'] ['qty'] );
            } else {
                $stockItem->setData ( 'qty', 0 );
            }
            $stockItem->setData ( 'use_config_min_qty', 1 );
            $stockItem->setData ( 'use_config_backorders', 1 );
            $stockItem->setData ( 'min_sale_qty', 1 );
            $stockItem->setData ( 'use_config_min_sale_qty', 1 );
            $stockItem->setData ( 'use_config_max_sale_qty', 1 );
            $stockItem->setData ( 'is_in_stock', $isInStock );
            $stockItem->setData ( 'use_config_notify_stock_qty', 1 );
            $stockItem->setData ( 'manage_stock', 1 );
            $stockItem->save ();
            
            // This section is what was required.
            $stockStatus = Mage::getModel ( 'cataloginventory/stock_status' );
            $stockStatus->assignProduct ( $product );
            $stockStatus->saveProductStatus ( $product->getId (), 1 );
        }
    
    }
    
    /**
     *
     *
     * Set base image for product and custom option
     *
     * @param number $productId            
     * @param number $store            
     * @param number $setbase            
     * @param array $product            
     */
    public function setBaseImageForProduct($productId, $store, $setbase, $productData, $action) {
        /**
         * Load the product
         */
        $productImage = Mage::getModel ( 'catalog/product' )->load ( $productId );
        /**
         * Get all images
         */
        $mediaGallery = $productImage->getMediaGallery ();
        /**
         * If there are images
         */
        if (isset ( $mediaGallery ['images'] ) && ! empty ( $store )) {
            /**
             * Loop through the images
             */
            $increment = 0;
           foreach ( $mediaGallery ['images'] as $image ) {
                /**
                 * Set the first image as the base image
                 */
                if ($increment == $setbase) {
                    $productImage->setStoreId ( $store )->setImage ( $image ['file'] )->setSmallImage ( $image ['file'] )->setThumbnail ( $image ['file'] );
                    $productImage->save ();
                    break;
                }
                $increment ++;
            /**
             * Stop
             */
            
            }
        
        }
        if ($action == 'new') {
            $this->saveProductOptions ( $productData, $productImage );
        }
    
    }
    
    /**
     * Delete temp image files
     *
     * @param array $imagesPath            
     */
    public function deleteTempImageFiles($imagesPath) {
        /**
         * Checking whether image or not
         */
        if (! empty ( $imagesPath )) {
            foreach ( $imagesPath as $deleteImage ) {
                /**
                 * Checking whether image exist or not
                 */
                if (file_exists ( $deleteImage )) {
                    /**
                     * Delete images from temporary folder
                     */
                    unlink ( $deleteImage );
                }
            }
        }
    }
    
    /**
     * Assign configurable product data
     *
     * @param array $attributeIds            
     * @param string $type            
     * @param array $product            
     *
     */
    public function assignConfigurableProductData($attributeIds, $type, $product) {
        if ($type == 'configurable' && count ( $attributeIds ) >= 1) {
            $attributeIds = array_unique ( $attributeIds );
            $product->getTypeInstance ()->setUsedProductAttributeIds ( $attributeIds );
            $configurableAttributesData = $product->getTypeInstance ()->getConfigurableAttributesAsArray ();
            $product->setCanSaveConfigurableAttributes ( true );
            $product->setConfigurableAttributesData ( $configurableAttributesData );
            $product->setConfigurableProductsData ( array () );
        }
    }
    
    /**
     *
     *
     * Get product data array
     *
     * @param array $productData            
     * @param string $type            
     * @return array
     */
    public function getProductDataArray($productData, $type) {
        /**
         * Initilize product weight
         */
        if ($type == 'simple' && ! isset ( $productData ['weight'] )) {
            $productData ['weight'] = 0;
        }
        if ($type == 'configurable') {
            $isInStock = 1;
            if (isset ( $productData ['stock_data'] ['is_in_stock'] )) {
                $isInStock = $productData ['stock_data'] ['is_in_stock'];
            }
            $productData ['stock_data'] = array ();
        }
        return $productData;
    }
    
    /**
     * Message for new product add
     */
    public function getMessageForNewProductAdd() {
        $message = '';
        if (Mage::helper ( 'marketplace' )->getProductApproval () == 1) {
            $message = Mage::helper ( 'marketplace' )->__ ( 'Your product is added successfully' );
        } else {
            $message = Mage::helper ( 'marketplace' )->__ ( 'Your product is awaiting moderation' );
        }
        return $message;
    }
    /**
     *
     *
     * Save product optins
     *
     * @param array $productData            
     * @param array $product            
     * @return array
     */
    public function saveProductOptions($productData, $product) {
        /**
         * Delete existing product custom option
         */
        $productOption = $product;
        
        if ($productOption->getOptions ()) {
            foreach ( $productOption->getOptions () as $opt ) {
                Mage::helper ( 'marketplace/image' )->deleteCustomOption ( $opt );
            }
            $productOption->setCanSaveCustomOptions ( 1 );
            $productOption->save ();
        }
        
        /**
         * Initialize product options
         */
        if (! empty ( $productData ['options'] )) {
            $productOption->setProductOptions ( $productData ['options'] );
            $productOption->setCanSaveCustomOptions ( 1 );
            $productOption->save ();
        }
    }
    
    /**
     *
     *
     * Check whether sku exist or not
     *
     * @param array $productData            
     * @return number $value
     */
    public function checkWhetherSkuExistOrNot($productData) {
        if (isset ( $productData ['sku'] )) {
            $value = 0;
            $skuProductId = Mage::getModel ( 'catalog/product' )->getIdBySku ( trim ( $productData ['sku'] ) );
        }
        if (! empty ( $skuProductId )) {
            $value = 1;
        }
        return $value;
    }
    
    /**
     * Set product data for update product
     *
     * @param array $product            
     * @param array $categoryIds            
     * @param array $productData            
     * @param string $type            
     * @param number $isInStock            
     * @param number $store            
     * @param string $name            
     * @param string $sku            
     * @param string $description            
     * @param string $shortDescription            
     * @param number $price            
     * @param number $qty            
     * @return array
     */
    public function setProductDataForUpdate($product, $categoryIds, $productData, $type, $isInStock) {
        
        $shippingOption = $nationalShippingPrice = $internationalShippingPrice = $defaultCountry = '';
        /**
         * Updating product data
         */
        $name = $productData ['name'];
        $sku = $productData ['sku'];
        $description = $productData ['description'];
        $shortDescription = $productData ['short_description'];
        $price = $productData ['price'];   
        $storeId = $productData ['store'];
        $qty = $productData ['stock_data'] ['qty'];
        $metaTitle = $productData ['meta_title'];
        $metaKeyword = $productData ['meta_keyword'];
        $metaDescription = $productData ['meta_description'];
        $isInStock = $productData ['stock_data'] ['is_in_stock'];
        $specialPrice = $productData ['special_price'];
        $specialFromDate = $productData ['special_from_date'];
        $specialToDate = $productData ['special_to_date'];
        $stockEndDate=$productData['stock_end_date'];
        $productVideoUrl=$productData['apptha_product_video_url'];
        if(isset($productData ['default_country'])){
        $defaultCountry = $productData ['default_country'];
        }        
        if (isset ( $productData ['seller_shipping_option'] )) {
            $shippingOption = $productData ['seller_shipping_option'];
        }
        if (isset ( $productData ['national_shipping_price'] )) {
            $nationalShippingPrice = $productData ['national_shipping_price'];
        } else {
            $nationalShippingPrice = 0;
        }
        if (isset ( $productData ['international_shipping_price'] )) {
            $internationalShippingPrice = $productData ['international_shipping_price'];
        } else {
            $internationalShippingPrice = 0;
        }
        
        /**
         * Initilize product weight
         */
        if ($type == 'simple') {
            $weight = $productData ['weight'];
            $product->setWeight ( $weight );
        }
        
        $product->setStoreId ( $storeId );
        
        /**
         * Initilize product special price and date
         */
        if (isset ( $specialPrice )) {
            $product->setSpecialPrice ( $specialPrice );
        }
        
        if (! empty ( $specialFromDate )) {
            $product->setSpecialFromDate ( $specialFromDate );
        } else {
            $product->setSpecialFromDate ( '' );
        }
        if (! empty ( $specialToDate )) {
            $product->setSpecialToDate ( $specialToDate );
        } else {
            $product->setSpecialToDate ( '' );
        }
        
        if(!empty($productVideoUrl)){
        $product->setAppthaProductVideoUrl($productVideoUrl);
        }
if (! empty ( $stockEndDate )) {
$product->setStockEndDate ( $stockEndDate );
} else {
$product->setStockEndDate ( '' );
}

        $product->setName($name);
        $product->setShortDescription ( $shortDescription );
        $product->setDescription ( $description );
        $product->setPrice ( $price );
        $product->setSku ( $sku );
        $product->setDelivery($productData['delivery']);
        if(isset($productData ['status'])){
        $product->setStatus( $productData ['status'] );
        }
        $product->setSellerShippingOption ( $shippingOption );
        $product->setNationalShippingPrice ( $nationalShippingPrice );
        $product->setInternationalShippingPrice ( $internationalShippingPrice );
        $product->setDefaultCountry ( $defaultCountry );
        $product->setMetaTitle ( $metaTitle );
        $product->setMetaKeyword ( $metaKeyword );
        $product->setMetaDescription ( $metaDescription );
        if (count ( $categoryIds ) >= 1) {
            $product->setCategoryIds ( $categoryIds );
        }
        /**
         * Initilize product in stock
         */
        $stockData ['is_in_stock'] = Mage::helper ( 'marketplace/marketplace' )->productInStock ( $isInStock );
        $stockData ['qty'] = $qty;
        $product->setStockData ( $stockData );
        /**
         * Set assign product is disabled
         */
        $product->setIsAssignProduct ( 0 );
        return $product;
    }
    
    /**
     *
     *
     * Set product image for product
     *
     * @param string $baseimage            
     * @param number $productId            
     * @param number $store            
     * @param array $product            
     */
    public function setProductImagesforProduct($baseimage, $productId, $store, $product, $productData) {
        /**
         * Setting product base image
         */
        if (! empty ( $baseimage ) && ! empty ( $productId )) {
            $product = Mage::getModel ( 'catalog/product' )->load ( $productId );
            $product->setStoreId ( $store )->setImage ( $baseimage )->setSmallImage ( $baseimage )->setThumbnail ( $baseimage );
            $product->save ();
        } else {
            $setbase = 0;
            $this->setBaseImageForProduct ( $productId, $store, $setbase, $productData, 'edit' );
        }
        
        $this->saveProductOptions ( $productData, $product );
    
    }
    
    /**
     * Delete product images
     *
     * @param array $deleteimages            
     * @param number $productId            
     */
    public function deleteProductImagesForEdit($deleteimages, $productId, $baseimage) {
        /**
         * Removing product images
         */
        if (! empty ( $deleteimages ) && ! empty ( $productId )) {
            $mediaApi = Mage::getModel ( "catalog/product_attribute_media_api" );
            foreach ( $deleteimages as $image ) {
                if ($image != $baseimage) {
                    $mediaApi->remove ( $productId, $image );
                }
            }
        }
    }
    
    /**
     * Create auto product name
     *
     * @param number $nameAutogenerate            
     * @param number $skuAutogenerate            
     * @param array $attributes            
     * @param array $productData            
     * @return string $autoProductName
     */
    public function createAutoProductName($nameAutogenerate, $skuAutogenerate, $attributes, $productData) {
    $autoProductName = '';
        if ($nameAutogenerate == 1 || $skuAutogenerate == 1) {
            foreach ( $attributes as $key => $attributeCodeValueForName ) {
                $attributeDetails = Mage::getSingleton ( "eav/config" )->getAttribute ( "catalog_product", $key );
                $options = $attributeDetails->getSource ()->getAllOptions ( false );
                $autoProductName = Mage::helper ( 'marketplace/general' )->getAutoProductName ( $autoProductName, $options, $productData, $key );
            }
            
            $autoProductName = rtrim ( $autoProductName, "-" );
        }
        return $autoProductName;
    }
} 