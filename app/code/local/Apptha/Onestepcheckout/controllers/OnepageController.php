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
require_once 'Mage/Checkout/controllers/OnepageController.php';
class Apptha_Onestepcheckout_OnepageController extends Mage_Checkout_OnepageController {
	
	/**
	 * Save checkout billing address
	 */
	public function saveBillingQuoteAction() {
		$zipcode = $this->getRequest ()->getPost ( 'zipcode' );
		$addressid = $this->getRequest ()->getPost ( 'address_id' );
		$data = $this->getRequest ()->getPost ( 'billing', array () );
		$result = $this->zipcodeValidation ( $zipcode, $addressid, $data );
		$this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
	}
	
	/**
	 * Save checkout billing address
	 */
	public function removeQuoteAction() {
		$zipcode = $this->getRequest ()->getPost ( 'zipcode' );
		$addressid = $this->getRequest ()->getPost ( 'address_id' );
		if (trim ( $zipcode ) == '' || ! is_numeric ( $zipcode )) {
			$address = Mage::getModel ( 'customer/address' )->load ( $addressid );
			$zipcode = $address->getPostcode ();
		}
		
		if (trim ( $zipcode ) != '' && is_numeric ( $zipcode )) {
			$cart = Mage::getModel ( 'checkout/cart' )->getQuote ();
			$product = array ();
			$cartHelper = Mage::helper ( 'checkout/cart' );
			foreach ( $cart->getAllItems () as $item ) {
				$id = $item->getProduct ()->getId ();
				$productCollection = Mage::getModel ( 'catalog/product' )->load ( $id );
				$productcity = $productCollection->getDelivery ();
				if (isset ( $productcity )) {
					$cityzipcode = explode ( ',', $productcity );
				}
				$collection = Mage::getModel ( 'marketplace/zipcode' )->load ( $zipcode, 'zipcode' );
				$citynames = trim ( $collection->getCity () );
				
				if (in_array ( $citynames, $cityzipcode )) {
				} else {
					$product [$id] ['name'] = $item->getName ();
					/**
					 * Get Qty
					 */
					$product [$id] ['qty'] = $item->getQty ();
					/**
					 * Get Product Id
					 */
					$product [$id] ['id'] = $item->getProductId ();
					$session = Mage::getModel ( 'core/session' )->setAppthaMpSplitCart ( $product );
					$cartHelper->getCart ()->removeItem ( $item->getItemId () )->save ();
				}
			}
		}
		$result ['success'] = 1;
		$this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $result ) );
	}
	
	/**
	 * Function for validating Zipcode
	 * 
	 * @return object
	 */
	public function zipcodeValidation($pincode, $addressid = 0, $data) {
		if (trim ( $pincode ) == '' || ! is_numeric ( $pincode )) {
			$address = Mage::getModel ( 'customer/address' )->load ( $addressid );
			
		}
		if ($pincode != '') {
			$sellerIds=$productIds=$productErrorIds=$productNames=$zipRange=$zipException=$productIds=$result=$zipCodeRangesForSeller=$zipCodeExceptionForSeller=$sellerZipCodeRangeValues=array();
			$errorFlag[]=$successFlag[]=$currentFlag=$currentErrorFlag=$currentSuccessFlag=0;
			$productName = '';
			$cart = Mage::getModel ( 'checkout/cart' )->getQuote ();
			
			
			foreach ( $cart->getAllItems () as $item ) {
				$id = $item->getProduct ()->getId ();
				$productName = $item->getProduct ()->getName();
				$productIds[$id]=$productName;
				$productCollection = Mage::getModel ( 'catalog/product' )->load ( $id );
				$sellerIds[$id]=$sellerId=$productCollection->getSellerId();
				$zipCodeDetailsCollection=  Mage::getModel('marketplace/productzipcode')->getCollection()->addFieldToFilter('seller_id',$sellerId);
				$zipCodeDetailsCollectionDatas=$zipCodeDetailsCollection->getData();
			
				foreach($zipCodeDetailsCollectionDatas as $zipCodeDetailsCollectionData){
					
					if(array_key_exists($sellerId,$zipCodeExceptionForSeller)){
						$zipCodeExceptionForSeller[$sellerId] =$zipCodeExceptionForSeller[$sellerId].','.$zipCodeDetailsCollectionData['seller_zipcode_exp'];
						
					}else{
						$zipCodeExceptionForSeller[$sellerId]= $zipCodeDetailsCollectionData['seller_zipcode_exp'];
					}
					$sellerZipCodeExceptionValues[$sellerId]= explode(",",$zipCodeExceptionForSeller[$sellerId]);
					if(array_key_exists($sellerId,$zipCodeRangesForSeller)){
						$zipCodeRangesForSeller[$sellerId] =$zipCodeRangesForSeller[$sellerId].','.$zipCodeDetailsCollectionData['seller_zipcode_range'];
					
					}else{
						$zipCodeRangesForSeller[$sellerId]= $zipCodeDetailsCollectionData['seller_zipcode_range'];
					}
					$sellerZipCodeRangeValues[$sellerId]= explode(",",$zipCodeRangesForSeller[$sellerId]);
					
				}
				
			}
			
			
			
		
			
			foreach ( $sellerIds as  $key=> $sellerId ) {
				
				$productCollection = Mage::getModel ( 'catalog/product' )->load ( $key );
				$productName=$productCollection->getName();
				$productIds[$id]=$productName;
				
				foreach($sellerZipCodeRangeValues[$sellerId] as  $sellerZipCodeRangeValue){
					if (preg_match('/-/',$sellerZipCodeRangeValue)){
						$formattedZipRangecode=explode("-",$sellerZipCodeRangeValue);
						if($pincode>=$formattedZipRangecode[0] && $pincode <= $formattedZipRangecode[1]){
							$successFlag[$key]=($pincode>=$formattedZipRangecode[0] && $pincode <= $formattedZipRangecode[1]) ? 1 : 0;
						}
						else{
						    
						    $productErrorIds[$key]=$productName;
						}
					}
					else{
				
						if($pincode==$sellerZipCodeRangeValue){
							$successFlag[$key]=($pincode==$sellerZipCodeRangeValue) ? 1 : 0;;
						}
						else{
						    $productErrorIds[$key]=$productName;
						}
					}
				}
				
				
				foreach($sellerZipCodeExceptionValues[$sellerId] as  $sellerZipCodeExceptionValue){
					if (preg_match('/-/',$sellerZipCodeExceptionValue)){
						$formattedZipExceptionCode=explode("-",$sellerZipCodeExceptionValue);
						$currentErrorFlag = ($pincode>=$formattedZipExceptionCode[0] && $pincode <= $formattedZipExceptionCode[1]) ? 1 : 0;
					}
					else{
						$currentErrorFlag = ($pincode==$sellerZipCodeExceptionValue) ? 1 : 0;
					}
					 if($currentErrorFlag==1){
						$productErrorIds[$key]=$productName;
					} 
					
				}
			}
			
			
			
			//$productshavingNotZipcodes=array_diff_key($productIds,$successFlag);
			if(!empty($productErrorIds)){
				$productshavingNotZipcodes=array_unique(array_merge($productErrorIds,$productshavingNotZipcodes));
			}
			if (empty($productErrorIds)){
						
				$result = array (
						'success' => 'Zipcode is available',
						'error' => false
				);
					
				}
				
				else{
					$result['error']   = 1;
					$result['message'] = 'Shipping is not available for this product "' . implode(",",$productErrorIds) . '" for given zipcode';
					
				
				}
				if (!isset($result ['error'])) {
					if ($this->getOnepage ()->getQuote ()->isVirtual ()) {
						$result ['goto_section'] = 'payment';
						$result ['update_section'] = array (
								'name' => 'payment-method',
								'html' => $this->_getPaymentMethodsHtml () 
						);
				} elseif (isset ( $data ['use_for_shipping'] ) && $data ['use_for_shipping'] == 1) {
					$result ['goto_section'] = 'shipping_method';
					$result ['update_section'] = array (
							'name' => 'shipping-method',
							'html' => $this->_getShippingMethodsHtml () 
					);
					
					$result ['allow_sections'] = array (
							'shipping' 
					);
					$result ['duplicateBillingInfo'] = 'true';
				} else {
					$result ['goto_section'] = 'shipping';
				}
			}
		} 
		
		
		
		return $result;
	}
	
	/**
	 * Function to get OnestepBillingId
	 *
	 * @return response
	 */
	public function getOnestepBillingIdAction() {
		$postCode = '';
		$customerAddressId = $this->getRequest ()->getParam ( 'billingid' );
		
		if (! empty ( $customerAddressId )) {
			$address = Mage::getModel ( 'customer/address' )->load ( $customerAddressId );
			$postCode = $address->getPostcode ();
		}
		$this->getResponse ()->setBody ( Mage::helper ( 'core' )->jsonEncode ( $postCode ) );
	}
}