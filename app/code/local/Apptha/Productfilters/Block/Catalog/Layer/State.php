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
class Apptha_Productfilters_Block_Catalog_Layer_State extends Mage_Catalog_Block_Layer_State
{
	/**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('productfilters/catalog/layer/state.phtml');
	}
	/**
	 * Retrieve active filters
	 *
	 * @return array
	 */
	public function getActiveFilters()
	{
		$filters = $this->getLayer()->getState()->getFilters();
		if (!is_array($filters)) {
			$filters = array();
		}
		return $filters;
	}
	public function getClearUrl()
	{
		if ($this->helper('productfilters')->isCatalogSearch()) {
			$filterState = array('isLayerAjax' => null);
			foreach ($this->getActiveFilters() as $item) {
				$filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
			
			}
			$params['_current'] = true;
			$params['_use_rewrite'] = true;
			$params['_query'] = $filterState;
			$params['_escape'] = true;
			return Mage::getUrl('*/*/*', $params);
		}
	
		return parent::getClearUrl();;
	}

}
