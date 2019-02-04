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
Class Apptha_Productfilters_CatalogSearch_ResultController extends Mage_Core_Controller_Front_Action
{ 
	/*
	 * To get Sessionfrom catalog module
	 * 
	 */  
	protected function _getSession()
    {
        return Mage::getSingleton('catalog/session');
    }
    
	public function indexAction()
	{
		$params = $this->getRequest()->getParams();
		$response = array();
		$query = Mage::helper('catalogsearch')->getQuery();
		/* @var $query Mage_CatalogSearch_Model_Query */
		$query->setStoreId(Mage::app()->getStore()->getId());
		if ($query->getQueryText() != '') {
			if (Mage::helper('catalogsearch')->isMinQueryLength()) {
				$query->setId(0)
				->setIsActive(1)
				->setIsProcessed(1);
			}
			else {
				if ($query->getId()) {
					$query->setPopularity($query->getPopularity()+1);
				}
				else {
					$query->setPopularity(1);
				}
	
				if ($query->getRedirect()){
					$query->save();
					$this->getResponse()->setRedirect($query->getRedirect());
					return;
				}
				else {
					$query->prepare();
				}
			}
			Mage::helper('catalogsearch')->checkNotes();
			$this->loadLayout();
			if($params['isAjax'] == 1){  //Check if it was an AJAX request
				$viewpanel = $this->getLayout()->getBlock('catalogsearch.leftnav')->toHtml(); //Get the new Layered Manu
				$productlist = $this->getLayout()->getBlock('search_result_list')->toHtml(); //New product List
				$response['status'] = 'SUCCESS'; //Send Success
				$response['viewpanel']=$viewpanel;
				$response['productlist'] = $productlist;
				$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
				return;
			}
			$this->_initLayoutMessages('catalog/session');
			$this->_initLayoutMessages('checkout/session');
			$this->renderLayout();
			if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
				$query->save();
			}
		}
		else {
			$this->_redirectReferer();
		}
	}
}
?>