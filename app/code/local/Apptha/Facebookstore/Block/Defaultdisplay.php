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
 * @package     Apptha_FacebookStore
 * @version    	0.1.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
?>
<?php
class Apptha_Facebookstore_Block_Defaultdisplay extends Mage_Core_Block_Template{
	/**
	 * Function to get product details using product id
	 * @return obj
	 */
	public function displayProduct($productId) {
		$model = Mage::getModel ( 'catalog/product' );
		return $model->load ( $productId );
	}
	/** 
	 * Function to get the related product collection
	 * @return collection
	 * */
	public function displayRelatedproducts($productId) {
		$collection = Mage::getModel ( 'catalog/product_link' )->getCollection ()->addFieldToFilter ( 'product_id', $productId )->addFieldToFilter ( 'link_type_id', '1' );
		$collection->getSelect ()->limit ( 4 );
		return $collection;
	}
	/**
	 * FUcntion to get related collection
	 * @param unknown $value
	 * @return Mage_Core_Model_Abstract
	 */
	public function relatedCollection($value) {
		$collection = Mage::getModel ( 'catalog/product' )->load ( $value );
		return $collection;
	}
}
?>
