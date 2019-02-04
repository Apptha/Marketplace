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
 */

/**
 * Renderer to get the seller contact phone number
 */
class Apptha_Marketplace_Block_Adminhtml_Renderersource_Contact extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * Function to get the seller contact phone number
     * 
     * Return the contact phone number
     * @return int
     */
    public function render(Varien_Object $row) {
        $id = $row->getData();
        foreach ($id as $_id) {
            $sellerProduct = Mage::helper('marketplace/marketplace')->getSellerCollection($_id);
            return $sellerProduct['contact'];
        }
    }

}

