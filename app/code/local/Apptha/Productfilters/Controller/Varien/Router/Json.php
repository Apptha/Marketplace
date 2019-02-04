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
class Apptha_Productfilters_Controller_Varien_Router_Json extends Mage_Core_Controller_Varien_Router_Standard
{
    /**
     * Collect routes
     *
     * @param $configArea
     * @param $useRouterName
     */
    public function collectRoutes($configArea, $useRouterName)
    {
        parent::collectRoutes($configArea, 'standard');
    }

    /**
     * Match with router
     *
     * @param Zend_Controller_Request_Http $request
     * @return boolean
     */
    public function match(Zend_Controller_Request_Http $request)
    {
        $path = trim($request->getPathInfo(), '/');

        if (strrpos($path, '.json') === strlen($path) - 5) {
            $request->setPathInfo(substr($path, 0, strlen($path) - 5));
            Mage::getSingleton('productfilters/core')->setAjaxrequest(true);

            return parent::match($request);
        }

        return false;
    }
}