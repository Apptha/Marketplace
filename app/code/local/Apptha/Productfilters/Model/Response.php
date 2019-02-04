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
class Apptha_Productfilters_Model_Response extends Varien_Object
{
    /**
     * @var Zend_Controller_Response_Http
     */
    protected $_response;

    /**
     * Send response to browser with json content type
     */
    public function sendResponse()
    {
        $this->_response = Mage::app()->getResponse();

        //check redirect
        if ($this->_response->isRedirect()) {
            $headers = $this->_response->getHeaders();
            $redirect = '';
            foreach ($headers AS $header) {
                if ("Location" == $header["name"]) {
                    $redirect = $header["value"];
           
                    break;
                }
            }
            if ($redirect) {
                $this->setRedirect($redirect);
            }
        }

        $this->_response->clearHeaders();
        $this->_response->setHeader('Content-Type', 'application/json');
        $this->_response->clearBody();
        $this->_response->setBody($this->toJson());
        $this->_response->sendResponse();
        exit;
    }
    /**
     * Load content for layout
     *
     */
    public function loadContent($actionContent, $customContent)
    {
        if ($actionContent) {
            $layout = $this->_loadControllerLayouts();
            $actionContentData = array();
            foreach ($actionContent as $_content) {
                $_block = $layout->getBlock($_content);
                if ($_block) {
                    $actionContentData[$_content] = $_block->toHtml();
                }
            }
            if ($actionContentData) {
                $this->setActionContentData($actionContentData);
            }
        }

        if ($customContent) {
            $layout = $this->_loadCustomLayouts();
            $customContentData = array();
            foreach ($customContent as $_content) {
                $_block = $layout->getBlock($_content);
                if ($_block) {
                    $customContentData[$_content] = $_block->toHtml();
                }
            }
            if ($customContentData) {
                $this->setCustomContentData($customContentData);
            }
        }
    }

    /**
     * Load layouts for current controller
     *
     * @return Mage_Core_Model_Layout
     */
    protected function _loadControllerLayouts()
    {
        $layout = Mage::app()->getLayout();
        $update = $layout->getUpdate();
        // load default handle
        $update->addHandle('default');
        // load store handle
        $update->addHandle('STORE_'.Mage::app()->getStore()->getCode());
        // load theme handle
        $package = Mage::getSingleton('core/design_package');
        $update->addHandle(
            'THEME_'.$package->getArea().'_'.$package->getPackageName().'_'.$package->getTheme('layout')
        );
        // load action handle
        $fullActionName = Mage::app()->getRequest()->getRequestedRouteName() . '_' .
            Mage::app()->getRequest()->getRequestedControllerName() . '_' .
            Mage::app()->getRequest()->getRequestedActionName();
        $update->addHandle(strtolower($fullActionName));

        //load updates
        Mage::dispatchEvent(
            'controller_action_layout_load_before',
            array('action' => Mage::app()->getFrontController()->getAction(), 'layout' => $layout)
        );
      /*   $update->load();
        //generate xml
        $layout->generateXml();
        //generate layout blocks
        $layout->generateBlocks(); */

        return $layout;
    }

    /**
     * Load custom layout
     *
     * @return Mage_Core_Model_Layout
     */
    protected function _loadCustomLayouts()
    {
        $layout = Mage::app()->getLayout();
        $update = $layout->getUpdate();
        // load default custom handle
        $update->addHandle('ajax_request_default');
        // load action handle
        $fullActionName = Mage::app()->getRequest()->getRequestedRouteName() . '_' .
            Mage::app()->getRequest()->getRequestedControllerName() . '_' .
            Mage::app()->getRequest()->getRequestedActionName();
        $update->addHandle('ajax_request_' . strtolower($fullActionName));

        if (Mage::app()->useCache('layout')) {
            $cacheId = $update->getCacheId().'_ajax_request';
            $update->setCacheId($cacheId);

            if (!Mage::app()->loadCache($cacheId)) {
                foreach ($update->getHandles() as $handle) {
                    $update->merge($handle);
                }

                $update->saveCache();
            } else {
                //load updates from cache
                $update->load();
            }
        } else {
            //load updates
            $update->load();
        }

         //generate xml
      /*   $layout->generateXml();
        //generate layout blocks
        $layout->generateBlocks(); */
 
        return $layout;
    }
}
