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
class Apptha_Marketplace_Model_System_Config_Source_Themes {
/**
* Available product type 
* 
* @return multitype:multitype:string NULL
*/
    
  
   public function toOptionArray(){
  
        return array(
            array('value' => 'violet_theme', 'label'=>Mage::helper('adminhtml')->__('Violet Theme')),
            array('value' => 'red_theme', 'label'=>Mage::helper('adminhtml')->__('Red Theme')),
            array('value' => 'green_theme', 'label'=>Mage::helper('adminhtml')->__('Green Theme')),
            array('value' => 'blk_theme', 'label'=>Mage::helper('adminhtml')->__('Black Theme')),
            array('value' => 'blue_theme', 'label'=>Mage::helper('adminhtml')->__('Blue Theme')),
        );
    }
}