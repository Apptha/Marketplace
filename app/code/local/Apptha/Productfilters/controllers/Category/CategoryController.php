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
 * @package    	Apptha_Advancedfilters
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
class Apptha_Productfilters_Catalog_CategoryController extends Mage_Core_Controller_Front_Action
{
	protected function _initCatagory()
	{
		Mage::dispatchEvent('catalog_controller_category_init_before', array('controller_action' => $this));
		$categoryId = (int) $this->getRequest()->getParam('id', false);
		if (!$categoryId) {
			return false;
		}
	
		$category = Mage::getModel('catalog/category')
		->setStoreId(Mage::app()->getStore()->getId())
		->load($categoryId);
	
		if (!Mage::helper('catalog/category')->canShow($category)) {
			return false;
		}
		Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($category->getId());
		Mage::register('current_category', $category);
		Mage::register('current_entity_key', $category->getPath());
	
		try {
			Mage::dispatchEvent(
			'catalog_controller_category_init_after',
			array(
			'category' => $category,
			'controller_action' => $this
			)
			);
		} catch (Mage_Core_Exception $e) {
			Mage::logException($e);
			return false;
		}
	
		return $category;
	}
	
	/**
	 * Recursively apply custom design settings to category if it's option
	 * custom_use_parent_settings is setted to 1 while parent option is not
	 *
	 * @deprecated after 1.4.2.0-beta1, functionality moved to Mage_Catalog_Model_Design
	 * @param Mage_Catalog_Model_Category $category
	 * @param Mage_Core_Model_Layout_Update $update
	 *
	 * @return Mage_Catalog_CategoryController
	 */
	protected function _applyCustomDesignSettings($category, $update)
	{
		if ($category->getCustomUseParentSettings() && $category->getLevel() > 1) {
			$parentCategory = $category->getParentCategory();
			if ($parentCategory && $parentCategory->getId()) {
				return $this->_applyCustomDesignSettings($parentCategory, $update);
			}
		}
	
		$validityDate = $category->getCustomDesignDate();
	
		if (array_key_exists('from', $validityDate) &&
		array_key_exists('to', $validityDate) &&
		Mage::app()->getLocale()->isStoreDateInInterval(null, $validityDate['from'], $validityDate['to'])
		) {
			if ($category->getPageLayout()) {
				$this->getLayout()->helper('page/layout')
				->applyHandle($category->getPageLayout());
			}
			$update->addUpdate($category->getCustomLayoutUpdate());
		}
	
		return $this;
	}
	
	public function viewAction()
	{
		$params = $this->getRequest()->getParams();
		$response = array();
	
		if ($category = $this->_initCatagory()) {
			$design = Mage::getSingleton('catalog/design');
			$settings = $design->getDesignSettings($category);
	
			// apply custom design
			if ($settings->getCustomDesign()) {
				$design->applyCustomDesign($settings->getCustomDesign());
			}
	
			Mage::getSingleton('catalog/session')->setLastViewedCategoryId($category->getId());
	
			$update = $this->getLayout()->getUpdate();
			$update->addHandle('default');
	
			if (!$category->hasChildren()) {
				$update->addHandle('catalog_category_layered_nochildren');
			}
	
			$this->addActionLayoutHandles();
			$update->addHandle($category->getLayoutUpdateHandle());
			$update->addHandle('CATEGORY_' . $category->getId());
			$this->loadLayoutUpdates();
	
			// apply custom layout update once layout is loaded
			if ($layoutUpdates = $settings->getLayoutUpdates()) {
				if (is_array($layoutUpdates)) {
					foreach($layoutUpdates as $layoutUpdate) {
						$update->addUpdate($layoutUpdate);
					}
				}
			}
	
			$this->generateLayoutXml()->generateLayoutBlocks();
			if($params['isAjax'] == 1){  //Check if it was an AJAX request
				// Generate New Layered Navigation Menu
				$viewPanel = $this->getLayout()->getBlock('catalog.leftnav')->toHtml();
				// Generate product list
				$productList = $this->getLayout()->getBlock('product_list')->toHtml();
				$response['status'] = 'SUCCESS';
				$response['viewpanel']=$viewPanel;
				$response['productlist'] = $productList;
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				return;
			}
			else{
				// apply custom layout (page) template once the blocks are generated
				if ($settings->getPageLayout()) {
					$this->getLayout()->helper('page/layout')->applyTemplate($settings->getPageLayout());
				}
	
				if ($root = $this->getLayout()->getBlock('root')) {
					$root->addBodyClass('categorypath-' . $category->getUrlPath())
					->addBodyClass('category-' . $category->getUrlKey());
				}
	
				$this->_initLayoutMessages('catalog/session');
				$this->_initLayoutMessages('checkout/session');
				$this->renderLayout();
			}
		}
		elseif (!$this->getResponse()->isRedirect()) {
			if($params['isAjax'] == 1)  //Check if it was an AJAX request
				$response['status'] = 'FAILURE';
			$this->_forward('noRoute');
		}
	}
}

?>