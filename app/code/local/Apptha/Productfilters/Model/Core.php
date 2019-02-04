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
class Apptha_Productfilters_Model_Core
{
    /**
     * is Ajaxrequest 
     *
     * @var bool
     */
    protected $_isAjaxrequest = null;

    /**
     * IsAjaxrequest event processed
     *
     * @var bool
     */
    protected $_proceed = false;


    /**
     * Is Ajaxrequest
     *
     * @return bool
     */
    public function isAjaxrequest()
    {
        if ($this->_isAjaxrequest === null) {
            $this->_isAjaxrequest = Mage::app()->getRequest()->isXmlHttpRequest()
                && Mage::app()->getRequest()->getParam('ajax_request', false);
        }
        return (bool) $this->_isAjaxrequest;
    }

    /**
     * Set that is  ajaxrequest or not
     *
     * @param bool $value
     */
    public function setAjaxrequest($value = true)
    {
        $this->_isAjaxrequest = (bool) $value;
    }

    /**
     * Is event processed
     *
     * @return bool
     */
    public function isProceed()
    {
        return (bool) $this->_proceed;
    }

    /**
     * Set that event processed
     *
     * @return $this
     */
    public function setProceed()
    {
        $this->_proceed = true;

        return $this;
    }

}
