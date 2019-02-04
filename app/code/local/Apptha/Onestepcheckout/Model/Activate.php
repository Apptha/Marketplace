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
class Apptha_Onestepcheckout_Model_Activate extends Mage_Core_Model_Abstract {
    public function _construct() {
        parent::_construct ();
        $this->_init ( 'onestepcheckout/activate' );
    }
    /**
     * Check whether checkout display in cart page or separate
     *
     * @return multitype:multitype:NULL unknown
     */
    public function toOptionArray() {
        $activatePage = array (
                'Cart Page',
                'Seperate Page' 
        );
        $activateValue = array (
                '1',
                '2' 
        );
        $arrayCombine = array_combine ( $activatePage, $activateValue );
        $temp = array ();
        
        foreach ( $arrayCombine as $activatePageKey => $activatePageValue ) {
            $temp [] = array (
                    'label' => $activatePageKey,
                    'value' => strtolower ( $activatePageValue ) 
            );
        }
        return $temp;
    }
}