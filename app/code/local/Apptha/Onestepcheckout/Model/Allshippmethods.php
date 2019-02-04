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
class Apptha_Onestepcheckout_Model_Allshippmethods extends Mage_Adminhtml_Model_System_Config_Source_Shipping_Allmethods {
    /**
     * Class constructor function
     */
    public function _construct() {
        /**
         * Loading parent constructor
         */
        parent::_construct ();
        $this->_init ( 'onestepcheckout/allshippmethods' );
    }
    public function toOptionArray($isActiveOnlyFlag = false) {
        /**
         * initialising active only flag as empty
         */
        $isActiveOnlyFlag = false;
        $methods = array (
                array (
                        'value' => '',
                        'label' => '' 
                ) 
        );
        /**
         * get all carrires
         */
        $carriers = Mage::getSingleton ( 'shipping/config' )->getAllCarriers ();
        
        foreach ( $carriers as $carrierCode => $carrierModel ) {
            /**
             * Check condition
             * carrier code is not equal to 'dhlint'
             */
            if ($carrierCode != 'dhlint') {
                /**
                 * Check condition
                 * carrier model is active
                 * and active only flag is true
                 */
                if (! $carrierModel->isActive () && ( bool ) $isActiveOnlyFlag === true) {
                    continue;
                }
                $carrierMethods = $carrierModel->getAllowedMethods ();
                /**
                 * Check Carrier methods has not been set already
                 */
                if (! $carrierMethods) {
                    continue;
                }
                /**
                 * get carrier title
                 */
                $carrierTitle = Mage::getStoreConfig ( 'carriers/' . $carrierCode . '/title' );
                $methods [$carrierCode] = array (
                        'label' => $carrierTitle,
                        'value' => array () 
                );
                /**
                 * loop condtion
                 */
                foreach ( $carrierMethods as $methodCode => $methodTitle ) {
                    if ($methodTitle != '') {
                        $methods [$carrierCode] ['value'] [] = array (
                                'value' => $carrierCode . '_' . $methodCode,
                                'label' => '[' . $carrierCode . '] ' . $methodTitle 
                        );
                    }
                }
            }
        }
        /**
         * return array
         */
        return $methods;
    }
}