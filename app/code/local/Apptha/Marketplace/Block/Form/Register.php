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
 * Registration page functionality
 * Registration data with address, country, newsletter subscription
 */
class Apptha_Marketplace_Block_Form_Register extends Mage_Core_Block_Template {
    protected $_address;
    
    /**
     * Used to set page title
     *
     * Return page title
     *
     * @return varchar
     */
    protected function _prepareLayout() {
        $title = Mage::helper ( 'marketplace' )->__ ( 'Create New Seller Account' );
        /**
         * Set Title
         */
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $title );
        return parent::_prepareLayout ();
    }
    
    /**
     * Function to get registration form data post url
     *
     * Return the registration data post url
     *
     * @return string
     */
    public function getPostActionUrl() {
        /**
         * Current Url
         */
        $currentUrlVal = Mage::helper ( 'core/url' )->getCurrentUrl ();
        $secureVal = strstr ( $currentUrlVal, "https" );
        $true = true;
        if ($secureVal == $true) {
            return $this->getUrl ( '*/*/createpost', array (
                    '_secure' => true 
            ) );
        } else {
            return $this->getUrl ( '*/*/createpost' );
        }
    }
    
    /**
     * Retrieve back url(product url)
     *
     * Return the product url
     *
     * @return string
     */
    public function getBackUrl() {
        /**
         * Get Current url
         */
        $currentUrlVal = Mage::helper ( 'core/url' )->getCurrentUrl ();
        /**
         * getting current Url
         */
        $secureVal = strstr ( $currentUrlVal, "https" );
        $true = true;
        if ($secureVal == $true) {
            return $this->getUrl ( 'marketplace/seller/login', array (
                    '_secure' => true 
            ) );
        } else {
            return $this->getUrl ( 'marketplace/seller/login' );
        }
    }
    
    /**
     * Retrieve form data
     *
     * Return form post data
     *
     * @return Varien_Object
     */
    public function getFormData() {
        $data = $this->getData ( 'form_data' );
        if (is_null ( $data )) {
            /**
             * Getting Form Data
             */
            $formData = Mage::getSingleton ( 'customer/session' )->getCustomerFormData ( true );
            /**
             * New Varien Object
             */
            $data = new Varien_Object ();
            if ($formData) {
                $data->addData ( $formData );
                $data->setCustomerData ( 1 );
            }
            /**
             * Set Region Id
             */
            if (isset ( $data ['region_id'] )) {
                $data ['region_id'] = ( int ) $data ['region_id'];
            }
            $this->setData ( 'form_data', $data );
        }
        return $data;
    }
    
    /**
     * Retrieve customer country identifier
     *
     * Returnt the customer country id
     *
     * @return int
     */
    public function getCountryId() {
        /**
         * Get Country Id
         */
        $countryId = $this->getFormData ()->getCountryId ();
        if ($countryId) {
            return $countryId;
        }
        return parent::getCountryId ();
    }
    
    /**
     * Retrieve customer region identifier
     *
     * Return the customer region id
     *
     * @return int
     */
    public function getRegion() {
        /**
         * Get Region
         */
        if (false !== ($region = $this->getFormData ()->getRegion ())) {
            return $region;
        }
        if (false !== ($region = $this->getFormData ()->getRegionId ())) {
            return $region;
        }
        return null;
    }
    
    /**
     * Newsletter module availability
     *
     * Return boolen value for newletter enabled or not
     *
     * @return boolean
     */
    public function isNewsletterEnabled() {
        /**
         * Is News letter Enabled
         */
        return Mage::helper ( 'core' )->isModuleOutputEnabled ( 'Mage_Newsletter' );
    }
    
    /**
     * To get the customer address
     *
     * Return customer address instance
     *
     * @return Mage_Customer_Model_Address
     */
    public function getAddress() {
        if (is_null ( $this->_address )) {
            /**
             * Get Address
             */
            $this->_address = Mage::getModel ( 'customer/address' );
        }
        
        return $this->_address;
    }
    
    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param Mage_Customer_Model_Form $form            
     * @return Mage_Customer_Block_Form_Register
     */
    public function restoreSessionData(Mage_Customer_Model_Form $form, $scope = null) {
        if ($this->getFormData ()->getCustomerData ()) {
            /**
             * Get Request
             */
            $request = $form->prepareRequest ( $this->getFormData ()->getData () );
            /**
             * Extract Data
             */
            $data = $form->extractData ( $request, $scope, false );
            /**
             * Restore Data
             */
            $form->restoreData ( $data );
        }
        return $this;
    }
}