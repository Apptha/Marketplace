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
class Apptha_Marketplace_Model_Observer {
    /**
     * Order saved successfully then commisssion information will be saved in database and email notification
     * will be sent to seller
     *
     * Order information will be get from the $observer parameter
     *
     * @param array $observer            
     *
     * @return void
     */
    public function successAfter($observer) {
        $sellerDefaultCountry = '';
        /**
         * Create an array for hold on seller ids
         */
        $sellerIdsArray = array ();
        $nationalShippingPrice = $internationalShippingPrice = $totalSellerAmount = 0;
        $orderIds = $observer->getEvent ()->getOrderIds ();
        /**
         * Get order collection.
         */
        $order = Mage::getModel ( 'sales/order' )->load ( $orderIds [0] );
        /**
         * Get order Shippment description.
         */
        $shippingDescription = $order->getShippingDescription();
        /**
         * Get customer information from session,Get customer id,Get grand total
         * Get status
         */
        $customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
        $getCustomerId = $customer->getId ();
        $grandTotal = $order->getGrandTotal ();
        $status = $order->getStatus ();
        $itemCount = 0;
        $shippingCountryId = '';
        $items = $order->getAllItems ();
        $orderEmailData = array ();
        /**
         * Get all item from the order collection,Get product id,Get created At
         */
        foreach ( $items as $item ) {
        $shippingPrice = $sellerShippingPrice = 0;
            $getProductId = $item->getProductId ();
            $createdAt = $item->getCreatedAt ();
            $paymentMethodCode = $order->getPayment ()->getMethodInstance ()->getCode ();
            $products = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $getProductId );
            $sellerId = $products->getSellerId ();
            $productType = $products->getTypeID ();
            /**
             * Get the shipping active status of seller
             */
            $sellerShippingEnabled = Mage::getStoreConfig ( 'carriers/apptha/active' );
            if ($sellerShippingEnabled == 1 && $productType == 'simple') {
                /**
                 * Get the product national shipping price,and international shipping price,and shipping country
                 */
                $nationalShippingPrice = $products->getNationalShippingPrice ();
                $internationalShippingPrice = $products->getInternationalShippingPrice ();
                $sellerDefaultCountry = $products->getDefaultCountry ();
                $shippingCountryId = $order->getShippingAddress ()->getCountry ();
            }
            Mage::helper( 'marketplace/productranking' )->updateTotalSalesForAssignedProducts($getProductId,'+');
            /**
             * Check seller id has been set
             */
            if ($sellerId) {
                /**
                 * Get order price,Get quantity ordered,Get payment method code
                 */
                $orderPrice = $item->getPrice () * $item->getQtyOrdered ();
                $productAmt = $item->getPrice ();
                $productQty = $item->getQtyOrdered ();
                if ($paymentMethodCode == 'paypaladaptive') {  
                $credited = 1;
                } else {
                    $credited = 0;
                }
                $marketplace_shipping_description = Mage::getStoreConfig('carriers/apptha/title').' - '.Mage::getStoreConfig('carriers/apptha/name');
                if(Mage::getStoreConfig ( 'marketplace/shipping/shippingcost' ) && $shippingDescription == $marketplace_shipping_description ){
                $shippingPrice = Mage::helper ( 'marketplace/market' )->getShippingPrice ( $sellerDefaultCountry, $shippingCountryId, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty );
                }else{
                $shippingPrice = Mage::helper ( 'marketplace/market' )->getShippingPrice ( $sellerDefaultCountry, $shippingCountryId, $orderPrice, 0, 0, $productQty );
                }
                $sellerShippingPrice = Mage::helper ( 'marketplace/market' )->getSellerShippingPrice ( $sellerDefaultCountry, $shippingCountryId, $orderPrice, $nationalShippingPrice, $internationalShippingPrice, $productQty );
                /**
                 * Getting seller commission percent
                 */
                $sellerCollection = Mage::helper ( 'marketplace/marketplace' )->getSellerCollection ( $sellerId );
                $percentPerProduct = Mage::helper ( 'marketplace/general' )->setSellerCommission ( $sellerCollection ['commission'] );
                $commissionFee = $orderPrice * ($percentPerProduct / 100);
                $sellerAmount = $shippingPrice;
                $commissionMode=Mage::getStoreConfig("marketplace/product/commission_mode");
                if($commissionMode=="category"){
                $commissionFee=$this->getCategoryCommission($getProductId,$orderPrice);
                }
                /**
                 * Check the delivery schedule is enable status if it's enabled delivery details will added into seller information
                 */
                $deliveryScheduleEnable = Mage::getStoreConfig ( 'deliveryschedule/general/delivery_schedule_enabled' );
                if ($deliveryScheduleEnable == 1 && $sellerShippingEnabled == 1 && ! in_array ( $sellerId, $sellerIdsArray )) {
                    /**
                     * Check the seller id exist or not
                     */                
                        $sellerIdsArray [] = $sellerId;
                        $deliveryCost = $order->getShippingDeliveryCost ();
                        $sellerAmount = $deliveryCost + $sellerAmount;                
                }
                /**
                 * Subtract the commision fee from seller amount
                 */
                $sellerAmount = $sellerAmount - $commissionFee;
                $orderEmailData = $this->deliverySheduleEnabled($orderEmailData,$deliveryScheduleEnable , $itemCount, $order);
                /**
                 * Storing commission information in database table
                 */
                if ($commissionFee > 0 || $sellerAmount > 0) {
                   $commissionDataArr = array (  'seller_id' => $sellerId,'product_id' => $getProductId,'product_qty' => $productQty,'product_amt' => $productAmt,'commission_fee' => $commissionFee,'seller_amount' => $sellerAmount,'seller_shipping_amount' => $sellerShippingPrice,'order_id' => $order->getId (),
                            'increment_id' => $order->getIncrementId (), 'order_total' => $grandTotal,'order_status' => $status,'credited' => $credited,'customer_id' => $getCustomerId,
                            'status' => 1,'created_at' => $createdAt, 'payment_method' => $paymentMethodCode  );
                    $commissionId = $this->storeCommissionData ( $commissionDataArr ); 
                    $orderCurrencySymbol = Mage::helper('marketplace')->orderCurrencySymbol($order->getId ());
                    $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
                    /**
                     * Compare the currency code for order product.
                     */
                    if($orderCurrencySymbol == $baseCurrencyCode){
                    $shippingAmount = number_format(round($sellerShippingPrice, 2), 2);
                    }else{
                    $orderCurrencyCode = Mage::helper('marketplace')->orderCurrencyCode($order->getId ());
                    $currencyRates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array($orderCurrencyCode));
                    $shippingAmount = number_format(round(($currencyRates[$orderCurrencyCode] * $sellerShippingPrice), 2), 2);
                    }
                    $productPrice = $orderPrice;
                    $sellerAmount = number_format(round(($productPrice + str_replace(',', '', $shippingAmount)), 2), 2);
                    $totalSellerAmount = number_format(round((str_replace(',', '', $totalSellerAmount) + str_replace(',', '', $sellerAmount)), 2), 2);
                    /**
                     * Store seller id in order email data
                     * and product quantity,product id,product amount,product commission fee, and seller amount,order id,customer email id
                     */
                    $orderEmailData [$itemCount] ['seller_id'] = $sellerId;
                    $orderEmailData [$itemCount] ['product_qty'] = $productQty;
                    $orderEmailData [$itemCount] ['product_id'] = $getProductId;
                    $orderEmailData [$itemCount] ['product_amt'] = $productPrice;
                    $orderEmailData [$itemCount] ['commission_fee'] = $commissionFee;
                    $orderEmailData [$itemCount] ['seller_amount'] = str_replace(',', '', $sellerAmount);
                    $orderEmailData [$itemCount] ['increment_id'] = $order->getIncrementId ();
                    $orderEmailData [$itemCount] ['customer_firstname'] = $order->getCustomerFirstname ();
                    $orderEmailData [$itemCount] ['customer_email'] = $order->getCustomerEmail ();
                    $itemCount = $itemCount + 1;
                }
            }
            if ($paymentMethodCode == 'paypaladaptive') {
            $this->updateCommissionPA ( $commissionId );
            }
        }
      if (Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/sales_notification' ) == 1) {
            $this->sendOrderEmail ( $orderEmailData );
        }
    }
    /**
     * 
     * @param unknown $deliveryScheduleEnable
     * @param unknown $itemCount
     * @param unknown $order
     * @return unknown
     */
    public function deliverySheduleEnabled ( $orderEmailData,$deliveryScheduleEnable , $itemCount, $order) {
        if ($deliveryScheduleEnable == 1) {
             /**
             * Get shipping delivery cost
             * Get shipping delivery comments
             * Get shipping delivery date
             * Get shipping delivery schedule
             * Get shipping delivery time
             */
            $deliveryCost = $order->getShippingDeliveryCost ();
            $deliveryComment = $order->getShippingDeliveryComments ();
            $deliveryDateInfo = $order->getShippingDeliveryDate ();
            $deliveryTypeInfo = $order->getShippingDeliverySchedule ();
            $deliveryTimeInfo = $order->getShippingDeliveryTime ();
            $orderEmailData [$itemCount] ['delivery_cost'] = $deliveryCost;
            $orderEmailData [$itemCount] ['delivery_comment'] = $deliveryComment;
            $orderEmailData [$itemCount] ['delivery_date'] = $deliveryDateInfo;
            $orderEmailData [$itemCount] ['delivery_type'] = $deliveryTypeInfo;
            $orderEmailData [$itemCount] ['delivery_time'] = $deliveryTimeInfo;          
        }
        return $orderEmailData;
    }
    /**
     * Update commission while uisng PayPal Adaptive
     */
    public function updateCommissionPA($commissionId) {
        /**
         * If payment method is paypal adaptive, then commission table(credited to seller) and transaction table(amout paid to seller) will be updated
         */
        $model = Mage::helper ( 'marketplace/transaction' )->getCommissionInfo ( $commissionId );
        
        /**
         * Get the Commission Fee of admin
         */
        $adminCommission = $model->getCommissionFee ();
        /**
         * Get the seller amount
         */
        $sellerCommission = $model->getSellerAmount ();
        /**
         * Get the Seller Id
         */
        $sellerId = $model->getSellerId ();
        /**
         * Get commission & order id
         */
        $commissionId = $model->getId ();
        $orderId = $model->getOrderId ();
        
        /**
         * transaction collection to update the payment information
         */
        $transaction = Mage::helper ( 'marketplace/transaction' )->getTransactionInfo ( $commissionId );
        $transactionIdVal = $transaction->getId ();
        /**
         * check transaction id is empty
         * if so update the transaction data like
         * commission id
         * seller id
         * seller commission
         * admin commission
         * order id in a variable
         * and save the transaction data
         */
        if (empty ( $transactionIdVal )) {
            $Data = array (
                    'commission_id' => $commissionId,
                    'seller_id' => $sellerId,
                    'seller_commission' => $sellerCommission,
                    'admin_commission' => $adminCommission,
                    'order_id' => $orderId,
                    'received_status' => 0 
            );
            Mage::helper ( 'marketplace/transaction' )->saveTransactionData ( $Data );
        }
        /**
         * Update the database after admin paid seller amount
         */
        $transactions = Mage::getModel ( 'marketplace/transaction' )->getCollection ()->addFieldToFilter ( 'seller_id', $sellerId )->addFieldToSelect ( 'id' )->addFieldToFilter ( 'paid', 0 );
        foreach ( $transactions as $transaction ) {
            $transactionIdVal = $transaction->getId ();
            /**
             * Check the transaction id is not empty
             */
            if (! empty ( $transactionIdVal )) {
                /**
                 * Update the transaction Details
                 */
                Mage::helper ( 'marketplace/transaction' )->updateTransactionData ( $transactionIdVal );
            }
        }
    }
    
    /**
     * Save seller commission data in database and get the commission id
     *
     * Commission information passed to update in database
     *
     * @param array $commissionDataArr
     *            This function will return the commission id of the last saved data
     * @return int
     */
    public function storeCommissionData($commissionDataArr) {
    /**
     * Get Marketplace Commission
     * @var object
     */
        $model = Mage::getModel ( 'marketplace/commission' );
        $model->setData ( $commissionDataArr );
        /**
         * Save data
         */
        $model->save ();
        return $model->getId ();
    }
    
    /**
     * Send Order Email to seller
     *
     * Passed the order information to send with email
     *
     * @param array $orderEmailData            
     *
     * @return void
     */
    public function sendOrderEmail($orderEmailData) {
        $deliveryScheduleEnable = Mage::getStoreConfig ( 'deliveryschedule/general/delivery_schedule_enabled' );
        $sellerIds = array ();
        $session = Mage::getSingleton('checkout/session');
        $incrementId = $session->getLastRealOrderId();
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $adminShippingAmount = $order['base_shipping_amount'];
        $isVirtual=$order->getIsVirtual();
        $shippingId=Mage::helper('marketplace/order')->getShippingIdFromOrder($isVirtual,$order);
        $shipMehtod = $order->getShippingMethod();
        $checkSellerOrProductShipping = Mage::getStoreConfigFlag('marketplace/shipping/shippingcost');
        $displayProductCommission = Mage::helper ( 'marketplace' )->__ ( 'Commission Fee' );
        $displaySellerAmount = Mage::helper ( 'marketplace' )->__ ( 'Seller Amount' ).'<span class=incl-shippment style="font-size: 10px;display: block;">'.Mage::helper ( 'marketplace' )->__ ( '(Incl. shippment)' ).'</span>';
        $displayProductName = Mage::helper ( 'marketplace' )->__ ( 'Product Name' );
        $displayProductQty = Mage::helper ( 'marketplace' )->__ ( 'Product QTY' );
        $displayProductAmt = Mage::helper ( 'marketplace' )->__ ( 'Product Amount' );
        foreach ( $orderEmailData as $data ) {
            if (! in_array ( $data ['seller_id'], $sellerIds )) {
                $sellerIds [] = $data ['seller_id'];
            }
        }
        if($checkSellerOrProductShipping != 1 && $shipMehtod =='apptha_apptha'){
          foreach ( $sellerIds as $id ) {
                $sellerProfile = Mage::getModel ( 'marketplace/sellerprofile' )->load ( $id, 'seller_id' );
                $sellerDefaultCountry = $sellerProfile['country'];
                $nationalShippingPrice = $sellerProfile['national_shipping_cost'];
                $internationalShippingPrice = $sellerProfile['international_shipping_cost'];
                $totalProductAmt = $totalCommissionFee = $totalSellerAmt = 0;
                $productDetails = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #eaeaea">';
                $productDetails .= '<thead><tr>';
                $productDetails .= '<th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductName . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductQty . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductAmt . '</th>';
                $productDetails .= '<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductCommission . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displaySellerAmount . '</th></tr></thead>';
                $productDetails .= '<tbody bgcolor="#F6F6F6">';
                $currencySymbol = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
                foreach($orderEmailData as $data ) {
                    if ($id == $data ['seller_id']) {
                        $sellerId = $data ['seller_id'];
                        $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
                        $productId = $data ['product_id'];
                        $product = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $productId );
                        $productGroupId = $product->getGroupId ();
                        $productTypeId=$product->getTypeId();
                        $productName = $product->getName ();
                        $productDetails .= '<tr>';
                        $productDetails .= '<td align="left" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $productName . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . round ( $data ['product_qty'] ) . '</td>';
                        $productDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $data ['product_amt'], 2 ) . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $data ['commission_fee'], 2 ) . '</td>';
                        $productDetails.=$this->getShippingDetails($sellerDefaultCountry, $shippingId, $nationalShippingPrice, $internationalShippingPrice,$data,$productTypeId);
                        $totalProductAmt = $totalProductAmt + ($data ['product_amt'] * $data ['product_qty']);
                        $totalCommissionFee = $totalCommissionFee + $data ['commission_fee'];
                        $totalSellerAmt = $totalSellerAmt + $data ['seller_amount']-$data['commission_fee'] ;
                        $typeIdArray=array("virtual","downloadable");
                        $nationalShippingPrices = in_array($productTypeId,$typeIdArray)?$nationalShippingPrices:$nationalShippingPrices + ($nationalShippingPrice * $data ['product_qty']);
                        $internationalShippingPrices = in_array($productTypeId,$typeIdArray)?$internationalShippingPrices:$internationalShippingPrices + ($internationalShippingPrice * $data ['product_qty']);
                        $customerEmail = $data ['customer_email'];
                        $incrementId = $data ['increment_id'];
                        $customerFirstname = $data ['customer_firstname'];
                        $deliveryScheleDetails=$this->getDeliveryScheduleDetails($deliveryScheduleEnable,$data);
                        $productDetails .= '</tr>';
                    }
                }
                $productDetails .= '</tbody><tfoot>';
                $productDetails.=Mage::helper('marketplace/order')->getDeliveryScheduleprice($deliveryScheduleEnable,$deliveryScheleDetails,$currencySymbol);
                if ($sellerDefaultCountry == $shippingId && $nationalShippingPrice != ''&&(!(in_array($productTypeId,$typeIdArray))) ){
                    $TotalSellerShppingCost = $totalSellerAmt + $nationalShippingPrices;
                    $TotalProductShppingCost = $totalCommissionFee + $totalSellerAmt + $nationalShippingPrices;
                    $productDetails .= '<tr><td colspan="4" align="right" style="padding:3px 9px">'.Mage::helper ( 'marketplace' )->__ ( 'Commision Fee' ).'</td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . round ( $totalCommissionFee, 2 ) . '</span></td></tr>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px">'.Mage::helper ( 'marketplace' )->__ ( 'Seller Amount' ).'<span style="font-size: 9px;display: block;">'.Mage::helper ( 'marketplace' )->__ ( '(Incl. shippment)' ).'</span></td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . round ( $TotalSellerShppingCost, 2 ) . '</span></td></tr>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px"><strong>'.Mage::helper ( 'marketplace' )->__ ( 'Total Product Amount' ).'</strong></td><td align="center" style="padding:3px 9px"><strong><span>' . $currencySymbol . round (($TotalProductShppingCost),2) . '</span></strong></td></tr>';
                    }
                else if ($sellerDefaultCountry != $shippingId && $internationalShippingPrice != '' &&(!(in_array($productTypeId,$typeIdArray)))){
                    $TotalSellerShppingCost = $totalSellerAmt + $internationalShippingPrices;
                    $TotalProductShppingCost = $totalCommissionFee + $totalSellerAmt + $internationalShippingPrices;
                    
                    $productDetails .= '<tr><td colspan="4" align="right" style="padding:3px 9px">'.Mage::helper ( 'marketplace' )->__ ( 'Commision Fee' ).'</td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . round ( $totalCommissionFee, 2 ) . '</span></td></tr>
                                <tr><td colspan="4" align="right" style="padding:3px 9px">'.Mage::helper ( 'marketplace' )->__ ( 'Seller Amount' ).'<span style="font-size: 9px;display: block;">'.Mage::helper ( 'marketplace' )->__ ( '(Incl. shippment)' ).'</span></td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . round ( $TotalSellerShppingCost, 2 ) . '</span></td></tr>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px"><strong>'.Mage::helper ( 'marketplace' )->__ ( 'Total Product Amount' ).'</strong></td><td align="center" style="padding:3px 9px"><strong><span>' . $currencySymbol . round (($TotalProductShppingCost),2) . '</span></strong></td></tr>';
                 } else {
                    $productDetails .= '<tr><td colspan="4" align="right" style="padding:3px 9px">'.Mage::helper ( 'marketplace' )->__ ( 'Commision Fee' ).'</td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . round ( $totalCommissionFee, 2 ) . '</span></td></tr>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px">'.Mage::helper ( 'marketplace' )->__ ( 'Seller Amount' ).'<span style="font-size: 9px;display: block;">'.Mage::helper ( 'marketplace' )->__ ( '(Incl. shippment)' ).'</span></td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . round ( $totalSellerAmt, 2 ) . '</span></td></tr>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px"><strong>'.Mage::helper ( 'marketplace' )->__ ( 'Total Product Amount' ).'</strong></td><td align="center" style="padding:3px 9px"><strong><span>' . $currencySymbol . round (($totalCommissionFee + $totalSellerAmt),2) . '</span></strong></td></tr>';
        
                }
                $productDetails .= '</tfoot></table>';
                $marketplaceGroupData=array('groupId'=>$groupId,'productGroupId'=>$productGroupId);
                Mage::helper('marketplace/order')->sendSellerShippingData($sellerId,$customerFirstname,$customerEmail,$productDetails,$sellerStore,$incrementId,$marketplaceGroupData);
            }
          } else {
            foreach ( $sellerIds as $id ) {
                $sellerProfile = Mage::getModel ( 'marketplace/sellerprofile' )->load ( $id, 'seller_id' );
                $totalProductAmt = $totalCommissionFee = $totalSellerAmt = 0;
                $productDataDetails = '<table cellspacing="0" cellpadding="0" border="0" width="650" style="border:1px solid #eaeaea">';
                $productDataDetails .= '<thead><tr>';
                $productDataDetails .= '<th align="left" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductName . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductQty . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductAmt . '</th>';
                $productDataDetails .= '<th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displayProductCommission . '</th><th align="center" bgcolor="#EAEAEA" style="font-size:13px;padding:3px 9px;">' . $displaySellerAmount . '</th></tr></thead>';
                $productDataDetails .= '<tbody bgcolor="#F6F6F6">';
                $currencySymbol = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
                foreach ( $orderEmailData as $data ) {
                    if ($id == $data ['seller_id']) {
                        $sellerId = $data ['seller_id'];
                        $groupId = Mage::helper ( 'marketplace' )->getGroupId ();
                        $productId = $data ['product_id'];
                        $product = Mage::helper ( 'marketplace/marketplace' )->getProductInfo ( $productId );
                        $productGroupId = $product->getGroupId ();
                        $adminCommissionFee = $data ['commission_fee'];
                        $productName = $product->getName ();
                        $productDataDetails .= '<tr>';
                        $productDataDetails .= '<td align="left" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $productName . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . round ( $data ['product_qty'] ) . '</td>';
                        $productDataDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $data ['product_amt'], 2 ) . '</td><td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $adminCommissionFee, 2 ) . '</td>';
                        $productDataDetails .= '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $data ['seller_amount']-$data ['commission_fee'], 2 ) . '</td>';
                        $totalProductAmt = $totalProductAmt + ($data ['product_amt'] * $data ['product_qty']);
                        $totalCommissionFee = $totalCommissionFee + $data ['commission_fee'];
                        $totalSellerAmt = $totalSellerAmt + $data ['seller_amount']-$data ['commission_fee'];
                        $customerEmail = $data ['customer_email'];
                        $incrementId = $data ['increment_id'];
                        $customerFirstname = $data ['customer_firstname'];
                        $deliveryScheleData=$this->getDeliveryScheduleDetails($deliveryScheduleEnable,$data);
                        $productDataDetails .= '</tr>';
                    }
                }
                $productDataDetails .= '</tbody><tfoot>';
                if ($deliveryScheduleEnable == 1) {
                   $productDataDetails .= '<tr><td colspan="1" style="padding:3px 9px;font-weight:bold;">' . Mage::helper ( 'deliveryschedule' )->__ ( 'Delivery Schedule' ) . '</td><td colspan="4" align="left" style="padding:3px 9px"><span>' .$deliveryScheleData['deliveryTypeInfo'] . '</span></td></tr>';
                    $productDataDetails .= '<tr><td colspan="1" style="padding:3px 9px;font-weight:bold;">' . Mage::helper ( 'deliveryschedule' )->__ ( 'Delivery Date' ) . '</td><td colspan="4" align="left" style="padding:3px 9px"><span>' . $deliveryScheleData['deliveryDateInfo'] . '  ' . $deliveryScheleData['deliveryTimeInfo'] . '</span></td></tr>';
                    $productDataDetails .= '<tr><td colspan="1" style="padding:3px 9px;font-weight:bold;">' . Mage::helper ( 'deliveryschedule' )->__ ( 'Delivery Comments' ) . '</td><td colspan="4" align="left" style="padding:3px 9px"><span>' . $deliveryScheleData['deliveryComment'] . '</span></td></tr>';
                    $productDataDetails .= '<tr><td colspan="1" style="padding:3px 9px;font-weight:bold;">' . Mage::helper ( 'deliveryschedule' )->__ ( 'Delivery Cost' ) . ' </td><td colspan="4" align="left" style="padding:3px 9px"><span>' . $currencySymbol . $deliveryScheleData['deliveryCost'] . '</span></td></tr>';
                }
                $totalCommissionFee = $totalCommissionFee + $adminShippingAmount ;
                $productDataDetails .= '<tr><td colspan="4" align="right" style="padding:3px 9px">'.Mage::helper ( 'marketplace' )->__ ( 'Commision Fee' ).'<span style="font-size: 9px;display: block;">'.Mage::helper ( 'marketplace' )->__ ( '(Incl. shippment)' ).'</span></td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . round ( $totalCommissionFee, 2 ) . '</span></td></tr>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px">'.Mage::helper ( 'marketplace' )->__ ( 'Seller Amount' ).'<span style="font-size: 9px;display: block;">'.Mage::helper ( 'marketplace' )->__ ( '(Incl. shippment)' ).'</span></td><td align="center" style="padding:3px 9px"><span>' . $currencySymbol . round ( $totalSellerAmt, 2 ) . '</span></td></tr>
                                 <tr><td colspan="4" align="right" style="padding:3px 9px"><strong>'.Mage::helper ( 'marketplace' )->__ ( 'Total Product Amount' ).'</strong></td><td align="center" style="padding:3px 9px"><strong><span>' . $currencySymbol . round (($totalCommissionFee + $totalSellerAmt),2) . '</span></strong></td></tr>';
                $productDataDetails .= '</tfoot></table>';
                $marketplaceData=array('groupId'=>$groupId,'productGroupId'=>$productGroupId);
                Mage::helper('marketplace/order')->sendOrderEmailData($sellerId,$productDataDetails,$incrementId,$sellerStore,$customerEmail,$customerFirstname,$marketplaceData);
            }
         }
      }
      
      
      
      public function getShippingDetails($sellerDefaultCountry,$shippingId,$nationalShippingPrice,$internationalShippingPrice,$data,$productTypeId){
          $typeIdArray=array("virtual","downloadable");
          if ($sellerDefaultCountry == $shippingId && $nationalShippingPrice != ''&&(!(in_array($productTypeId,$typeIdArray)))){
              $nationalShippingPrice = $nationalShippingPrice * $data ['product_qty'];
              $SellerShppingCost = $data ['seller_amount']-$data['commission_fee'] + $nationalShippingPrice;
              $shippingProductDetails = '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $SellerShppingCost, 2 ) . '</td>';
          }
          else if ($sellerDefaultCountry != $shippingId && $internationalShippingPrice != ''&&(!(in_array($productTypeId,$typeIdArray)))){
              $internationalShippingPrice = $internationalShippingPrice * $data ['product_qty'];
              $SellerShppingCost = $data ['seller_amount']-$data['commission_fee']  + $internationalShippingPrice;
              $shippingProductDetails = '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $SellerShppingCost, 2 ) . '</td>';
          } else {
              $shippingProductDetails = '<td align="center" valign="top" style="font-size:11px;padding:3px 9px;border-bottom:1px dotted #cccccc;">' . $currencySymbol . round ( $data ['seller_amount']-$data['commission_fee'] , 2 ) . '</td>';
          }
          return $shippingProductDetails;
      }
        /**
       * To get Delivery Schedule Details
       * @param $deliveryScheduleEnable boolean,$data array
       * @return array
       * 
       */
      public function getDeliveryScheduleDetails($deliveryScheduleEnable,$data){
          
          if ($deliveryScheduleEnable == 1) {
              $deliveryCost = $data ['delivery_cost'];
              $deliveryComment = $data ['delivery_comment'];
              $deliveryDateInfo = $data ['delivery_date'];
              $deliveryTypeInfo = $data ['delivery_type'];
              $deliveryTimeInfo = $data ['delivery_time'];
          
          return array('deliveryCost'=>$deliveryCost,'deliveryComment'=>$deliveryComment,'deliveryDateInfo'=>$deliveryDateInfo,'deliveryTypeInfo'=>$deliveryTypeInfo,'deliveryTimeInfo'=>$deliveryTimeInfo);
          }
      }
      
    /**
     * Setting Cron job to enable/disable vacation mode by seller
     *
     * @return void
     */
    public function eventVacationMode() {
        $currentDate = date ( "Y-m-d ", Mage::getModel ( 'core/date' )->timestamp ( time () ) );
        /**
         * Get vacation mode 
         * Filter by all attributes
         */
        $vacationInfo = Mage::getModel ( 'marketplace/vacationmode' )->getCollection ()->addFieldToSelect ( '*' );
       /**
        * Increment foreach loop
        */
        foreach ( $vacationInfo as $_vacationInfo ) {
            /**
             * Get Vacation info from date
             */
            $fromDate = $_vacationInfo ['date_from'];
            /**
             * Get Vacation info to date
             */
            $toDate = $_vacationInfo ['date_to'];
            /**
             * Get Seller id of each vacation
             */
            $sellerId = $_vacationInfo ['seller_id'];
            /**
             * Get product disabled status of each vacation product
             */
            $productStatus = $_vacationInfo ['product_disabled'];
            $product = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $sellerId );
            $productId = array ();
            /**
             * Increment foreach loop
             */
            foreach ( $product as $_product ) {
                $productId [] = $_product->getId ();
            }
            /**
             * Set current store
             */
            Mage::app ()->setCurrentStore ( Mage_Core_Model_App::ADMIN_STORE_ID );
            /**
             * Confirm the vacation is active by checking
             * current date is greater than or equal to vacation from-date
             * and current date is less than or equal to vacation to-date
             * and vacation product status is equal to zero
             * if so update the product status to 2
             */
            if ($currentDate >= $fromDate && $currentDate <= $toDate && $productStatus == 0) {
            /**
             * Increment foreach loop
             */
                foreach ( $productId as $_productId ) {
                    Mage::getModel ( 'catalog/product' )->load ( $_productId )->setStatus ( 2 )->save ();
                }
            }
            /**
             * check the current date is less than vacation from-date
             * and current date is greater than vacation to-date
             * if so update the product status to 1
             */
            if ($currentDate < $fromDate || $currentDate > $toDate) {
            /**
             * Increment foreach loop
             */
                foreach ( $productId as $_productId ) {
                    Mage::getModel ( 'catalog/product' )->load ( $_productId )->setStatus ( 1 )->save ();
                }
            }
        }
    }
     /**
     * Change status to disable for deleted seller products.
     *
     * @param object $observer            
     */
    public function customerdelete($observer) {
        $customer = $observer->getCustomer ();
        /**
         * Get Product Collection
         * Filter by seller id
         */
        $productCollections = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToFilter ( 'seller_id', $customer->getId () );
        /**
         * Increment foreach loop
         */
        foreach ( $productCollections as $product ) {
        /**
         * Get Product Id
         * @var int
         */
            $productId = $product->getEntityId ();
            Mage::helper ( 'marketplace/general' )->changeAssignProductId ( $productId );
        }
    }
    
    /**
     * Restrict seller product to buy themself
     *
     * @param object $observer            
     */
    public function addToCartEvent($observer) {    
    /**
         * check the observer event gull action name is equal to the checkout cart add
         */
        if ($observer->getEvent ()->getControllerAction ()->getFullActionName () == 'checkout_cart_add' || $observer->getEvent ()->getControllerAction ()->getFullActionName () == 'wishlist_index_add') {
            /**
             * Assign the customer id as empty
             */
            $customerId = '';
            /**
             * Check the customer is currently logged in
             * if so then get the customer data
             */
            if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
            /**
             * Get customer data
             * @var unknown
             */
                $customerData = Mage::getSingleton ( 'customer/session' )->getCustomer ();
                $customerId = $customerData->getId ();
            }
            
            $product = Mage::getModel ( 'catalog/product' )->load ( Mage::app ()->getRequest ()->getParam ( 'product', 0 ) );
            /**
             * Check the product id is not set
             * or cutomer id is empty
             * if so return
             */
            if (! $product->getId () || empty ( $customerId )) {
                return;
            }
            $sellerId = $product->getSellerId ();
            /**
             * check the the current customer id is equal to the seller id
             */
            if ($sellerId == $customerId) {
                
                /**
                 * Get assign product id
                 */
                $assignProductId = $product->getAssignProductId ();
                /**
                 * Get product url
                 */
                if (! empty ( $assignProductId )) {
                    $productUrl = Mage::getModel ( 'catalog/product' )->load ( $assignProductId )->getProductUrl ();
                } else {
                    $productUrl = $product->getProductUrl ();
                }
                /**
                 * Display error message if seller can try to buy there own products.
                 */
                if($observer->getEvent ()->getControllerAction ()->getFullActionName () == 'wishlist_index_add'){
                $msg = Mage::helper ( 'marketplace' )->__ ( "Seller can't add their own product in wishlist.");
                }else{
                $msg = Mage::helper ( 'marketplace' )->__ ( "Seller can't buy their own product." );
                }
              
                Mage::getSingleton ( 'core/session' )->addError ( $msg );
                Mage::app ()->getFrontController ()->getResponse ()->setRedirect ( $productUrl );
                Mage::app ()->getResponse ()->sendResponse ();
                $controller = $observer->getControllerAction ();
                $controller->getRequest ()->setDispatched ( true );
                $controller->setFlag ( '', Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true );
            }
        }
        return $this;
    }
    
    /**
     * Function to get Category Commision
     * @retun int
     */
    public function getCategoryCommission($getProductId,$orderPrice){
/**
 * load Product data
 * @var obj
 */
    $product = Mage::getModel('catalog/product')->load($getProductId);
    /**
     * Get category ids
     * @var unknown
     */
    $cats = $product->getCategoryIds();
    $commmax=array();
    /**
     * Increment foreach loop
     */
    foreach ($cats as $category_id) {
    /**
     * Load category data
     * @var int
     */
    $_cat = Mage::getModel('catalog/category')->load($category_id);
    $comm=$_cat->getCommission();
    /**
     * Checking whether it's empty array
     */
    if(!empty($comm)){
    $commmax[]= $comm;
    }
    }
    if (!empty($commmax)) {
    /**
     * Find maximum of commission
     * @var int
     */
    $percentperproduct = max($commmax);
    }
    else{
    /**
     * Get global commission
     * @var unknown
     */
    $percentperproduct=Mage::getStoreConfig("marketplace/marketplace/percentperproduct");
    }
    $percentperproduct=(int)$percentperproduct;
    return $orderPrice * ($percentperproduct / 100);
    }
    
    /**
     * Function to save special price offer
     * @params observer
     * @return void
     * 
     */
    public function saveOffer($observer){
        try{
            /**
             * Get Product Details
             * @obj
             */
            $product = $observer->getProduct ();
            $productId = $product->getId ();
            $product = Mage::getModel("catalog/product")->load($productId);
           /**
            * Get product price and special price
            */
            $productPrice = $product->getPrice();
            $productSpPrice = $product->getSpecialPrice();
            $productOfferPrice=$productPrice-$productSpPrice;
            $discount_percentage = ($productOfferPrice/$productPrice)*100;
            $discountPercentage=ceil($discount_percentage);
            /**
             * Calcualting Discount Percentage
             */
           
           if($discountPercentage < 10 ){
            	$discountPercentage = 5;
            }
            else{
            	$remainder=$discountPercentage%10;
            	$quotient = ($discountPercentage - $remainder) / 10;
            	$discountPercentage=$quotient*10;
            	
            	
            }
             if($discountPercentage>0){
            
            /**
             * Add Attribute option if not present
             * 
             * @return integer
             */
            $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', 'discount');
            $attributeId = $attribute->getId();
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attributeId);
            $options = $attribute->getSource()->getAllOptions(false);
            $optionArray=array();
            foreach($options as $opt){
                $optionArray[$opt['value']]=$opt['label'];
                
            }
            $attributeValue=array_search($discountPercentage,$optionArray);
            $product->setData($attributeId, $attributeValue)
            ->getResource()
            ->saveAttribute($product, $attributeId);
            }
        }catch (Exception $e) {
            Mage::logException($e->getMessage());
        }
    }
    
    
    /**
     * Setting Cron job to disable products if seller subscription expires
     *
     * @return void
     */
    public function eventExpireSubscription(){
       $subscribedInfo = Mage::getModel('marketplace/subscribedinfo')->getCollection()->addFieldToSelect('*');
    
       foreach ($subscribedInfo as $_subscribedInfo) {
           $sellerId = $_subscribedInfo->getSellerId();
            $checksubscription = Mage::helper('marketplace/subscription')->checkSubscribed($sellerId);
           if($checksubscription == 2) {
               $subscribedInfo = Mage::getModel('marketplace/subscribedinfo')->load($sellerId,'seller_id');
               $subscribedInfo->setStatus('expire');
               $subscribedInfo->setAdminApproval('');
               $subscribedInfo->setUpgradeTo('');
               $subscribedInfo->setPaypalVerify('');
               $subscribedInfo->save();
               $oldPlanId= $_subscribedInfo->getPlanId();
               $planInfo = Mage::getModel('marketplace/subscriptionplans')->load($oldPlanId);
               $PlanName= $planInfo->getPlanName();
               $admin_email_id= Mage::getStoreConfig('marketplace/marketplace/admin_email_id');
             
               $adminName= Mage::getStoreConfig("trans_email/ident_$admin_email_id/name");
               $custmerData = Mage::getModel('customer/customer')->load($sellerId)->getData();
               $sellerEmail= $custmerData['email'];
               $sellerName= $custmerData['firstname'].$custmerData['lastname'];
               $templeId       =  (int)Mage::getStoreConfig('marketplace/subscription/seller_plan_exipry');
                if ($templeId){
                   $emailTemplate = Mage::getModel('core/email_template')->load($templeId);
               }
               else{
                   $emailTemplate = Mage::getModel('core/email_template')
                   ->loadDefault('marketplace_subscription_seller_plan_exipry');
               }
               $emailTemplate->setSenderName($adminName);
               $emailTemplate->setSenderEmail($admin_email_id);
               $redirecturl = Mage::getBaseUrl().'marketplace/subscription/subscribe/';
               $emailTemplateVariables = (array('planname' =>$PlanName,'ownername'=>$sellerName, 'redirecturl'=>$redirecturl));
               $emailTemplate->setDesignConfig(array('area' => 'frontend'));
                $emailTemplate->getProcessedTemplate($emailTemplateVariables);
               $emailTemplate->send($sellerEmail, $sellerName, $emailTemplateVariables);
               $product = Mage::getModel('catalog/product')->getCollection()->addAttributeToFilter('seller_id', $sellerId);
               foreach ($product as $_product) {
                   $productId = $_product->getId();
                   Mage::getModel('catalog/product')->load($productId)->setStatus(2)->save();
               }
           }
       }
    }
    
}