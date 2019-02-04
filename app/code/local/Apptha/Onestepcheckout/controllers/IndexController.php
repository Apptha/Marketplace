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
 * This will be used for managing one step checkout functions
 * and views in front end
 */
class Apptha_Onestepcheckout_IndexController extends Mage_Core_Controller_Front_Action {
    /**
     * load the onepage template
     * and check the quotes
     * if not available redirect to cart page
     */
    public function indexAction() {
        $quote = $this->getOnepage ()->getQuote ();
        /**
         * Check condition
         * if quote variable have value
         * or have error
         * if condition is satisfied means return this function
         */
        if (! $quote->hasItems () || $quote->getHasError ()) {
            $this->_redirect ( 'checkout/cart' );
            return;
        }
        
        /**
         * Check condition
         * if not have any value in quote validity minimum amount
         */
        if (! $quote->validateMinimumAmount ()) {
            /**
             * get error value from cofig
             */
            $error = Mage::getStoreConfig ( 'sales/minimum_order/error_message' );
            Mage::getSingleton ( 'checkout/session' )->addError ( $error );
            $this->_redirect ( 'checkout/cart' );
            return;
        }
        Mage::getSingleton ( 'checkout/session' )->setCartWasUpdated ( false );
        Mage::getSingleton ( 'customer/session' )->setBeforeAuthUrl ( Mage::getUrl ( '*/*/*', array (
                '_secure' => true 
        ) ) );
        /**
         * save billing and shipping information onload
         */
        $helper = Mage::helper ( 'onestepcheckout/checkout' );
        $this->shippingreloadAction ();
        $billing_data = $this->getRequest ()->getPost ( 'billing', array () );
        $customerAddressId = $this->getRequest ()->getPost ( 'billing_address_id', false );
        /**
         * check condition if onepage is visible
         */
        if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
            $Billingdata = $helper->load_add_data ( $billing_data );
            $this->getOnepage ()->saveBilling ( $Billingdata, $customerAddressId );
        } else {
            /**
             * check condition if billing data is not empty
             */
            if (! empty ( $billing_data ['use_for_shipping'] )) {
                $Shippingdata = $helper->load_add_data ( $billing_data );
                $shipping_result1 = $this->getOnepage ()->saveBilling ( $Shippingdata, $customerAddressId );
                $shipping_result = $this->getOnepage ()->saveShipping ( $Shippingdata, $customerAddressId );
            } else {
                /**
                 * get shipping address id value from post data
                 */
                $shippingAddressId = $this->getRequest ()->getPost ( 'shipping_address_id', false );
                $shipping_data = $this->getRequest ()->getPost ( 'shipping', array () );
                $Shippingdata = $helper->load_add_data ( $shipping_data );
                $shipping_result1 = $this->getOnepage ()->saveBilling ( $Shippingdata, $customerAddressId );
                $shipping_result = $this->getOnepage ()->saveShipping ( $Shippingdata, $shippingAddressId );
            }
        }
        $this->_checkCountry ();
        $this->getOnepage ()->initCheckout ();
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->renderLayout ();
    /**
     * End of save billing and shipping information onload
     */
    }
    
    /**
     * End index action method
     */
    
    /**
     * check the billing and shipping country
     *
     * @return void
     */
    private function _checkCountry() {
        $quote = $this->getOnepage ()->getQuote ();
        $shipping = $quote->getShippingAddress ();
        $billing = $quote->getBillingAddress ();
        $helper = Mage::helper ( 'onestepcheckout/checkout' );
        /**
         * Get the geoip,default country if from the admin configuration.
         */
        $enableGeoIp = Mage::getStoreConfig ( 'onestepcheckout/general/enable_geoip' );
        $countryId = Mage::getStoreConfig ( 'onestepcheckout/general/default_country_id' );
        /**
         * Check condition
         * enable config value is equal 1
         */
        if (($enableGeoIp == 1) && ($helper->getGeoIp ()->countryCode)) {
            $countryId = $helper->getGeoIp ()->countryCode;
        }
        
        /**
         * Check condition
         * if country id is null means get default country
         * and assign this variable
         */
        if (is_null ( $countryId )) {
            $countryId = Mage::helper ( 'core' )->getDefaultCountry ();
        }
        /**
         * save shipping rate value into database
         */
        $shipping->setCountryId ( $countryId )->setCollectShippingRates ( true )->save ();
        $billing->setCountryId ( $countryId )->save ();
        $shipping->setSameAsBilling ( true )->save ();
    }
    /**
     * End check country method
     */
    
    /**
     * Reload shipping info according
     * state
     * zip
     * and country selection
     *
     * @return void
     */
    public function shippingreloadAction() {
        /**
         * check condition if date is expired
         */
        if ($this->_expireAjax ()) {
            return;
        }
        $shipping_methods = $this->getRequest ()->getPost ( 'shipping_method' );
        
        /**
         * Check condition
         * if shipping method is not have any value means load config value
         */
        if (! $shipping_methods) {
            $shipping_methods = Mage::getStoreConfig ( 'onestepcheckout/general/default_shipping_method' );
        }
        $save_shippingmethod = $this->getOnepage ()->saveShippingMethod ( $shipping_methods );
        /**
         * check condition
         * if shipping method is saved or not
         */
        if (! $save_shippingmethod) {
            Mage::dispatchEvent ( 'checkout_controller_onepage_save_shipping_method', array (
                    'request' => $this->getRequest (),
                    'quote' => $this->getOnepage ()->getQuote () 
            ) );
            $this->getOnepage ()->getQuote ()->collectTotals ();
        }
        $this->getOnepage ()->getQuote ()->collectTotals ()->save ();
        $this->getOnepage ()->getQuote ()->getShippingAddress ()->setShippingMethod ( $shipping_methods );
    }
    
    /**
     * End of index Action
     */
    
    /**
     * if ajax expires check the quouetes
     * if not avaiable redirect to ajaxredirectresponse fn
     */
    protected function _expireAjax() {
        /**
         * get activate cart value from config
         */
        $activateInCart = Mage::getStoreConfig ( 'onestepcheckout/general/Activate_apptha_onestepcheckout_cart' );
        /**
         * check condition activate cart is not equal to 1
         */
        if ($activateInCart != 1) :
            /**
             * check condition quote item is equal to true
             */
            if (! $this->getOnepage ()->getQuote ()->hasItems () || $this->getOnepage ()->getQuote ()->getHasError () || $this->getOnepage ()->getQuote ()->getIsMultiShipping ()) {
                $this->_ajaxRedirectResponse ();
                return true;
            }
            $action = $this->getRequest ()->getActionName ();
            /**
             * check condition cart updated is equal to true
             */
            if (Mage::getSingleton ( 'checkout/session' )->getCartWasUpdated ( true ) && ! in_array ( $action, array (
                    'index',
                    'progress' 
            ) )) {
                $this->_ajaxRedirectResponse ();
                return true;
            }
        endif;
    }
    
    /**
     * End of expireAjax fn
     */
    
    /**
     * set session expires
     * and send the response to Onestepcheckout.js
     */
    public function _ajaxRedirectResponse() {
        $this->getResponse ()->setHeader ( 'HTTP/1.1', '403 Session Expired' )->setHeader ( 'Login-Required', 'true' )->sendResponse ();
        return $this;
    }
    
    /**
     * End of ajaxRedirectResponse fn
     */
    
    /**
     * function:includes the core checkout onepage model
     *
     * @return string
     */
    public function getOnepage() {
        return Mage::getSingleton ( 'checkout/type_onepage' );
    }
    
    /**
     * End of getOnepage fn
     */
    
    /**
     * get the username and password from ajax
     * and check the user table
     * and send the result as json response to js
     */
    public function loginAction() {
        $username = $this->getRequest ()->getPost ( 'onestepcheckout_username', false );
        $password = $this->getRequest ()->getPost ( 'onestepcheckout_password', false );
        $session = Mage::getSingleton ( 'customer/session' );
        
        $result = array (
                'success' => false 
        );
        
        /**
         * check condition username password is not empty
         */
        if ($username && $password) {
            try {
                $session->login ( $username, $password );
            } catch ( Exception $e ) {
                $result ['error'] = $e->getMessage ();
            }
            
            /**
             * check condition error message is not empty
             */
            if (! isset ( $result ['error'] )) {
                $result ['success'] = true;
            }
        } else {
            $result ['error'] = $this->__ ( 'Please enter your Email Id and password.' );
        }
        
        $this->getResponse ()->setBody ( Zend_Json::encode ( $result ) );
    }
    
    /**
     * End of Login Action
     */
    public function forgotPasswordAction() {
        $email = $this->getRequest ()->getPost ( 'email', false );
        
        /**
         * check condition email is not empty
         */
        if (! Zend_Validate::is ( $email, 'EmailAddress' )) {
            $result = array (
                    'success' => false 
            );
        } else {
            
            $customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
            
            /**
             * check condition if customer id is not empty
             */
            if ($customer->getId ()) {
                try {
                    /**
                     * Generate a new password.
                     */
                    $newPassword = $customer->generatePassword ();
                    $customer->changePassword ( $newPassword, false );
                    /**
                     * Send the password reminder emial.
                     */
                    $customer->sendPasswordReminderEmail ();
                    $result = array (
                            'success' => true 
                    );
                } catch ( Exception $e ) {
                    /**
                     * If error occurred when generate a new password, throw an error message.
                     */
                    $result = array (
                            'success' => false,
                            'error' => $e->getMessage () 
                    );
                }
            } else {
                /**
                 * If customerid is not found, return an error message.
                 */
                $result = array (
                        'success' => false,
                        'error' => 'notfound' 
                );
            }
        }
        
        $this->getResponse ()->setBody ( Zend_Json::encode ( $result ) );
    }
    
    /**
     * function:load the product information when payment method selects
     */
    public function playAction() {
        /**
         * check condition ajax expire is equal to true
         */
        if ($this->_expireAjax ()) {
            return;
        }
        $this->loadLayout ();
        $this->renderLayout ();
    }
    
    /**
     * End of Play Action
     */
    
    /**
     * function:load the product information when shipping method selects
     */
    public function reloadAction() {
        /**
         * Seller Shipping for Marketplace
         */
        if ($this->_expireAjax ()) {
            return;
        }
        /**
         * get shipping method value from post data
         */
        $shipping_method = $this->getRequest ()->getPost ( 'shipping_method' );
        /**
         * check condition shipping method value is not empty
         */
        if (! $shipping_method) {
            $shipping_method = Mage::getStoreConfig ( 'onestepcheckout/general/default_shipping_method' );
        }
        $save_shippingmethod = $this->getOnepage ()->saveShippingMethod ( $shipping_method );
        /**
         * check condition shipping method data is not empty
         */
        if (! $save_shippingmethod) {
            Mage::dispatchEvent ( 'checkout_controller_onepage_save_shipping_method', array (
                    'request' => $this->getRequest (),
                    'quote' => $this->getOnepage ()->getQuote () 
            ) );
            $this->getOnepage ()->getQuote ()->collectTotals ();
        }
        /**
         * Save the shipping methos details.
         */
        $this->getOnepage ()->getQuote ()->collectTotals ()->save ();
        $this->getOnepage ()->getQuote ()->getShippingAddress ()->setShippingMethod ( $shipping_method );
        
        $this->loadLayout ();
        $this->renderLayout ();
    }
    
    /**
     * End of reload Action
     */
    
    /**
     * Start of paymentreload Action
     * payment reload when changes the shipping methods
     */
    public function paymentreloadAction() {
        $this->loadLayout ( false );
        $this->renderLayout ();
    }
    
    /**
     * End of paymentreload Action
     */
    public function summaryAction() {
        /**
         * check condition ajax expire is equal to true
         */
        if ($this->_expireAjax ()) {
            
            return;
        }
        
        $this->loadLayout ();
        $this->renderLayout ();
    }
    /**
     * End summary action
     */
    
    /**
     * ajax save billing function
     * save billing,shipping,payment information
     */
    public function savebillingAction() {
        /**
         * Get params value.
         */
        $billing_data = $this->getRequest ()->getPost ( 'billing', array () );
        $shipping_data = $this->getRequest ()->getPost ( 'shipping', array () );
        $customerAddressId = $this->getRequest ()->getPost ( 'billing_address_id', false );
        $shippingAddressId = $this->getRequest ()->getPost ( 'shipping_address_id', false );
        /**
         * Load billing address based on
         * country id
         * region id
         * post code
         * city
         */
        if (isset ( $billing_data ['use_for_shipping'] ) == 1) {
            $billingCountryId = $billing_data ['country_id'];
            $billingRegionId = $billing_data ['region_id'];
            $billingZipcode = $billing_data ['postcode'];
            $billingRegion = $billing_data ['region'];
            $billingCity = $billing_data ['city'];
            $this->getOnepage ()->getQuote ()->getBillingAddress ()->setCountryId ( $billingCountryId )->setRegionId ( $billingRegionId )->setPostcode ( $billingZipcode )->setRegion ( $billingRegion )->setCity ( $billingCity )->setCollectShippingRates ( true );
            $this->getOnepage ()->getQuote ()->getShippingAddress ()->collectTotals ();
            /**
             * Save billing address details.
             */
            $this->getOnepage ()->getQuote ()->save ();
        } else {
            $this->getShippingInfo ( $shipping_data );
        }
        
        /**
         * Check customer is login or not.
         *
         * Check customer address.
         */
        if ((Mage::helper ( 'customer' )->isLoggedIn ()) && (! empty ( $customerAddressId ))) {
            $billingAddress = Mage::getModel ( 'customer/address' )->load ( $customerAddressId );
            if (is_object ( $billingAddress ) && $billingAddress->getCustomerId () == Mage::helper ( 'customer' )->getCustomer ()->getId ()) {
                $billing_data = array_merge ( $billing_data, $billingAddress->getData () );
            }
        }
        /**
         * Get the tax calculation active status from admin configurartion.
         */
        $config = Mage::getStoreConfig ( 'tax/calculation/based_on' );
        $helper = Mage::helper ( 'onestepcheckout/checkout' );
        if ($config == "billing") {
            /**
             * Save billing information.
             */
            $billing_info = $helper->load_add_data ( $billing_data );
            $billing_result = $this->getOnepage ()->saveBilling ( $billing_info, $customerAddressId );
        } else {
            if (! empty ( $billing_data ['use_for_shipping'] )) {
                /**
                 * Save billing data.
                 */
                $Billingdata = $helper->load_add_data ( $billing_data );
                $shipping_result = $this->getOnepage ()->saveShipping ( $Billingdata, $customerAddressId );
            } else {
                if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
                    /**
                     * Save billing information.
                     */
                    $billing_info = $helper->load_add_data ( $billing_data );
                    $billing_result = $this->getOnepage ()->saveBilling ( $billing_info, $customerAddressId );
                } else {
                    /**
                     * Save shipping information.
                     */
                    $shipping_info = $helper->load_add_data ( $shipping_data );
                    $shipping_result = $this->getOnepage ()->saveShipping ( $shipping_info, $shippingAddressId );
                }
            }
        }
        if (! empty ( $billing_data ['use_for_shipping'] )) {
            if (! empty ( $billing_data ['country_id'] )) {
                /**
                 * Set shipping rates based on country.
                 */
                $this->getOnepage ()->getQuote ()->getShippingAddress ()->setCountryId ( $billing_data ['country_id'] )->setCollectShippingRates ( true );
            }
            if (! empty ( $billing_data ['region_id'] )) {
                /**
                 * Set shipping rates based on region id.
                 */
                $this->getOnepage ()->getQuote ()->getShippingAddress ()->setRegionId ( $billing_data ['region_id'] )->setCollectShippingRates ( true );
            }
            if (! empty ( $billing_data ['region'] )) {
                /**
                 * Set shipping rates based on region.
                 */
                $this->getOnepage ()->getQuote ()->getShippingAddress ()->setRegionId ( $billing_data ['region'] )->setCollectShippingRates ( true );
            }
            if (! empty ( $billing_data ['city'] )) {
                /**
                 * Set shipping rates based on city.
                 */
                $this->getOnepage ()->getQuote ()->getShippingAddress ()->setCity ( $billing_data ['city'] )->setCollectShippingRates ( true );
            }
            if (! empty ( $billing_data ['postcode'] )) {
                /**
                 * Set shipping rates based on postcode.
                 */
                $this->getOnepage ()->getQuote ()->getShippingAddress ()->setPostcode ( $billing_data ['postcode'] )->setCollectShippingRates ( true );
            }
        } else {
            if (isset ( $shipping_data ['country_id'] )) {
                /**
                 * Set shipping rates based on country.
                 */
                if (! empty ( $shipping_data ['country_id'] )) {
                    $this->getOnepage ()->getQuote ()->getShippingAddress ()->setCountryId ( $shipping_data ['country_id'] )->setCollectShippingRates ( true );
                } else {
                    $this->getOnepage ()->getQuote ()->getBillingAddress ()->setCountryId ( $shipping_data ['country_id'] )->setCollectShippingRates ( true );
                }
            }
            /**
             * Set shipping rates based on region id.
             */
            if (! empty ( $shipping_data ['region_id'] )) {
                $this->getOnepage ()->getQuote ()->getShippingAddress ()->setRegionId ( $shipping_data ['region_id'] )->setCollectShippingRates ( true );
            }
            /**
             * Set shipping rates based on region.
             */
            if (! empty ( $shipping_data ['region'] )) {
                $this->getOnepage ()->getQuote ()->getShippingAddress ()->setRegionId ( $shipping_data ['region'] )->setCollectShippingRates ( true );
            }
            /**
             * Set shipping rates based on city.
             */
            if (! empty ( $shipping_data ['city'] )) {
                $this->getOnepage ()->getQuote ()->getShippingAddress ()->setCity ( $shipping_data ['city'] )->setCollectShippingRates ( true );
            }
            /**
             * Set shipping rates based on postcode.
             */
            if (! empty ( $shipping_data ['postcode'] )) {
                $this->getOnepage ()->getQuote ()->getShippingAddress ()->setPostcode ( $shipping_data ['postcode'] )->setCollectShippingRates ( true );
            }
        }
        /**
         * Get the payment methos from the parameters.
         */
        $paymentMethod = $this->getRequest ()->getPost ( 'payment_method', false );
        if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
            /**
             * Set payment method into quate
             */
            $this->getOnepage ()->getQuote ()->getBillingAddress ()->setPaymentMethod ( ! empty ( $paymentMethod ) ? $paymentMethod : null );
        } else {
            $this->getOnepage ()->getQuote ()->getShippingAddress ()->setPaymentMethod ( ! empty ( $paymentMethod ) ? $paymentMethod : null );
        }
        /**
         * set load layout is false
         * 
         */
        $this->loadLayout ( false );
        $this->renderLayout ();
    }
    /**
     * Get shipping information according to
     *
     * Country id
     * region id
     * post code
     * region
     * city
     */
    public function getShippingInfo($shipping_data) {
        $shippingCountryId = $shippingRegionId = $shippingZipcode = $shippingRegion = $shippingCity = '';
        /**
         * Check country_id available
         */
        if (isset ( $shipping_data ['country_id'] )) {
            $shippingCountryId = $shipping_data ['country_id'];
        }
        /**
         * Check region_id available
         */
        if (isset ( $shipping_data ['region_id'] )) {
            $shippingRegionId = $shipping_data ['region_id'];
        }
        /**
         * Check postcode available
         */
        if (isset ( $shipping_data ['postcode'] )) {
            $shippingZipcode = $shipping_data ['postcode'];
        }
        /**
         * Check region available
         */
        if (isset ( $shipping_data ['region'] )) {
            $shippingRegion = $shipping_data ['region'];
        }
        /**
         * Check city available
         */
        if (isset ( $shipping_data ['city'] )) {
            $shippingCity = $shipping_data ['city'];
        }
        $this->getOnepage ()->getQuote ()->getShippingAddress ()->setCountryId ( $shippingCountryId )->setRegionId ( $shippingRegionId )->setPostcode ( $shippingZipcode )->setRegion ( $shippingRegion )->setCity ( $shippingCity )->setCollectShippingRates ( true );
        $this->getOnepage ()->getQuote ()->getShippingAddress ()->collectTotals ();
        $this->getOnepage ()->getQuote ()->save ();
    }
    /**
     * Get iframe block.
     */
    protected function _getReviewHtml() {
        return $this->getLayout ()->createBlock ( 'paypal/iframe' )->toHtml ();
    }
    public function saveOrderAction() {
        if ($this->_expireAjax ()) {
            return;
        }
        
        $helper = Mage::helper ( 'onestepcheckout/checkout' );
        
        if ($this->getRequest ()->isPost ()) {
            $Method = $this->getRequest ()->getPost ( 'checkout_method', false );
            $Billingdata = $this->getRequest ()->getPost ( 'billing', array () );
            $Billingdata = $helper->load_exclude_data ( $Billingdata );
            $Paymentdata = $this->getRequest ()->getPost ( 'payment', array () );
            $result = $this->getOnepage ()->saveCheckoutMethod ( $Method );
            
            if (isset ( $Billingdata ['is_subscribed'] ) && ! empty ( $Billingdata ['is_subscribed'] )) {
                $this->getOnepage ()->getCheckout ()->setCustomerIsSubscribed ( 1 );
            }
            $customerAddressId = $this->getRequest ()->getPost ( 'billing_address_id', false );
            if (isset ( $Billingdata ['email'] )) {
                $Billingdata ['email'] = trim ( $Billingdata ['email'] );
            }
            /**
             * Get the billing result.
             */
            $Billingresult = $this->getOnepage ()->saveBilling ( $Billingdata, $customerAddressId );
            
            if (! empty ( $Billingresult )) {
                /**
                 * Billing result is empty, throw the error massage.
                 */
                $result ['error'] = true;
                $result ['error_messages'] = $Billingresult ['message'];
                $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
                return;
            }
            
            $Paymentresult = $this->getOnepage ()->savePayment ( $Paymentdata );
            
            if (! empty ( $Paymentresult )) {
                /**
                 * If payment result fails, through the error message.
                 */
                $result ['error'] = true;
                $result ['error_messages'] = $Paymentresult ['message'];
                $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
                return;
            }
            
            if (isset ( $Billingdata ['email'] )) {
                
                if (! Zend_Validate::is ( $Billingdata ['email'], 'EmailAddress' )) {
                    /**
                     * If email is not valid show error message as invalid email address.
                     */
                    $result ['error'] = true;
                    $result ['error_messages'] = $this->__ ( 'Invalid Email address' );
                    $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
                    return;
                }
                
                if ($Method == 'register') {
                    /**
                     * Check if the email is already register or not.
                     */
                    $cust_exist = Mage::helper ( 'onestepcheckout/checkout' )->IscustomerEmailExists ( $Billingdata ['email'] );
                    
                    if ($cust_exist) {
                        /**
                         * If email is already registered, show massage as email already exist.
                         */
                        $result ['error'] = true;
                        $result ['error_messages'] = $this->__ ( 'Email address Already Exists' );
                        $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
                        return;
                    }
                }
            }
            /**
             * Get the shipping address.
             */
            $Shippingdata = $this->getRequest ()->getPost ( 'shipping', array () );
            $ShippingAddressId = $this->getRequest ()->getPost ( 'shipping_address_id', false );
            if (! empty ( $Billingdata ['use_for_shipping'] )) {
                /**
                 * Get the shipping result.
                 */
                $shipping_result = $this->getOnepage ()->saveShipping ( $Billingdata, $customerAddressId );
            } else if (! empty ( $ShippingAddressId )) {
                $shippingAddress = Mage::getModel ( 'customer/address' )->load ( $ShippingAddressId );
                
                if (is_object ( $shippingAddress ) && $shippingAddress->getCustomerId () == Mage::helper ( 'customer' )->getCustomer ()->getId ()) {
                    $Shippingdata = array_merge ( $Shippingdata, $shippingAddress->getData () );
                    $shipping_result = $this->getOnepage ()->saveShipping ( $Shippingdata, $ShippingAddressId );
                }
            } else if (empty ( $Billingdata ['use_for_shipping'] ) && ! $ShippingAddressId) {
                /**
                 * Save the shipping address.
                 */
                $shipping_result = $this->getOnepage ()->saveShipping ( $Shippingdata, $ShippingAddressId );
            } else {
                /**
                 * Save the billing address.
                 */
                $shipping_result = $this->getOnepage ()->saveShipping ( $Billingdata, $customerAddressId );
            }
            $ShippingMethoddata = $this->getRequest ()->getPost ( 'shipping_method', '' );
            /**
             * Save shipping method data
             */
            $this->getOnepage ()->saveShippingMethod ( $ShippingMethoddata );
        }
        /**
         * Dispatch event for the one page save shipping method.
         */
        Mage::dispatchEvent ( 'checkout_controller_onepage_save_shipping_method', array (
                'request' => $this->getRequest (),
                'quote' => $this->getOnepage ()->getQuote () 
        ) );
        $data = $this->getRequest ()->getPost ( 'payment', array () );
        /**
         * save payment details
         */
        $result = $this->getOnepage ()->savePayment ( $data );
        /**
         * Get redirect url
         */
        $redirectUrl = $this->getOnepage ()->getQuote ()->getPayment ()->getCheckoutRedirectUrl ();
        if ($redirectUrl) {
            if ($requiredAgreements = Mage::helper ( 'checkout' )->getRequiredAgreementIds ()) {
                $postedAgreementsArray = array_keys ( $this->getRequest ()->getPost ( 'agreement', array () ) );
                if ($diff = array_diff ( $requiredAgreements, $postedAgreementsArray )) {
                    /**
                     * Display error message, check that terms and condition is checked or not.
                     */
                    $result ['success'] = false;
                    /**
                     * Assign error into $result array
                     */
                    $result ['error'] = true;
                    /**
                     * Assign error message into $result array
                     */
                    $result ['error_messages'] = $this->__ ( 'Please agree to all the terms and conditions before placing the order.' );
                    /**
                     * Set json response
                     */
                    $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
                    return;
                }
            }
            $result ['success'] = true;
            /**
             * Assign error into $result array
             */
            $result ['error'] = false;
            $result ['redirect'] = $redirectUrl;
            /**
             * Set json for response body
             */
            $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
            return;
        }
        $result = array ();
        try {
            if ($requiredAgreements = Mage::helper ( 'checkout' )->getRequiredAgreementIds ()) {
                $postedAgreements = array_keys ( $this->getRequest ()->getPost ( 'agreement', array () ) );
                if ($diff = array_diff ( $requiredAgreements, $postedAgreements )) {
                    /**
                     * Display error message, check that terms and condition is checked or not.
                     */
                    $result ['success'] = false;
                    /**
                     * Assign error into $result array
                     */
                    $result ['error'] = true;
                    /**
                     * Assign error_message into $result array
                     */
                    $result ['error_messages'] = $this->__ ( 'Please agree to all the terms and conditions before placing the order.' );
                    $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
                    return;
                }
            }
            if ($data = $this->getRequest ()->getPost ( 'payment', false )) {
                /**
                 * Set import data
                 */
                $this->getOnepage ()->getQuote ()->getPayment ()->importData ( $data );
            }
            /**
             * Show the success message.
             */
            $this->getOnepage ()->saveOrder ();
            /**
             * Assign redirect url
             */
            $redirectUrl = $this->getOnepage ()->getCheckout ()->getRedirectUrl ();
            /**
             * Assign success into $result array
             */
            $result ['success'] = true;
            $result ['error'] = false;
        } catch ( Mage_Payment_Model_Info_Exception $e ) {
            $message = $e->getMessage ();
            if (! empty ( $message )) {
                $result ['error_messages'] = $message;
            }
        } catch ( Mage_Core_Exception $e ) {
            /**
             * Send payment failure message
             */
            Mage::logException ( $e );
            $result ['success'] = false;
            $result ['error'] = true;
            $result ['error_messages'] = $e->getMessage ();
            Mage::helper ( 'checkout' )->sendPaymentFailedEmail ( $this->getOnepage ()->getQuote (), $e->getMessage () );
            $result ['success'] = false;
            $result ['error'] = true;
            $result ['error_messages'] = $e->getMessage ();
        } catch ( Exception $e ) {
            /**
             * Show error message while processing order is getting error.
             */
            Mage::logException ( $e );
            /**
             * Assign success into $result array
             */
            $result ['success'] = false;
            $result ['error'] = true;
            $result ['error_messages'] = $e->getMessage ();
            Mage::helper ( 'checkout' )->sendPaymentFailedEmail ( $this->getOnepage ()->getQuote (), $e->getMessage () );
            $result ['success'] = false;
            /**
             * Assign error into $result array
             */
            $result ['error'] = true;
            /**
             * Assign error message into $result array
             */
            $result ['error_messages'] = $this->__ ( 'There was an error processing your order. Please contact us or try again later.' );
        }
        
        $this->getOnepage ()->getQuote ()->save ();
        if (isset ( $redirectUrl )) {
            /**
             * Assign redirect url into $result array
             */
            $result ['redirect'] = $redirectUrl;
        }
        if ((isset ( $Paymentdata ['method'] )) && ($Paymentdata ['method'] == 'hosted_pro' || $Paymentdata ['method'] == 'payflow_advanced')) {
            $this->loadLayout ( 'onestepcheckout_index_review' );
            /**
             * Assign data into $result array
             */
            $result ['update_section'] = array (
                    'name' => 'paypaliframe',
                    'html' => $this->_getReviewHtml () 
            );
        }
        /**
         * If order placed successfully, redirected to order success page.
         */
        if ($result ['success']) {
            /**
             * Assign success url into $result array
             */
            $result ['success'] = Mage::getBaseUrl () . 'checkout/onepage/success/';
        }
        $this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
    }
    function couponcodeAction() {
        $quote = $this->getOnepage ()->getQuote ();
        /**
         * Get params.
         */
        $couponCode = ( string ) $this->getRequest ()->getParam ( 'code' );
        
        /**
         * check condition post remove data is equal to 1
         */
        if ($this->getRequest ()->getParam ( 'remove' ) == 1) {
            $couponCode = '';
        }
        $response = array (
                'success' => false,
                'error' => false,
                'message' => false 
        );
        try {
            /**
             * Get shipping address from quote.
             */
            $quote->getShippingAddress ()->setCollectShippingRates ( true );
            $quote->setCouponCode ( strlen ( $couponCode ) ? $couponCode : '' )->collectTotals ()->save ();
            
            /**
             * check condition coupon code is not empty
             */
            if ($couponCode) {
                /**
                 * check condition coupon code equal to quote coupon code value
                 */
                if ($couponCode == $quote->getCouponCode ()) {
                    /**
                     * Coupon code is succesfuly applied, show the success message.
                     */
                    $response ['success'] = true;
                    $response ['message'] = $this->__ ( 'Coupon code "%s" was applied successfully.', Mage::helper ( 'core' )->htmlEscape ( $couponCode ) );
                } else {
                    /**
                     * Show error message, if coupon code is not valid.
                     */
                    $response ['success'] = false;
                    /**
                     * Assign error into $response array
                     */
                    $response ['error'] = true;
                    /**
                     * Assign message into $response array
                     */
                    $response ['message'] = $this->__ ( 'Coupon code "%s" is not valid.', Mage::helper ( 'core' )->htmlEscape ( $couponCode ) );
                }
            } else {
                /**
                 * If cancel the coupon code, show error message as coupon code is cancelled successfully.
                 */
                $response ['success'] = true;
                $response ['message'] = $this->__ ( 'Coupon code was canceled successfully.' );
            }
        } catch ( Mage_Core_Exception $e ) {
            /**
             * Assign success into $response array
             */
            $response ['success'] = false;
            /**
             * Assign error into $response array
             */
            $response ['error'] = true;
            /**
             * Assign message into $response array
             */
            $response ['message'] = $e->getMessage ();
        } catch ( Exception $e ) {
            /**
             * If error occured while apply coupon code, show error message as cannot apply a coupon code.
             */
            $response ['success'] = false;
            $response ['error'] = true;
            /**
             * Assign message into $response array
             */
            $response ['message'] = $this->__ ( 'Can not apply coupon code.' );
        }
        /**
         * Load the one step checkout review information.
         */
        $html = $this->getLayout ()->createBlock ( 'onestepcheckout/onestep_review_info' )->setTemplate ( 'onestepcheckout/onestep/review/info.phtml' )->toHtml ();
        /**
         * Assign summery of response to body
         */
        $response ['summary'] = $html;
        $this->getResponse ()->setBody ( Zend_Json::encode ( $response ) );
    }
    /**
     * End of couponcode Action
     */
    
    /**
     * Load reply action and render layout files
     */
    public function replayAction() {
        /**
         * check condition ajax is expire
         */
        if ($this->_expireAjax ()) {
            /**
             * @return if ajax expired
             */
            return;
        }
        $quote = $this->getOnepage ()->getQuote ();
        /**
         * check condition shipping address is not equal to empty
         */
        if ($quote->getShippingAddress ()) {
            /**
             * Set shipping rates.
             */
            $quote->getShippingAddress ()->setCollectShippingRates ( true );
            /**
             * Quate collect rates save 
             */
            $quote->collectTotals ()->save ();
        }
        /**
         * Load laytout
         * Render layout
         */
        $this->loadLayout ();
        $this->renderLayout ();
    }
}