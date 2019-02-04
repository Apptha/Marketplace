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
 * Function written in this file are globally accessed
 */
class Apptha_Marketplace_Helper_General extends Mage_Core_Helper_Abstract {
    /**
     * Get email template
     *
     * @param number $status            
     * @return number $templateId
     */
    public function getEmailTemplate($status) {
        if ($status == 1) {
            $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/addproductenabledemailnotificationtemplate' );
        } else {
            $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/product/addproductdisabledemailnotificationtemplate' );
        }
        
        if ($templateId) {
            $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
        } else {
            $emailTemplate = Mage::helper ( 'marketplace/market' )->getEmailTemplate ( $status );
        }
        
        return $emailTemplate;
    }
    
    /**
     * Get auto product name
     *
     * @param string $autoProductname            
     * @param array $options            
     * @param array $productData            
     * @param string $key            
     * @return string $autoProductName
     */
    public function getAutoProductName($autoProductName, $options, $productData, $key) {
    /**
     * Increment Foreach loop
     */
        foreach ( $options as $option ) {
            if ($productData [$key] == $option ['value']) {
                $autoProductName = $autoProductName . $option ['label'] . '-';
            }
        }
        return $autoProductName;
    }
    
    /**
     * Delete products
     *
     * @param array $productCollections            
     */
    public function deleteProducts($productCollections) {
    /**
     * Increment Foreach loop
     */
        foreach ( $productCollections as $product ) {
        /**
         * Get Product Id
         * @var int
         */
            $productId = $product->getEntityId ();
            /**
             * load product object
             * @var object
             */
            $model = Mage::getModel ( 'catalog/product' )->load ( $productId );
            /**
             * Delete product using product  id
             */
            $model->delete ();
        }
    }
    /**
     * Get seller approval email template
     *
     * @param
     *            $templateId
     * @return $emailTemplate
     */
    public function getSellerApprovalEmailTemplate($templateId, $value) {
    /**
     * Starting If condition
     */
        if ($templateId) {
            $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
        } else {
            if ($value == 1) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_seller_email_template_selection' );
            } elseif ($value == 3) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_seller_review_approve_review' );
            } elseif ($value == 4) {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_contact_email_template_selection' );
            } else {
                $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_admin_approval_seller_registration_seller_email_template_disapprove' );
            }
        }
        /**
         * End if condition
         */
        return $emailTemplate;
    }
    /**
     * Get address errors
     *
     * @param array $errors            
     * @param array $addressErrors            
     * @return array $errors
     */
    public function getAddressErrors($errors, $addressErrors) {
    /**
     * Check whether given variable is array or not
     */
        if (is_array ( $addressErrors )) {
        /**
         * Merge two arrays
         * @var array
         */
            $errors = array_merge ( $errors, $addressErrors );
        }
        return $errors;
    }
    
    /**
     * Change assign product id
     *
     * @param number $entity_id            
     * @return void
     */
    public function changeAssignProductId($entity_id) {
    /**
     * Get Product COllection
     * Filter by visibility
     */
        $getFirstAssignProduct = $assignProducts = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addFieldToFilter ( 'assign_product_id', $entity_id )->addAttributeToFilter ( 'is_assign_product', 1 )->addAttributeToFilter ( 'visibility', array (
                'eq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE 
        ) );
        /**
         * Get Count of assign products
         * @var unknown
         */
        $assignProductsCount = count ( $assignProducts );
        /**
         * Check assign product count is greater than 1
         */
        if ($assignProductsCount >= 1) {
            $firstAssignProduct = $getFirstAssignProduct->getFirstItem ();
            /**
             * Get Entity id
             */
            if ($firstAssignProduct->getEntityId ()) {
                $storeId = Mage::app ()->getStore ()->getStoreId ();
                /**
                 * Get Store id
                 */
                Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
                /**
                 * Load Product id
                 * @var id
                 */
                $firstAssignProductData = Mage::getModel ( 'catalog/product' )->load ( $firstAssignProduct->getEntityId () );
                $firstAssignProductData->setIsAssignProduct ( 0 );
                $firstAssignProductData->setAssignProductId ( '' );
                $firstAssignProductData->setVisibility ( Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH );
                $firstAssignProductData->save ();
                $newAssignProductId = $firstAssignProductData->getEntityId ();
                /**
                 * Set Current Store
                 */
                Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
                Mage::helper ( 'marketplace/product' )->updateAssignProductId ( $newAssignProductId, $assignProducts );
                Mage::app ()->setCurrentStore ( $storeId );
            }
        }       
    }
    
    /**
     * Get custom attribute option enable or not for seller
     */
    public function getCustomAttributeEnableOrNot(){
$flagVal = '';
if (Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/custom_attribute' )) {
$flagVal = 1;
} else {
$flagVal = 0;
}
return $flagVal;
}
    /**
     * Set seller commission globally when commission is not set for based on seller account.
     * 
     */
    public function setSellerCommission ($sellerCommission) {
/**
 * Starting If condition
 */
    if ($sellerCommission != 0) {
$percentPerProduct = $sellerCommission;
} else {
/**
 * Get Global commission
 * 
 */
$percentPerProduct = Mage::getStoreConfig ( 'marketplace/marketplace/percentperproduct' );
}
/**
 * End if clause
 */
return $percentPerProduct;
}
    
    /**
     * Get image validation
     *
     * @param array $uploader
     * @param number $imgSize
     * @param number $validateFlag
     * @return array $uploaderArray
     */
    public function getImageValidation($uploader, $imgSize, $validateFlag) {
$uploaderArray = array ();
/**
 * start if clause
 */
if (! $imgSize) {
$uploader->setFilesDispersion ( true );
$validateFlag = 1;
}
$uploaderArray ['uploader'] = $uploader;
$uploaderArray ['validate_flag'] = $validateFlag;
return $uploaderArray;
}
    /**
     * Function to getsubscription url
     *
     * This Function will return the redirect url to subscription page
     *
     * @return string
     */
    public function getSubscriptionUrl() {
return Mage::getUrl ( 'marketplace/subscription/subscribe' );
}
    
    /**
     * Function to delete deal price and date for products
     *
     * Passed the entity id in url to get the product details
     *
     * @param int $entityIds
     *            This Function will delete deal details
     * @return bool
     */
    public function deleteDeal($entityIds) {
    /**
     * load product object
     */
Mage::getModel ( 'catalog/product' )->load ( $entityIds )->setSpecialFromDate ( '' )->setSpecialToDate ( '' )->setSpecialPrice ( '' )->save ();
return true;
}
    
    /**
     * Function to get vacation mode savae url
     *
     * This Function will return the redirect url of vacation mode save action
     *
     * @return string
     */
    public function getVacationModeSaveUrl() {
/**
 * To check vacation mode
 */
return Mage::getUrl ( 'marketplace/general/vacationmodesave' );
}
    
    /**
     * Function to get new product url
     *
     * This Function will return the redirect url of new product form
     * @return string
     */
    public function getWithdrawalUrl() {
return Mage::getUrl ( 'marketplace/order/withdrawrequest' );
}
    
    /**
     * Set custom attribute
     * 
     * @param array $product
     * @param array $productData
     * @return array
     */
    public function customAttributeSave($product,$productData){
        /**
        * Update custom attributes values for seller inputs
        */    
        foreach ( $productData as $dataKey => $dataValue ) {
        /**
         * Check Array Exists
         */
if (array_key_exists ( $dataKey, $product->getData () ) && $productData [$dataKey] != $product->getData ( $dataKey )) {
$specificationFunction = 'set' . str_replace ( '_', "", uc_words ( $dataKey ) );
$product->$specificationFunction ( $productData [$dataKey] );
}
}
       return $product;
    }
    /**
     * Send cancel order status for Buyer
     *
     * @param $order array
     *
     * @return void
     */
    public function sendCancelOrderBuyerNotification($order) {
        /**
         * Get email template for buyer notification
         */
        $templateIdValueBuyer = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/cancel_notification_buyer_template_selection' );
        /**
         * Get order Id from order
        */
        $orderId = $order->getIncrementId();
        $recipientMail = $order->getCustomerEmail();
        $recipientName = $order->getCustomerFirstname();
        $adminEmailIdValue = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdValue/email" );
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdValue/name" );
        /**
         * Check template id has been set
         * if set then load that particular template
         *
        */
        if ($templateIdValueBuyer) {
            $emailTemplateBuyer = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateIdValue );
        } else {
            $emailTemplateBuyer = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_cancel_order_buyer_email_template_selection' );
        }
        $emailTemplateBuyer->setSenderName ( $toName );
        $emailTemplateBuyer->setSenderEmail ( $toMailId );
        /**
         * Dynamically replacing the email template variables with the retrieved values
        */
        $emailTemplateVariables = (array ('order_id'=>$orderId,'ownername' => $recipientName));
        $emailTemplateBuyer->setDesignConfig ( array ('area' => 'frontend') );
        $emailTemplateBuyer->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * Send email using dyanamically replaced template
        */
        $emailTemplateBuyer->send ( $recipientMail, $recipientName, $emailTemplateVariables );
    }
    /**
     * Send cancel order status for Admin
     *
     * @param $order array
     *
     * @return void
     */
    public function sendCancelOrderAdminNotification($orderId,$productId,$sellerId) {
    /**
     * Get Product Info
     * @var unknown
     */
        $getProduct = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $productId );
        $getSellerDetails = Mage::getModel('customer/customer')->load($sellerId);
        $getProductName = $getProduct->getName();
        $currencySymbol = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
        /**
         * Add the comment and save the order (last parameter will determine if comment will be sent to customer)
        */
        $order = Mage::getModel('sales/order')->load($orderId);
        $j = 0;
        $attributeLabel = $attributeValue = "";
        /**
         * Increment foreach loop
         */
        foreach($order->getAllItems() as $item)
        {
            if($item->getProductId () == $productId){
                $orderPrice = $item->getPrice () * $item->getQtyOrdered ();
                /**
                 * Get the Product Amount
                */
                $productAmt = $item->getPrice ();
                $productQty = $item->getQtyOrdered ();
                $orderPrice = $productAmt * $productQty;
                $productAmt = number_format ( $productAmt, 2 );
                $orderPrice = number_format ( $orderPrice, 2 );
                $productQty = round($item->getQtyOrdered ());
                if($j == 0) {
                    $options = $item->getProductOptions();
                    $customOptions = $options['attributes_info'];
                    $productTypeId = $getProduct->getTypeID();
                    if($productTypeId == 'configurable'){
                        $attributeLabel = $customOptions[0][label];
                        $attributeValue = $customOptions[0][value];
                        $j++;
                    }
                }
            }
        }
        $comment = 'The item "<b>'.$getProductName.'</b>" has been cancelled by '.$getSellerDetails->getFirstname();
        $displayLabel = '<dl class="item-options"><dt>'.$attributeLabel.'  </dt><dd>'.$attributeValue.'</dd>';
        $order->addStatusHistoryComment();
        $order->save();
        /**
         * Get email template for admin notification
        */
        $templateIdValueBuyer = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/cancel_notification_admin_template_selection' );
        /**
         * Get order Id from order
        */
        $orderId = $order->getIncrementId();
        $sellerName = $getSellerDetails->getFirstname();
        $sellerEmail = $getSellerDetails->getEmail();
        $displayProductQty = Mage::helper ( 'marketplace' )->__ ( 'Ordered QTY' );
        $displayProductAmt = Mage::helper ( 'marketplace' )->__ ( 'Ordered Amount' );
        $displayProductName = Mage::helper ( 'marketplace' )->__ ( 'Product Name' );
        $productDetailsHtml = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #eaeaea"><thead><tr>';
        $productDetailsHtml .= '<th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductName . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductQty . '</th>';
        $productDetailsHtml .= '<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductAmt . '</th>';
        $productDetailsHtml .= '</tr></thead>';
        $productDetailsHtml .= '<tbody bgcolor="#F6F6F6">';
        $productDetailsHtml .= '<tr>';
        $productDetailsHtml .= '<td align="left" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $getProductName .$displayLabel.'</td>
                <td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $productQty . '</td>';
        $productDetailsHtml .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . $orderPrice . '</td>';
        $productDetailsHtml .= '</tr>';
        $productDetailsHtml .= '</tbody><tfoot></tfoot></table>';
    
        $adminEmailIdValue = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
        $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdValue/email" );
        $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailIdValue/name" );
        /**
         * Check template id has been set
         * if set then load that particular template
         *
        */
        if ($templateIdValueBuyer) {
            $emailTemplateBuyer = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateIdValue );
        } 
        else {
            $emailTemplateBuyer = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_cancel_order_admin_email_update_selection' );
        }
        $emailTemplateBuyer->setSenderName ( $toName );
        $emailTemplateBuyer->setSenderEmail ( $toMailId );
        /**
         * Dynamically replacing the email template variables with the retrieved values
        */
        $emailTemplateVariables = (array ('order_id'=>$orderId,'ownername' => $toName,'productdetails'=>$productDetailsHtml,'seller_firstname'=>$sellerName,'seller_email'=>$sellerEmail,'comment'=>$comment));
        $emailTemplateBuyer->setDesignConfig ( array ('area' => 'frontend') );
        $emailTemplateBuyer->getProcessedTemplate ( $emailTemplateVariables );
        /**
         * Send email using dyanamically replaced template
        */
        $emailTemplateBuyer->send ( $toMailId, $toMailId, $emailTemplateVariables );
    }
}