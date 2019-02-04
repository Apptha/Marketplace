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
 */

/**
 * Event Observer
 */
class Apptha_Marketplace_Model_Observer_Product {
    
    /**
     * If product edit(enable/disable) from admin panel this event function will be called to
     * send email notification to seller
     *
     * Product information will be get from the $observer parameter
     *
     * @param array $observer            
     *
     * @return void
     */
    public function productEditAction($observer) {
        if (Mage::getStoreConfig ( 'marketplace/product/productmodificationnotification' )) {
            $product = array ();
            /**
             * Define productGroup Id, Seller Id, Marketplace group Id.
             * Saved Product Status as empty
             */
            $productGroupId = $sellerId = $marketplaceGroupId = $savedProductStatus = '';
            $store = 0;
            $storeName = 'All Store Views';
            $product = $observer->getProduct ();
            $productGroupId = $product->getGroupId ();
            $sellerId = $product->getSellerId ();
            $productStatus = $product->getStatus ();
            $marketplaceGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
            $observer->getStoreId ();
            /**
             * Check Store value if it is not equal to zero assign the curresponding store name else Assign the store name as 'All Store Views'
             */
            if ($store != 0) {
                $storeName = Mage::getModel ( 'core/store' )->load ( $store );
            } else {
                $storeName = 'All Store Views';
            }
            $savedProductId = $product->getId ();
            $savedProduct = Mage::getModel ( 'catalog/product' )->load ( $savedProductId );
            $savedProductStatus = $savedProduct->getStatus ();
            if ($savedProductStatus != $productStatus && count ( $savedProduct ) >= 1 && $productGroupId == $marketplaceGroupId) {
                if ($productStatus == 1) {
                    $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/addproductenabledemailnotificationtemplate' );
                } else {
                    $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/addproductdisabledemailnotificationtemplate' );
                }
                $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
                /**
                 * Get Mail to Name
                 */
                $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
                /**
                 * Selecting template id
                 */
                if ($templateId) {
                    $emailTemplateData = Mage::getModel ( 'core/email_template' )->load ( $templateId );
                } else {
                    $emailTemplateData = Mage::helper ( 'marketplace/market' )->getEmailTemplate ( $productStatus );
                }
                $customer = Mage::getModel ( 'customer/customer' )->load ( $sellerId );
                $sellerEmail = $customer->getEmail ();
                /**
                 * Get Name of the product
                 */
                $productName = $product->getName ();
                /**
                 * Get product url
                */
                $productUrl = $product->getProductUrl ();
                /**
                 * Assign Recipient of mail
                 */
                $recipient = $sellerEmail;
                /**
                 * Get the name of the seller
                 */
                $sellerName = $customer->getName ();
  $emailTemplateData->setSenderName ( $toName );
                $emailTemplateData->setSenderEmail ( $toMailId );
                /**
                 * Update Email Template with the dynamic retrieved values
                 */
                $emailTemplateVariables = (array (
                        'ownername' => $toName,
                        'sellername' => $sellerName,
                        'adminemailid' => $toMailId,
                        'productname' => $productName,
                        'producturl' => $productUrl,
                        'storename' => $storeName 
                ));
                $emailTemplateData->setDesignConfig ( array (
                        'area' => 'frontend' 
                ) );
                $emailTemplateData->getProcessedTemplate ( $emailTemplateVariables );
                /**
                 * Mail Sending function
                 */
                $emailTemplateData->send ( $recipient, $toName, $emailTemplateVariables );
            }
        }
    }
    /**
     * If multiple product are selected to edit(enable/disable) from admin panel this event function will be called to
     * send email notification to seller
     *
     * Product information will be get from the $observer parameter
     *
     * @param array $observer            
     *
     * @return void
     */
    public function productMassEditAction($observer) {
        /**
         * Checking whether email notification enabled or not
         */
        if (Mage::getStoreConfig ( 'marketplace/product/productmodificationnotification' )) {
            $product = $productIds = $attributesData = array ();
            $storeName = 'All Store Views';
            $storeName = 0;
            $attributesData = $observer->getAttributesData ();
            $status = $attributesData ['status'];
            $productIds = $observer->getProductIds ();
            $store = $observer->getStoreId ();
            /**
             * Check store is not equal to zero
             * and get the store name
             */
            if ($store != 0) {
                $storeName = Mage::getModel ( 'core/store' )->load ( $store );
            } /**
             * If store is equal to zero
             * Assign Store name as 'All Store Views'
             */
            else {
                $storeName = 'All Store Views';
            }
            foreach ( $productIds as $productId ) {
                /**
                 * Define Mareketplace group id, product group id, prodcut status as empty
                 */
                $marketplaceGroupId = $prdouctGroupId = $sellerId = $productStatus = '';
                /**
                 * Get Group Id
                 */
                $marketplaceGroupId = Mage::helper ( 'marketplace' )->getGroupId ();
                $product = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $productId );
                $prdouctGroupId = $product->getGroupId ();
                $sellerId = $product->getSellerId ();
                /**
                 * Get Product Status
                 */
                $productStatus = $product->getStatus ();
                
                if ($productStatus != $status && $prdouctGroupId == $marketplaceGroupId) {
                    
                    /**
                     * Selecting template id
                     */
                    $emailTemplate = Mage::helper ( 'marketplace/general' )->getEmailTemplate ( $status );
                   $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                    $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
                    $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
                   $customer = Mage::helper ( 'marketplace/common' )->loadCustomerData ( $sellerId );
                    /**
                     * Get the seller Email
                     * and assign it as recipient
                     */
                    $sellerEmail = $customer->getEmail ();
                    $recipient = $sellerEmail;
                    /**
                     * get Seller Name
                     */
                    $sellerName = $customer->getName ();
                    /**
                     * Get Product Name
                     */
                    $productName = $product->getName ();
                    /**
                     * Get Product Link
                     */
                    $productUrl = $product->getProductUrl ();
                    /**
                     * Configure mail parameters
                     */
                    $emailTemplate->setSenderName ( $toName );
                    $emailTemplate->setSenderEmail ( $toMailId );
                    $emailTemplateVariables = (array (
                            'ownername' => $toName,
                            'sellername' => $sellerName,
                            'adminemailid' => $toMailId,
                            'productname' => $productName,
                            'producturl' => $productUrl,
                            'storename' => $storeName 
                    ));
                    $emailTemplate->setDesignConfig ( array (
                            'area' => 'frontend' 
                    ) );
                    $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                    /**
                     * sending mail
                     */
                    $emailTemplate->send ( $recipient, $toName, $emailTemplateVariables );
                }
            }
        }
    }
    
    /**
     * Admin product delete event
     */
    public function adminProductDelete($observer) {
    /**
     * Get product details
     * @var product id
     */
        $product = $observer->getProduct ();
        $productId = $product->getId ();
        Mage::helper ( 'marketplace/general' )->changeAssignProductId ( $productId );
    }
    /**
     * This method will run when the product is saved from the Magento Admin
     * Use this function to update the product model, process the
     * data or anything you like
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveProductTabData(Varien_Event_Observer $observer)
    {
    	$productId = $observer->getEvent()->getProduct()->getEntity_id();
    	$zipcodeState =  Mage::app()->getRequest()->getPost('zipcode_area');
    	if(!empty($zipcodeState)){
	    	$zipcodeAreas=json_encode($zipcodeState);
	    	$zipcodeModel = Mage::getModel('marketplace/productzipcode');
	    	
	    	$zipcodedata = array(
	    			'product_id'=> $productId,
	    			'zipcode_id'=> $zipcodeAreas,
	    			'created_time' =>now()
	    	);
	    	
	    	$zipcodeDatas=$zipcodeModel->getCollection()->addFieldToSelect('product_id');
	    	$zipcodeDatasCollection=$zipcodeDatas->getData();
	    	$productIds=array();
	    	foreach($zipcodeDatasCollection as $zipData){
	    		$productIds[]=$zipData['product_id'];
	    		
	    	}
	    	
	    	
	    	if(in_array($productId,$productIds)){
	    		$zipcodePrimaryData=$zipcodeModel->load($productId,'product_id');
	    		$model = $zipcodeModel->load($zipcodePrimaryData->getId())->addData($zipcodedata);
	    		$model->save();
	    	}
	    	else{
	    		$zipcodedata = array(
									'product_id'=>$productId,
									'zipcode_id'=>$zipcodeAreas,
									'created_time'=>now()
								);
	    		$zipcodeModel->setData($zipcodedata)->save();
	    	}
    	}
    }
   
}