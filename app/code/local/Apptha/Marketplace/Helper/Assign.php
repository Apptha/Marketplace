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
class Apptha_Marketplace_Helper_Assign extends Mage_Core_Helper_Abstract {
    /**
     * Assign configurable product attribute values
     */
    public function setconfigurableProductDataForAssignProduct($configurableAttributes, $newProduct, $childProductData, $childProductId) {
        $productConfigData = array ();
        /**
         * Checking Configurable attributes greater than 1
         */
        if (count ( $configurableAttributes ) >= 1) {
            $productConfigData = array ();
            /**
             * Adding data to product instanse
             */
            /**
             * Increment Foreach loop
             */
            foreach ( $configurableAttributes as $configurableAttribute ) {
            /**
             * Check whether child product isset or not
             */
                if (! isset ( $childProductData [$childProductId] [$configurableAttribute] )) {
                /**
                 * Get Product Object
                 * @var array
                 */
                    $productAttributeOption = Mage::getModel ( 'catalog/product' );
                    $attr = $productAttributeOption->getResource ()->getAttribute ( $configurableAttribute );
                    $optionValueFoConfig = $attr->getSource ()->getOptionId ( $childProductData [$childProductId] [$configurableAttribute] );
                    $productConfigData [$configurableAttribute] = $optionValueFoConfig;
                }
            }
            if (! empty ( $productConfigData )) {
                /**
                 * add data configurable attributes
                 */
            $newProduct->addData ( $productConfigData );
            }
        }
        return $newProduct;
    }
}