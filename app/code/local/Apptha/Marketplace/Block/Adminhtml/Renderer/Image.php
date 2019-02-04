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
?>
<?php

/**
 * This class contains image display functionality in grid page
 */
class Apptha_Marketplace_Block_Adminhtml_Renderer_Image extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    /**
     * Get image tag
     */
    public function render(Varien_Object $row) {
        $id = $row->getId ();
        $bannerModel = Mage::getModel ( 'marketplace/sidebar' )->load ( $id );
        $filePath = $bannerModel->getFile ();
        $url = Mage::getBaseUrl ( 'media' ) . 'sliderimages/thumbs/' . $filePath;
        return "<img src=" . $url . " width='75px'/>";
    }
}