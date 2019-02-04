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
class Apptha_Productfilters_Block_Catalog_Product_List_Toolbar extends Mage_Catalog_Block_Product_List_Toolbar
{
	protected function _construct()
	{
		parent::_construct();
	}

    /**
     * Return current URL with rewrites and additional parameters
     *
     * @param array $params Query parameters
     * @return string
     */
    public function getPagerUrl($params = array())
    {
        if (!$this->helper('productfilters')->isEnabled()) {
            return parent::getPagerUrl($params);
        }

        if ($this->helper('productfilters')->isCatalogSearch()) {
            $params['isLayerAjax'] = null;
            return parent::getPagerUrl($params);
        }
        $params = array(
        		'_current' => true,
        		'_use_rewrite' => true,
        		'_query' => $params,
        		'_escape' => true,
        );
        
        $url = Mage::getUrl('*/*/*', $params);
        return $url;
    }

}