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
 * This file is used to display product price
 */
class Apptha_Superdeals_Block_Price extends Mage_Catalog_Block_Product_View {
    protected function _prepareLayout() {
        $superdealsPrice = 'superdeals/price.phtml';
        /**
         * Display the price for particular product type
         */
        $simpleBlock = $this->getLayout ()->getBlock ( 'product.info.simple' );
        $virtualBlock = $this->getLayout ()->getBlock ( 'product.info.virtual' );
        $groupedBlock = $this->getLayout ()->getBlock ( 'product.info.grouped' );
        $configurableBlock = $this->getLayout ()->getBlock ( 'product.info.configurable' );
        if ($simpleBlock) {
            $simpleBlock->setTemplate ( $superdealsPrice );
        }
        if ($virtualBlock) {
            $virtualBlock->setTemplate ( $superdealsPrice );
        }
        if ($configurableBlock) {
            $configurableBlock->setTemplate ( $superdealsPrice );
        }
        if ($groupedBlock) {
            $groupedBlock->setTemplate ( $superdealsPrice );
        }
    }
}