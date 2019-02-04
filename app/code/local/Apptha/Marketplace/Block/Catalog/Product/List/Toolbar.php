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
 * This class is used to sort by total ranking
 * @author user
 *
 */
class Apptha_Marketplace_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar{
    /**
     * Products collection
     *
     * @var Mage_Core_Model_Mysql4_Collection_Abstract
     */
    protected $_collection = null;

    /**
     * GET parameter page variable
     *
     * @var string
     */
    protected $_pageVarName     = 'p';

    /**
     * GET parameter order variable
     *
     * @var string
     */
    protected $_orderVarName        = 'order';

    /**
     * GET parameter direction variable
     *
     * @var string
     */
    protected $_directionVarName    = 'dir';

    /**
     * GET parameter mode variable
     *
     * @var string
     */
    protected $_modeVarName         = 'mode';

    /**
     * GET parameter limit variable
     *
     * @var string
     */
    protected $_limitVarName        = 'limit';

    /**
     * List of available order fields
     *
     * @var array
     */
    protected $_availableOrder      = array();

    /**
     * List of available view types
     *
     * @var string
     */
    protected $_availableMode       = array();

    /**
     * Is enable View switcher
     *
     * @var bool
     */
    protected $_enableViewSwitcher  = true;

    /**
     * Is Expanded
     *
     * @var bool
     */
    protected $_isExpanded          = true;

    /**
     * Default Order field
     *
     * @var string
     */
    protected $_orderField          = null;

    /**
     * Default direction
     *
     * @var string
     */
    protected $_direction           = 'desc';

    /**
     * Default View mode
     *
     * @var string
     */
    protected $_viewMode            = null;

    /**
     * Available page limits for different list modes
     *
     * @var array
     */
    protected $_availableLimit  = array();

    /**
     * Default limits per page
     *
     * @var array
     */
    protected $_defaultAvailableLimit  = array(10=>10,20=>20,50=>50);

    /**
     * @var bool $_paramsMemorizeAllowed
     */
    protected $_paramsMemorizeAllowed = true;

    /**
     * Retrieve Catalog Config object
     *
     * @return Mage_Catalog_Model_Config
     */
    protected function _getConfig(){
        return Mage::getSingleton('catalog/config');
    }

   
}
