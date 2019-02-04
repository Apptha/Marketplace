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
 * This file is used to Customer login, Customer registeration, Seller login and Seller registeration functionality
 *
 * In this class, login/create account and forget password operations.
 * Also it will connects social networks such as Google, Twitter, Yahoo and Facebook oAuth connections.
 */
class Apptha_Sociallogin_IndexController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Render Apptha sociallogin pop-up layout
     */
    public function indexAction() {
        $this->loadLayout ();
        $this->renderLayout ();
    }
    
    /**
     * Customer Register Action
     *
     * @return string
     */
    public function customerAction($firstname, $lastname, $email, $provider, $data) {
        $customer = Mage::getModel ( 'customer/customer' );
        $collection = $customer->getCollection ();
        /**
         * Check the configuration for seller registration approval.
         */
        $adminApproval = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/need_approval' );
        /**
         * Get groupid
         */
        $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
        if ($customer->getSharingConfig ()->isWebsiteScope ()) {
            $collection->addAttributeToFilter ( 'website_id', Mage::app ()->getWebsite ()->getId () );
        }
        /**
         * Check the customer is logged in or not
         *
         * If customer is logged in get the customerid for the further process.
         */
        if ($this->_getCustomerSession ()->isLoggedIn ()) {
            $collection->addFieldToFilter ( 'entity_id', array (
                    'neq' => $this->_getCustomerSession ()->getCustomerId () 
            ) );
        }
        /**
         * Retrieves the customer details depends on @email
         */
        $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
        $customerIdByEmail = $customer->getId ();
        /**
         * initialize the customer email id.
         */
        if ($customerIdByEmail == '') {
            $standardInfo ['email'] = $email;
        } else {
            $standardInfo ['email'] = $email;
        }
        /**
         * Retrieving the customer form posted values.
         *
         * @param array $standardInfo
         *            array values such as@first_name,@last_name and @email
         */
        $standardInfo ['first_name'] = $firstname;
        $standardInfo ['last_name'] = $lastname;
        $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $standardInfo ['email'] );
        /**
         * Check if Already registered customer.
         */
        if ($customer->getId ()) {
            $customerGroupId = $customer->getGroupId ();
            $loggedCustomerStatus = $customer->getCustomerstatus ();
            /**
             * Check login customer status.
             */
            if ($loggedCustomerStatus == 1) {
                $this->_getCustomerSession ()->setCustomerAsLoggedIn ( $customer );
                $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Your account has been successfully connected through' . ' ' . $provider ) );
            }
            /**
             * Display success message after complete the customer registration.
             */
            if ($customerGroupId == $groupId && ($loggedCustomerStatus == 0 || $loggedCustomerStatus == 2)) {
                $this->_getCustomerSession ()->setCustomerAsLoggedIn ( $customer );
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
            }
            
            /**
             * Get customer current URL from customer session.
             */
            $link = Mage::getSingleton ( 'customer/session' )->getLink ();
            
            /**
             * Check the current url link is empty or not.
             */
            if (! empty ( $link )) {
                $requestPath = trim ( $link, '/' );
            }
            /**
             * Check if customer current URL is checkout URL.
             */
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect ( $requestPath );
                return;
            } else {
                /**
                 * Check the configuration for the after loging the customer redirected to dashboard page.
                 */
                $enableRedirectStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );
                if ($enableRedirectStatus) {
                    if ($customerGroupId == $groupId && $loggedCustomerStatus == 1) {
                        $redirect = Mage::getUrl ( 'marketplace/seller/dashboard' );
                    } else {
                        /**
                         * Redirect url link.
                         */
                        $redirect = $this->_loginPostRedirect ();
                    }
                } else {
                    $redirect = Mage::getSingleton ( 'core/session' )->getReLink ();
                }
                $this->_redirectUrl ( $redirect );
                return;
            }
        }
        /**
         * Generate Random Password .
         *
         *
         * Set Login provider if customer uses social networks such as @google, @yahoo, @facebook and @twitter.
         */
        $randomPassword = $customer->generatePassword ( 8 );
        /**
         * Set Login provider if customer uses social networks such as @google, @yahoo, @facebook and @twitter.
         */
        $customer->setId ( null )->setSkipConfirmationIfEmail ( $standardInfo ['email'] )->setFirstname ( $standardInfo ['first_name'] )->setLastname ( $standardInfo ['last_name'] )->setEmail ( $standardInfo ['email'] )->setPassword ( $randomPassword )->setConfirmation ( $randomPassword )->setLoginProvider ( $provider );
        /**
         * Checking admin approval is required for seller registration or not
         * set customer status
         * set group id
         */
        if ($adminApproval == 1 && $data == 1) {
            $customer->setGroupId ( $groupId );
            $customer->setCustomerstatus ( '0' );
        } elseif ($adminApproval != 1 && $data == 1) {
            $customer->setGroupId ( $groupId );
            $customer->setCustomerstatus ( '1' );
        } else {
            $customer->setCustomerstatus ( '1' );
        }
        /**
         * Check param 'is_subscribed'
         * set is subscribed value
         */
        if ($this->getRequest ()->getParam ( 'is_subscribed', false )) {
            $customer->setIsSubscribed ( 1 );
        }
        /**
         * Registration will fail if tax required, also if @DOB, @Gender aren't allowed in your profile
         */
        $errors = array ();
        $validationCustomer = $customer->validate ();
        if (is_array ( $validationCustomer )) {
            /**
             * Merge the error list into the array.
             */
            $errors = array_merge ( $validationCustomer, $errors );
        }
        $validationResult = true;
        $this->_getCustomerSession ();
        /**
         * Check the ccustomer validation.
         */
        if (true === $validationResult) {
            $customerId = $customer->save ()->getId ();
            $this->getStatus ( $customerId, $customer, $adminApproval, $data );
        } else {
            /**
             * If customer validation fails, display the error message.
             */
            $this->_getCustomerSession ()->setCustomerFormData ( $customer->getData () );
            $this->_getCustomerSession ()->addError ( $this->__ ( 'User profile can\'t provide all required info, please register and then connect with Apptha Social login.' ) );
            if (is_array ( $errors )) {
                foreach ( $errors as $errorMessage ) {
                    $this->_getCustomerSession ()->addError ( $errorMessage );
                }
            }
            $this->_redirect ( 'customer/account/create' );
            return;
        }
    }
    
    /**
     * Retrieve customer session from core customer session
     *
     * @return array
     */
    private function _getCustomerSession() {
        return Mage::getSingleton ( 'customer/session' );
    }
    
    /**
     * Redirect customer dashboard URL after logging in
     *
     * @return string URL
     */
    protected function _loginPostRedirect($sellerLogin) {
        $session = $this->_getCustomerSession ();
        
        if (! $session->getBeforeAuthUrl () || $session->getBeforeAuthUrl () == Mage::getBaseUrl ()) {
            /**
             * Set Default Account URL to customer session
             */
            if ($sellerLogin == 1) {
                $session->setBeforeAuthUrl ( Mage::helper ( 'marketplace/marketplace' )->dashboardUrl () );
            } else {
                $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getAccountUrl () );
            }
            /**
             * Redirect customer to the last page visited after logging in
             */
            if ($session->isLoggedIn ()) {
                if (! Mage::getStoreConfigFlag ( 'customer/startup/redirect_dashboard' ) && $referer = $this->getRequest ()->getParam ( Mage_Customer_Helper_Data::REFERER_QUERY_PARAM_NAME )) {
                    
                    $referer = Mage::helper ( 'core' )->urlDecode ( $referer );
                    if ($this->_isUrlInternal ( $referer )) {
                        $session->setBeforeAuthUrl ( $referer );
                    }
                }
                /**
                 * Get the authendication url for the customer.
                 * Set before authendication url.
                 */
                if ($session->getAfterAuthUrl ()) {
                    $session->setBeforeAuthUrl ( $session->getAfterAuthUrl ( true ) );
                }
            } else {
                $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getLoginUrl () );
            }
        } else if ($session->getBeforeAuthUrl () == Mage::helper ( 'customer' )->getLogoutUrl ()) {
            $session->setBeforeAuthUrl ( Mage::helper ( 'customer' )->getDashboardUrl () );
        } else {
            if (! $session->getAfterAuthUrl ()) {
                $session->setAfterAuthUrl ( $session->getBeforeAuthUrl () );
            }
            /**
             * Check customer is loggin or not.
             */
            if ($session->isLoggedIn ()) {
                $session->setBeforeAuthUrl ( $session->getAfterAuthUrl ( true ) );
            }
        }
        
        return $session->getBeforeAuthUrl ( true );
    }
    
    /**
     * @Twitter login action
     */
    public function twitterloginAction() {
        
        /**
         * Include Twitter files for oAuth connection
         */
        require 'sociallogin/twitter/twitteroauth.php';
        require 'sociallogin/config/twconfig.php';
        
        /**
         * Retrives @Twitter consumer key and secret key from core session
         */
        $twOauthToken = Mage::getSingleton ( 'customer/session' )->getTwToken ();
        $twOauthTokenSecret = Mage::getSingleton ( 'customer/session' )->getTwSecret ();
        $twitteroauth = new TwitterOAuth ( YOUR_CONSUMER_KEY, YOUR_CONSUMER_SECRET, $twOauthToken, $twOauthTokenSecret );
        
        /**
         * Get Accesss token from @Twitter oAuth
         */
        $oauthVerifier = $this->getRequest ()->getParam ( 'oauth_verifier' );
        $twitteroauth->getAccessToken ( $oauthVerifier );
        
        /**
         * Get @Twitter User Details from twitter account
         *
         * @return string Redirect URL or Customer save action
         */
        $userInfo = $twitteroauth->get ( 'account/verify_credentials' );
        $data = $this->getRequest ()->getParam ( 'fb' );
        if (isset ( $userInfo->error )) {
            Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Twitter Login connection failed' ) );
            $url = Mage::helper ( 'customer' )->getAccountUrl ();
            return $this->_redirectUrl ( $url );
        } else {
            $firstName = $userInfo->name;
            $userInfo->id;
            /**
             * Get the registered email in the twitter.
             */
            $email = Mage::getSingleton ( 'customer/session' )->getTwemail ();
            $lastName = ' ';
            if ($email == '' || $firstName == '') {
                /**
                 * Throw the error message if connection failed with the twitter account.
                 */
                Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Twitter Login connection failed' ) );
                $url = Mage::helper ( 'customer' )->getAccountUrl ();
                return $this->_redirectUrl ( $url );
            } else {
                /**
                 * If registration is success useing twitter email send to customer action controller.
                 */
                $this->customerAction ( $firstName, $lastName, $email, 'Twitter', $data );
            }
        }
    }
    
    /**
     * @Twitter post action
     *
     * @return string Returns Twitter page URL for Authentication
     */
    public function twitterpostAction() {
        $provider = '';
        $twitterEmail = ( string ) $this->getRequest ()->getPost ( 'email_value' );
        $data = $this->getRequest ()->getPost ( 'twitter_hidden' );
        Mage::getSingleton ( 'customer/session' )->setTwemail ( $twitterEmail );
        $customer = Mage::getModel ( 'customer/customer' );
        $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $twitterEmail );
        /**
         * Get customer id to get customer information.
         */
        $customerIdByEmail = $customer->getId ();
        $customer = Mage::getModel ( 'customer/customer' )->load ( $customerIdByEmail );
        $googleUID = $customer->getGoogleUid ();
        /**
         * Check google id to get customer information.
         */
        if ($googleUID != '') {
            $provider .= ' Google';
        }
        /**
         * Check facebook id to get customer information.
         */
        $facebookUID = $customer->getFacebookUid ();
        if ($facebookUID != '') {
            $provider .= ', Facebook';
        }
        /**
         * Check linkedin id to get customer information
         */
        $linkedinUID = $customer->getLinkedinUid ();
        if ($linkedinUID != '') {
            $provider .= ', Linkedin';
        }
        /**
         * Check yahoo user id to get customer information
         */
        $yahooUId = $customer->getYahooUid ();
        if ($yahooUId != '') {
            $provider .= ', Yahoo';
        }
        /**
         * Get twitter uid
         */
        $twitterUID = $customer->getTwitterUid ();
        $provider = ltrim ( $provider, ',' );
        
        /**
         * If customer id is empty.
         * Checked to get customer id from social login.
         */
        if ($customerIdByEmail == '') {
            $url = Mage::helper ( 'sociallogin' )->getTwitterUrl ( $data );
            $this->getResponse ()->setBody ( $url );
        } else if ($provider != '') {
            $url = Mage::helper ( 'sociallogin' )->getTwitterUrl ( $data );
            $this->getResponse ()->setBody ( $url );
        } else if (($provider == '') && ($twitterUID != '')) {
            $url = Mage::helper ( 'sociallogin' )->getTwitterUrl ( $data );
            $this->getResponse ()->setBody ( $url );
        } else {
            $url = Mage::helper ( 'sociallogin' )->getTwitterUrl ( $data );
            $this->getResponse ()->setBody ( $url );
        }
    }
    
    /**
	 * @facebook login action
	 *
	 * Connect facebook Using oAuth coonection.
	 *
	 * @return string redirect URL
	 *
	 */
 public function fbloginAction() {
       if($this->getRequest()->getParam('email')) {
	      $email = $this->getRequest()->getParam('email');
	      $firstName = $this->getRequest()->getParam('fname');
	      $lastName = $this->getRequest()->getParam('lname');
	      $data = $this->getRequest()->getParam('fb');
	      $this->customerAction($firstName, $lastName, $email, 'Facebook',$data);
	   } else {
	      Mage::getSingleton('customer/session')->addError($this->__('Facebook Login connection failed'));
	   }
	   $url = Mage::helper('customer')->getAccountUrl();
	   return $this->_redirectUrl($url);
    }
    
    /**
     * @Google login action
     *
     * Connect Google Using oAuth coonection.
     *
     * @return string redirect URL either customer save and loggedin or an error if any occurs
     */
    public function googlepostAction() {
    	
    	$error =  $this->getRequest ()->getParam ( 'error' );
    	if($error) {
    		return $this->_redirectUrl ( Mage::getBaseUrl() );
    	}
        /**
         * Include @Google library files for oAuth connection
         */
        require_once 'sociallogin/src/Google_Client.php';
        require_once 'sociallogin/src/contrib/Google_Oauth2Service.php';
        /**
         * Retrieves the @google_client_id, @google_client_secret
         */
        $googleClientId = Mage::getStoreConfig ( 'sociallogin/google/google_id' );
        $googleClientSecret = Mage::getStoreConfig ( 'sociallogin/google/google_secret' );
        $googleDeveloperKey = Mage::getStoreConfig ( 'sociallogin/google/google_develop' );
        $googleRedirectUrl = Mage::getUrl () . 'sociallogin/index/googlepost/';
        $gClient = new Google_Client ();
        /**
         * Set application name
         * set client id
         * ser client secret key
         * set redirect url
         * set developer key
         */
        $gClient->setApplicationName ( 'login' );
        $gClient->setClientId ( $googleClientId );
        $gClient->setClientSecret ( $googleClientSecret );
        $gClient->setRedirectUri ( $googleRedirectUrl );
        $gClient->setDeveloperKey ( $googleDeveloperKey );
        $googleOauthV2 = new Google_Oauth2Service ( $gClient );
        $token = Mage::getSingleton ( 'core/session' )->getGoogleToken ();
        $reset = $this->getRequest ()->getParam ( 'reset' );
        $data = $this->getRequest ()->getParam ( 'fb' );
        if ($reset) {
            unset ( $token );
            $gClient->revokeToken ();
            $this->_redirectUrl ( filter_var ( $googleRedirectUrl, FILTER_SANITIZE_URL ) );
        }
        
        $code = $this->getRequest ()->getParam ( 'code' );
        
        if (isset ( $code )) {
            /**
             * Get information from third party.
             */
            $gClient->authenticate ( $code );
            Mage::getSingleton ( 'core/session' )->setGoogleToken ( $gClient->getAccessToken () );
            $this->_redirectUrl ( filter_var ( $googleRedirectUrl, FILTER_SANITIZE_URL ) );
            $this->_redirectUrl ( $googleRedirectUrl );
            return;
        }
        
        if (isset ( $token )) {
            $gClient->setAccessToken ( $token );
        }
        if ($gClient->getAccessToken ()) {
            /**
             * Retrieve user details If user succesfully in Google
             */
            $user = $googleOauthV2->userinfo->get ();
            $user ['id'];
            filter_var ( $user ['name'], FILTER_SANITIZE_SPECIAL_CHARS );
            $email = filter_var ( $user ['email'], FILTER_SANITIZE_EMAIL );
            filter_var ( $user ['link'], FILTER_VALIDATE_URL );
            $token = $gClient->getAccessToken ();
            Mage::getSingleton ( 'core/session' )->setGoogleToken ( $token );
        } else {
            /**
             * get google google Authendication URL
             */
            $authUrl = $gClient->createAuthUrl ();
        }
        /**
         * If user doesn't logged-in redirects the login URL
         */
        if (isset ( $authUrl )) {
            $this->_redirectUrl ( $authUrl );
        } else {
            /**
             * Fetching user infor from an array, @param array $user
             *
             * @param string $given_name,
             *            $familyname, $email general info for users from @google account.
             */
            $firstName = $user ['given_name'];
            $lastName = $user ['family_name'];
            
            $email = $user ['email'];
            $user ['id'];
            
            if ($email == '') {
                Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Google Login connection failed' ) );
                $url = Mage::helper ( 'customer' )->getAccountUrl ();
                return $this->_redirectUrl ( $url );
            } else {
                
                $this->customerAction ( $firstName, $lastName, $email, 'Google', $data );
            }
        }
    }
    
    /**
     * @Yahoo login action
     *
     * Connect Yahoo Using oAuth coonection.
     *
     * @return string URL eiether customer save and loggedin or an error if any occurs
     */
    public function yahoopostAction() {
        /**
         * Include @Yahoo library files for oAuth connection
         */
        require 'sociallogin/lib/Yahoo.inc';
        YahooLogger::setDebug ( true );
        YahooLogger::setDebugDestination ( 'LOG' );
        /**
         *
         * @param string $yahoo_client_id,
         *            $yahoo_client_secret and $yahoo_developer_key
         *            
         *            Using this params setting up the connection to yahoo
         */
        $yahooClientId = Mage::getStoreConfig ( 'sociallogin/yahoo/yahoo_id' );
        $yahooClientSecret = Mage::getStoreConfig ( 'sociallogin/yahoo/yahoo_secret' );
        $yahooDeveloperKey = Mage::getStoreConfig ( 'sociallogin/yahoo/yahoo_develop' );
        $yahooDomain = Mage::getUrl ();
        /**
         * Use memcache to store oauth credentials via php native sessions
         *
         * Make sure you obtain application keys before continuing by visiting:<https://developer.yahoo.com/dashboard/createKey.html>
         */
        define ( 'OAUTH_CONSUMER_KEY', "$yahooClientId" );
        define ( 'OAUTH_CONSUMER_SECRET', "$yahooClientSecret" );
        define ( 'OAUTH_DOMAIN', "$yahooDomain" );
        define ( 'OAUTH_APP_ID', "$yahooDeveloperKey" );
        $para = $this->getRequest ()->getParams ();
        $data = $this->getRequest ()->getParam ( 'fb' );
        if (array_key_exists ( "logout", $para )) {
            YahooSession::clearSession ();
            $url = "index.php";
            $this->_redirectUrl ( $url );
        }
        $hasSession = YahooSession::hasSession ( OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID );
        /**
         * Get the current url.
         */
        YahooUtil::current_url ();
        if (! $hasSession) {
            $callback = YahooUtil::current_url () . "?in_popup";
            $sessionStore = new NativeSessionStore ();
            /**
             * Send request to the yahoo authendication url.
             */
            $authUrl = YahooSession::createAuthorizationUrl ( OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $callback, $sessionStore );
            if ($authUrl) {
                $this->_redirectUrl ( $authUrl );
            }
        } else {
            /**
             * Session for the api authendication for the yahoo login.
             */
            $session = YahooSession::requireSession ( OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID );
            if ($session) {
                $user = $session->getSessionedUser ();
                /**
                 * Get the customer information from the yahoo api response.
                 */
                $profile = $user->getProfile ();
                $profile->guid;
                $email = $profile->emails [0]->handle;
                $firstName = $profile->givenName;
                $lastName = $profile->familyName;
                if ($email == '') {
                    /**
                     * If email id is empty from the yahoo api call, return error message.
                     */
                    Mage::getSingleton ( 'customer/session' )->addError ( $this->__ ( 'Yahoo Login connection failed' ) );
                    $url = Mage::helper ( 'customer' )->getAccountUrl ();
                    return $this->_redirectUrl ( $url );
                } else {
                    /**
                     * if we get customer email, we can redirected to the customer registration controller.
                     */
                    $this->customerAction ( $firstName, $lastName, $email, 'Yahoo', $data );
                }
            }
        }
    }
    
    /**
     * Customer Login layout render Action
     *
     * Rendering the layout if social login extension is enabled
     */
    public function loginAction() {
        /**
         * check that already customer is logged in or not.
         */
        if ($this->_getCustomerSession ()->isLoggedIn ()) {
            $this->_redirect ( '*/*/' );
            return;
        }
        /**
         * Check the social login configuration is activation status.
         */
        if (Mage::getStoreConfig ( 'sociallogin/general/enable_sociallogin' ) == 1) {
            return;
        }
        $this->getResponse ()->setHeader ( 'Login-Required', 'true' );
        /**
         * Render layout for customer
         */
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->_initLayoutMessages ( 'catalog/session' );
        $this->renderLayout ();
    }
    
    /**
     * Customer Create Account layout render Action
     *
     * Rendering the layout if social login extension is enabled
     */
    public function createAction() {
        /**
         * Check that customer is logged in or not.
         */
        if ($this->_getCustomerSession ()->isLoggedIn ()) {
            $this->_redirect ( '*/*/' );
            return;
        } else {
            /**
             * Check that social login configuration activation status.
             */
            $enableStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_sociallogin' );
            if ($enableStatus == 1) {
                return;
            }
        }
        
        $this->loadLayout ();
        $this->_initLayoutMessages ( 'customer/session' );
        $this->renderLayout ();
    }
    
    /**
     * Validation for Tax/Vat field for current store
     */
    public function _isVatValidationEnabled($store = null) {
        return Mage::helper ( 'customer/address' )->isVatValidationEnabled ( $store );
    }
    
    /**
     * Customer welcome function
     *
     * Its used for print welcome message once successfully logged in
     *
     * @return string customer success page URL.
     */
    public function _welcomeCustomer(Mage_Customer_Model_Customer $customer, $isJustConfirmed = false) {
        $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Thank you for registering with %s.', Mage::app ()->getStore ()->getFrontendName () ) );
        
        /**
         * Send email for the newly registered users.
         */
        $customer->sendNewAccountEmail ( $isJustConfirmed ? 'confirmed' : 'registered', '', Mage::app ()->getStore ()->getId () );
        
        $successUrl = Mage::getUrl ( 'customer/account', array (
                '_secure' => true 
        ) );
        
        /**
         * Get the authendication url for the customer.
         */
        if ($this->_getCustomerSession ()->getBeforeAuthUrl ()) {
            $successUrl = $this->_getCustomerSession ()->getBeforeAuthUrl ( true );
        }
        return $successUrl;
    }
    
    /**
     * Customer login Action
     *
     * validate the social login form posted values if the user is registered user or not
     *
     * @return string Redirect URL.
     */
    public function customerloginpostAction() {
    	
    	$session = $this->_getCustomerSession ();
        
        /**
         * Get the email parameters of user name and password from the social login form.
         */
        $sellerLogin = $this->getRequest ()->getParam ( 'login_hidden' );
        /**
         * Set customer login array from parameter
         */
        $login ['username'] = $this->getRequest ()->getParam ( 'email' );
        $login ['password'] = $this->getRequest ()->getParam ( 'password' );
        
        /**
         * Check that customer is already login or not.
         */
        if ($session->isLoggedIn ()) {
            $message = 'Already loggedin';
            $this->getResponse ()->setBody ( $message );
            return;
        }
        if ($this->getRequest ()->isPost () && ! empty ( $login ['username'] ) && ! empty ( $login ['password'] )) {
            
            try {
                /**
                 * Check that is a seller login or customer login.
                 */
                if ($sellerLogin == 1) {
                    $customer = Mage::getModel ( 'customer/customer' );
                    $customer->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $login ['username'] );
                    $customerGroupId = $customer->getGroupId ();
                    $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
                    /**
                     * Display error messsage, if customer is logged into seller login.
                     */
                    if ($customerGroupId != $groupId) {
                        $this->getResponse ()->setBody ( 'You must have a Seller Account to access this page' );
                        return;
                    }
                    $customerStatus = $customer->getCustomerstatus ();
                    if ($customerStatus == 2 || $customerStatus == 0) {
                        /**
                         * If seller is logged in before admin approval, display the error message.
                         */
                        $this->getResponse ()->setBody ( 'Admin Approval is required for Seller Account' );
                        return;
                    }
                }
                $session->login ( $login ['username'], $login ['password'] );
                /**
                 * Set user name in customer/seller dashboard.
                 */
                if ($session->getCustomer ()->getIsJustConfirmed ()) {
                    $this->getResponse ()->setBody ( $this->_welcomeCustomer ( $session->getCustomer (), true ) );
                }
            } catch ( Mage_Core_Exception $e ) {
                /**
                 * Catch the error passed, when logged into the site.
                 */
                switch ($e->getCode ()) {
                    case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED :
                        $value = Mage::helper ( 'customer' )->getEmailConfirmationUrl ( $login ['username'] );
                        $message = Mage::helper ( 'customer' )->__ ( 'Account Not Confirmed', $value );
                        $this->getResponse ()->setBody ( $message );
                        break;
                    /**
                     * Display error message if email or password is not valid.
                     */
                    case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD :
                        $message = $this->__ ( 'Invalid Email Address or Password' );
                        $this->getResponse ()->setBody ( $message );
                        break;
                    default :
                        $message = $e->getMessage ();
                        $this->getResponse ()->setBody ( $message );
                }
                /**
                 * Set user name.
                 */
                $session->setUsername ( $login ['username'] );
            } catch ( Exception $e ) {
                return $e;
            }
            
            /**
             * Get the customerid,After logged into the site.
             */
            if ($session->getCustomer ()->getId ()) {
                $link = Mage::getSingleton ( 'customer/session' )->getLink ();
                $requestPath = '';
                if (! empty ( $link )) {
                    $splitLink = explode ( Mage::getBaseUrl (), $link );
                    $requestPath = end ( $splitLink );
                }
                /**
                 * Check the current url when user login to the site.
                 *
                 * Get the current url path.
                 */
                if ($requestPath == 'checkout/onestep/' || $requestPath == 'checkout/cart/') {
                    $this->getResponse ()->setBody ( Mage::getSingleton ( 'core/session' )->getReLink () );
                } else {
                    $enableRedirectStatus = 0;
                    /**
                     * Check the redirect url path, after customer login to the site redirect to the dashbord.
                     */
                    $enableRedirectStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );
                    if ($enableRedirectStatus == 1) {
                        $this->getResponse ()->setBody ( $this->_loginPostRedirect ( $sellerLogin ) );
                    } else {
                        /**
                         * Set redirect link from core session
                         */
                        $this->getResponse ()->setBody ( Mage::getSingleton ( 'core/session' )->getReLink () );
                    }
                }
            }
        }
    }
    
    /**
     * Customer Register Action
     *
     * validate the social regiter form posted values
     *
     * @return string Redirect URL.
     */
    public function createPostAction() {
        /**
         * Check the configuration of seller registration.
         */
        $adminApproval = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/need_approval' );
        $sellerRegisteration = $this->getRequest ()->getPost ( 'register_hidden' );
        /**
         * Get customer model 
         */
        $customer = Mage::getModel ( 'customer/customer' );
        /**
         * Get customer session
         */
        $session = $this->_getCustomerSession ();
        /**
         * check that customer session created or not.
         *
         * if session is created, redirect to the site.
         */
        if ($session->isLoggedIn ()) {
            /**
             * Set redirect url is base url
             */
            $this->_redirect ( '*/*/' );
            return;
        }
          /**
         * Set session message true
         */
        $session->setEscapeMessages ( true );
        if ($this->getRequest ()->isPost ()) {
            $errors = array ();
            if (! $customer = Mage::registry ( 'current_customer' )) {
                $customer = Mage::getModel ( 'customer/customer' )->setId ( null );
            }
            /**
             * Check the user as customer or seller.
             *
             * If it is not a seller as we can check the admin approval configuration(auto approval or not).
             *
             * Then we can set the customer status for the new user.
             */
            $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
            if ($adminApproval == 1 && $sellerRegisteration == 1) {
                $customer->setGroupId ( $groupId );
                /**
                 * Set customer status is zero
                 */
                $customer->setCustomerstatus ( '0' );
            } elseif ($adminApproval != 1 && $sellerRegisteration == 1) {
                /**
                 * Set customer group Id
                 */
                $customer->setGroupId ( $groupId );
                /**
                 * Set customer status is one
                 */
                $customer->setCustomerstatus ( '1' );
            } else {
                /**
                 * Set customer status is one
                 */
                $customer->setCustomerstatus ( '1' );
            }
            
            $customerForm = Mage::getModel ( 'customer/form' );
            /**
             * set form code.
             */
            $customerForm->setFormCode ( 'customer_account_create' )->setEntity ( $customer );
            
            $customerData = $customerForm->extractData ( $this->getRequest () );
            
            if ($this->getRequest ()->getParam ( 'is_subscribed', false )) {
                $customer->setIsSubscribed ( 1 );
            }
            
            $customer->getGroupId ();
            /**
             * Get the params value for customer address.
             */
            if ($this->getRequest ()->getPost ( 'create_address' )) {
                
                $address = Mage::getModel ( 'customer/address' );
                
                $addressForm = Mage::getModel ( 'customer/form' );
                /**
                 * set form code.
                 */
                $addressForm->setFormCode ( 'customer_register_address' )->setEntity ( $address );
                
                $addressData = $addressForm->extractData ( $this->getRequest (), 'address', false );
                $addressErrors = $addressForm->validateData ( $addressData );
                /**
                 * Check the validation for the address.
                 */
                if ($addressErrors === true) {
                    $address->setId ( null )->setIsDefaultBilling ( $this->getRequest ()->getParam ( 'default_billing', false ) )->setIsDefaultShipping ( $this->getRequest ()->getParam ( 'default_shipping', false ) );
                    $addressForm->compactData ( $addressData );
                    $customer->addAddress ( $address );
                    /**
                     * Check if error exist or not.
                     */
                    $addressErrors = $address->validate ();
                    if (is_array ( $addressErrors )) {
                        $errors = array_merge ( $errors, $addressErrors );
                    }
                } else {
                    /**
                     * if error occurerd we can display the error.
                     */
                    $errors = array_merge ( $errors, $addressErrors );
                }
            }
            try {
                /**
                 * Validate the customer information.
                 */
                $customerErrors = $customerForm->validateData ( $customerData );
                /**
                 * Check the $customerErrors is true
                 */
                if ($customerErrors !== true) {
                    $errors = array_merge ( $customerErrors, $errors );
                } else {
                    $customerForm->compactData ( $customerData );
                    
                    /**
                     * Get the password from the form and set the password in customer table.
                     */
                    $customer->setPassword ( $this->getRequest ()->getPost ( 'password' ) );
                    
                    $magentoVersion = Mage::getVersion ();
                    if (version_compare ( $magentoVersion, '1.9.1', '>=' )) {
                        $customer->setPasswordConfirmation ( $this->getRequest ()->getPost ( 'confirmation' ) );
                    } else {
                        $customer->setConfirmation ( $this->getRequest ()->getPost ( 'confirmation' ) );
                    }
                    /**
                     * Validate the customer information.
                     *
                     * If error occurs, merge the error with existing errors.
                     */
                    $customerErrors = $customer->validate ();
                    /**
                     * Check the customer error message is availbale in $customerErrors array
                     */
                    if (is_array ( $customerErrors )) {
                        $errors = array_merge ( $customerErrors, $errors );
                    }
                }
                $validationResult = count ( $errors ) == 0;
                if (true === $validationResult) {
                    $customerId = $customer->save ()->getId ();
                    
                    /**
                     * Check that account confirmation based on the email link.
                     */
                    if ($customer->isConfirmationRequired () && $adminApproval == 1 && $sellerRegisteration == 1) {
                        $customer->sendNewAccountEmail ( 'confirmation', $session->getBeforeAuthUrl (), Mage::app ()->getStore ()->getId () );
                        Mage::getModel ( 'marketplace/sellerprofile' )->adminApproval ( $customerId );
                        /**
                         * If user directly login to the site, we can display error message for account confirmation link sent to email.
                         */
                        $session->addSuccess ( $this->__ ( 'Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper ( 'customer' )->getEmailConfirmationUrl ( $customer->getEmail () ) ) );
                        $this->getResponse ()->setBody ( Mage::getUrl ( '/index', array (
                                '_secure' => true 
                        ) ) );
                        return;
                    }
                    if ($customer->isConfirmationRequired ()) {
                        $customer->sendNewAccountEmail ( 'confirmation', $session->getBeforeAuthUrl (), Mage::app ()->getStore ()->getId () );
                        /**
                         * If user directly login to the site, we can display error message for account confirmation link sent to email.
                         */
                        $session->addSuccess ( $this->__ ( 'Account confirmation is required. Please, check your email for the confirmation link. To resend the confirmation email please <a href="%s">click here</a>.', Mage::helper ( 'customer' )->getEmailConfirmationUrl ( $customer->getEmail () ) ) );
                        $this->getResponse ()->setBody ( Mage::getUrl ( '/index', array (
                                '_secure' => true 
                        ) ) );
                        /**
                         * @return the  message to customer
                         */
                        return;
                    }
                    /**
                     * if seller register into the site, after complete registration we can give a admin approval is reqired message.
                     */
                    if ($adminApproval == 1 && $sellerRegisteration == 1) {
                        Mage::getModel ( 'marketplace/sellerprofile' )->adminApproval ( $customerId );
                        /**
                         * Send the dispatch event after register success
                         */
                        Mage::dispatchEvent ( 'customer_register_success', array (
                                'account_controller' => $this,
                                'customer' => $customer 
                        ) );
                        Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
                        $this->getResponse ()->setBody ( Mage::getUrl ( '/index', array (
                                '_secure' => true 
                        ) ) );
                        return;
                    } else {
                    	if($sellerRegisteration==1 && $adminApproval==0){
                    	$getTemplateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/seller_email_template_selection' );
                    	/**
                    	 * Get the admin email id
                    	 */
                    	$adminEmailIdVal = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                    	/**
                    	 * Get the to mail id
                    	 */
                    	$getToMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/email" );
                    	/**
                    	 * Get the to mail id
                    	 */
                    	$getToName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdVal/name" );
                    	if ($getTemplateId) {
                    		$getEmailTemplateForSeller = Mage::getModel ( 'core/email_template' )->load ( $getTemplateId );
                    	} else {
                    		$getEmailTemplateForSeller = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_seller_email_template_selection' );
                    	}
                    	/**
                    	 * Get the customer information like
                    	 * customer data
                    	 * customer email
                    	 * customer name
                    	 */
                    	$customerData = Mage::getModel ( 'customer/customer' )->load ( $customerId );
                    	$getRecipient = $customerData->getEmail ();
                    	$getCustomerName = $customerData->getName ();
                    	$getEmailTemplateForSeller->setSenderEmail ( $getToMailId );
                    	$getEmailTemplateForSeller->setSenderName ( ucwords ( $getToName ) );
                    	/**
                    	 * Replacing values in email template with dynamic values
                    	 */
                    	$emailTemplateForSellerVariables = (array (
                    			'cname' => ucwords ( $getCustomerName ),
                    			'ownername' => ucwords ( $getToName )
                    	));
                    	$getEmailTemplateForSeller->setDesignConfig ( array (
                    			'area' => 'frontend'
                    	) );
                    	$getEmailTemplateForSeller->getProcessedTemplate ( $emailTemplateForSellerVariables );
                    	/**
                    	 * Sending email
                    	 */
                    	$getEmailTemplateForSeller->send ( $getRecipient, ucwords ( $getCustomerName ), $emailTemplateForSellerVariables );
                    	
                    	}
                    	if($sellerRegisteration==1){
                    	Mage::getModel ( 'marketplace/sellerprofile' )->newSeller ( $customerId );
                    	}
                    	/**
                         * After complete the registration, we can redirect to the customer dashboard.
                         */
                        Mage::dispatchEvent ( 'customer_register_success', array (
                                'account_controller' => $this,
                                'customer' => $customer 
                        ) );
                        /**
                         * Set the customer session value.
                         */
                        $session->setCustomerAsLoggedIn ( $customer );
                        $session->renewSession ();
                        $this->getResponse ()->setBody ( $this->_welcomeCustomer ( $customer ) );
                    }
                } else {
                    /**
                     * validate the customer data.
                     */
                    $session->setCustomerFormData ( $this->getRequest ()->getPost () );
                    if (is_array ( $errors )) {
                        foreach ( $errors as $errorMessage ) {
                            $session->$errorMessage;
                        }
                        /**
                         * @return the error message to customer
                         */
                        $this->getResponse ()->setBody ( $errorMessage );
                        return;
                    } else {
                        $session->addError ( $this->__ ( 'Invalid customer data' ) );
                    }
                }
            } catch ( Mage_Core_Exception $e ) {
                /**
                 * Check that register email is already register display an error message
                 */
                $session->setCustomerFormData ( $this->getRequest ()->getPost () );
                /**
                 * Check if customer email alredy exist
                 */
                if ($e->getCode () === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
                    $message = $this->__ ( 'Email already exists' );
                    $this->getResponse ()->setBody ( $message );
                    $session->setEscapeMessages ( false );
                    /**
                     * @return the error message to customer
                     */
                    return;
                } else {
                    /**
                     * @return the message to customer
                     */
                    $message = $e->getMessage ();
                    $this->getResponse ()->setBody ( $message );
                    return;
                }
                /**
                 * Add the customer error from session
                 */
                $session->addError ( $message );
            } catch ( Exception $e ) {
                /**
                 * Internal error, when save the customer information into the site.
                 */
                $session->setCustomerFormData ( $this->getRequest ()->getPost () )->addException ( $e, $this->__ ( 'Cannot save the customer.' ) );
            }
        }
        if (! empty ( $message )) {
            $this->getResponse ()->setBody ( $message );
        }
        /**
         * Check if customer redirct url is not a dashbord
         */
        if (! Mage::getStoreConfigFlag ( 'customer/startup/redirect_dashboard' )) {
            $this->getResponse ()->setBody ( Mage::getUrl ( '/index', array (
                    '_secure' => true 
            ) ) );
        } else {
            /**
             * Send the response after successful
             */
            $this->getResponse ()->setBody ( Mage::helper ( 'customer' )->getAccountUrl () );
        }
    }
    
    /**
     * ForgetPassword Action
     *
     * @param string $email
     *            Forget password action for forget password form
     *            
     * @return string $message.
     */
    public function forgotPasswordPostAction() {
        $email = ( string ) $this->getRequest ()->getParam ( 'forget_password' );
        $customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
        /**
         * Check if customer id is available or not
         */
        if ($customer->getId ()) {
            try {
                /**
                 * Generate the reset password link.
                 */
                $newResetPasswordLinkToken = Mage::helper ( 'customer' )->generateResetPasswordLinkToken ();
                $customer->changeResetPasswordLinkToken ( $newResetPasswordLinkToken );
                /**
                 * send a mail to registered password link to customer.
                 */
                $customer->sendPasswordResetConfirmationEmail ();
            } catch ( Exception $exception ) {
                $this->_getCustomerSession ()->addError ( $exception->getMessage () );
                return;
            }
            /**
             * Display a message after sent a reset password link to customer.
             */
            $message = $this->__ ( ' you will receive an email (' . $email . ') with a link to reset your password' );
        } else {
            /**
             * Give email is mismatched with customer email, throw error message to users.
             */
            $message = $this->__ ( 'There is no account associated with this email-id' ) . '.';
        }
        
        $this->getResponse ()->setBody ( $message );
    }
    /**
     * Get the customer status.
     *
     * @param
     *       customerId,customer data.
     */
    public function getStatus($customerId, $customer, $adminApproval, $data) {
        if ($adminApproval == 1 && $data == 1) {
            Mage::getModel ( 'marketplace/sellerprofile' )->adminApproval ( $customerId );
            Mage::dispatchEvent ( 'customer_register_success', array (
                    'account_controller' => $this,
                    'customer' => $customer 
            ) );
            /**
             * Check that admin approval configuration for sellers.
             */
            Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
            $redirecturl = $this->getResponse ()->setBody ( Mage::getUrl ( '/index', array (
                    '_secure' => true 
            ) ) );
            $this->_redirectUrl ( $redirecturl );
            /**
             * @return function
             */
            return;
        } else {
            $session = $this->_getCustomerSession ();
            Mage::getModel ( 'marketplace/sellerprofile' )->newSeller ( $customerId );
            Mage::dispatchEvent ( 'customer_register_success', array (
                    'account_controller' => $this,
                    'customer' => $customer 
            ) );
            /**
             * Set customer session for logged in
             */
            $session->setCustomerAsLoggedIn ( $customer );
            /**
             * Renew the customer session once customer logged in via the social login
             */
            $session->renewSession ();
            $redirecturl = $this->getResponse ()->setBody ( $this->_welcomeCustomer ( $customer ) );
            /**
             * Redirct url from the customer get status
             */
            $this->_redirectUrl ( $redirecturl );
            $this->_getCustomerSession ()->addSuccess ( $this->__ ( 'Thank you for registering with %s', Mage::app ()->getStore ()->getFrontendName () ) . '. ' . $this->__ ( 'You will receive welcome email with registration info in a moment.' ) );
            /**
             * After register send maol to customer 
             */
            $customer->sendNewAccountEmail ();
            
            $this->_getCustomerSession ()->setCustomerAsLoggedIn ( $customer );
            /**
             * Get the customer link from session
             */
            $link = Mage::getSingleton ( 'customer/session' )->getLink ();
            /**
             * Check if the link is empty or not
             */
            if (! empty ( $link )) {
                $requestPath = trim ( $link, '/' );
            }
            /**
             * Check if this path is onstep checkout or not
             */
            if ($requestPath == 'checkout/onestep') {
                $this->_redirect ( $requestPath );
                return;
            } else {
                $enableRedirectStatus = Mage::getStoreConfig ( 'sociallogin/general/enable_redirect' );
                if ($enableRedirectStatus) {
                    $redirect = $this->_loginPostRedirect ();
                } else {
                    $redirect = Mage::getSingleton ( 'core/session' )->getReLink ();
                }
                /**
                 * Redirect the url from after customer get status 
                 */
                $this->_redirectUrl ( $redirect );
                /**
                 *
                 * @return function
                 */
                return;
            }
        }
    }
}
