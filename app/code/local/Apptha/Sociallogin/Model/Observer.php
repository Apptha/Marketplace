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
class Apptha_Sociallogin_Model_Observer extends Mage_Core_Model_Abstract {
    
    /**
     * Captcha validation for create account form
     *
     * @return string $message for validation failed if any
     */
	public function checkcaptchaforcreate($observer){
		$formId = 'user_create';
		$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
		if ($captchaModel->isRequired()) {
			$controller = $observer->getControllerAction();
			$word = $this->_getCaptchaString($controller->getRequest(), $formId);
			if (!$captchaModel->isCorrect($word)) {
			 	
				$controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
				$controller->getResponse()->setBody("Incorrect CAPTCHA"); 
				
			}
		}
		return $this;
	}
	/**
	 * Get Captcha String
	 *
	 * @param Varien_Object $request
	 * @param string $formId
	 * @return string
	 */
	protected function _getCaptchaString($request, $formId)
	{
		$captchaParams = $request->getPost(Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE);
		return $captchaParams[$formId];
	}
  
	public function checkcaptcha($observer){
		$formId = 'user_login';
		$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
		if ($captchaModel->isRequired()) {
			$controller = $observer->getControllerAction();
			$word = $this->_getCaptchaString($controller->getRequest(), $formId);
			if (!$captchaModel->isCorrect($word)) {
				
				$controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
				$controller->getResponse()->setBody("Incorrect CAPTCHA");
				
			}
		}
		return $this;
	}
	
	public function checkCaptchaforgetpassword($observer){
		$formId = 'user_forgotpassword';
		$captchaModel = Mage::helper('captcha')->getCaptcha($formId);
		if ($captchaModel->isRequired()) {
			$controller = $observer->getControllerAction();
			$word = $this->_getCaptchaString($controller->getRequest(), $formId);
			if (!$captchaModel->isCorrect($word)) {
				
				$controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
				$controller->getResponse()->setBody("Incorrect CAPTCHA");
				
			}
		}
		return $this;
	}
}
