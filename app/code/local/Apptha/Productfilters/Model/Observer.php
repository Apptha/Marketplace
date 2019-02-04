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
 * @package     Apptha_Advancedfilters
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
class Apptha_Productfilters_Model_Observer
{
    /**
     * Add Json to response instead of default data
     */
    public function getResult()
    {
        $core = Mage::getSingleton('productfilters/core');
        if ($core->isAjaxrequest() && !$core->isProceed()) {
            $core->setProceed();
            /** @var $messages Apptha_Ajaxrequest_Model_Message_Storage */
            $messages = Mage::getSingleton('productfilters/message_storage');
            /** @var $response Apptha_Ajaxrequest_Model_Response */
            $response = Mage::getModel('productfilters/response');
            $response->setMessages($messages->getMessages());
            $response->loadContent(
                (array) Mage::app()->getRequest()->getParam('action_content', array()),
                (array) Mage::app()->getRequest()->getParam('custom_content', array())
            );
            $response->sendResponse();
        }
    }
}
