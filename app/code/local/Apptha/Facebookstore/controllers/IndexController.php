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
 * @package     Apptha_FacebookStore
 * @version    	0.1.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
?>
<?php
class Apptha_Facebookstore_IndexController extends Mage_Core_Controller_Front_Action {
	/**
	 * Function to get Fb store details
	 *
	 * {@inheritDoc}
	 *
	 * @see Apptha_Facebookstore_IndexController::_construct()
	 */
	public function _construct() {
		parent::_construct ();
		$this->appId = Mage::getStoreConfig ( 'facebookstore/faceconnect/facebook_app_id' );
		$this->appSecret = Mage::getStoreConfig ( 'facebookstore/faceconnect/facebook_app_secret' );
	}
	/**
	 * Function to display products
	 *
	 * @return void
	 */
	public function productdisplayAction() {
		if (Mage::getStoreConfig ( 'facebookstore/faceconnect/enabled' ) == 1) {
			$this->loadLayout ();
			$this->renderLayout ();
		} else {
			$this->_redirect ( 'marketplace/seller/dashboard' );
			return;
		}
	}
	/**
	 * Function to display products in fb
	 *
	 * @return void
	 */
	public function defaultdisplayAction() {
		if (Mage::getStoreConfig ( 'facebookstore/faceconnect/enabled' ) == 1) {
			$this->loadLayout ();
			$this->renderLayout ();
		} else {
			$this->_redirect ( 'marketplace/seller/dashboard' );
			return;
		}
	}
}
