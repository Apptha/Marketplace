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
 * @version     1.9.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2015 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */

/**
 * This file is used to event observer
 */
class Apptha_Superdeals_Model_Observer extends Mage_Core_Model_Abstract {
    
    /**
     * Define Constant string variables
     */
    const EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH = 'superdeals/general/email_template';
    const XML_PATH_EMAIL_IDENTITY = 'superdeals/general/sender_email_id';
    const XML_PATH_EMAIL_RECIPIENT = 'superdeals/general/receiver_email_id';
    const EMAIL_TO_RECIPIENT_TEMPLATE_NOTIFY_XML_PATH = 'superdeals/general/notify_template';
    
    /**
     * Send Periodic Mail to Admin When a Deal End
     *
     * return void
     */
    public function SendNotificationMail() {
        $superDeals = 'superdeals';
        $tdStart = '<td valign="top" style="border-width: 0; text-align:center; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:normal 13px arial; padding:5px; border-bottom: 0;">';
        $tdStartWithFontBold = '<td valign="top" style="border-width: 0; text-align:center; border: 1px solid #CCC; border-right:0; border-top:0; color: #0A263C; font:bold 13px arial; padding:5px; border-bottom: 0;">';
        $tdEnd = '</td>';
        
        /**
         * Check condition if dealz is enable and mail is enable
         */
        if ((Mage::helper ( $superDeals )->isDealzEnabled ()) && (Mage::helper ( $superDeals )->isMailEnabled ())) {
            /**
             * Get Currency Symbol
             */
            $sym = Mage::app ()->getLocale ()->currency ( Mage::app ()->getStore ()->getCurrentCurrencyCode () )->getSymbol ();
            /**
             * Get Deal end notification days
             */
            $notifyDays = Mage::getStoreConfig ( 'superdeals/general/deal_end_notification' );
            $now = Mage::getModel ( 'core/date' )->timestamp ( time () );
            $now = date ( "Y-m-d", $now );
            $end = Mage::getModel ( 'core/date' )->timestamp ( date ( "Y-m-d", strtotime ( "+$notifyDays days" ) ) );
            $end = date ( "Y-m-d", $end );
            $model = Mage::getModel ( 'superdeals/dealstatistics' )->getCollection ()->addFieldToFilter ( 'deal_end_date', array (
                    'from' => $now,
                    'to' => $end 
            ) );
            
            $model->addFieldToFilter ( 'status','Active');
            
            /**
             * check condition if model count is not equal to 0
             */
            if (count ( $model ) != 0) {
                $i = 1;
                /**
                 * load model data
                 */
                foreach ( $model as $mod ) {
                    $dealEndDetails .= '<tr style="background:#F8F7F5;">' . $tdStart . $i . $tdEnd . $tdStartWithFontBold . $mod->getDealId () . $tdEnd . $tdStart . $sym . $mod->getActualPrice () . $tdEnd . $tdStart . $sym . $mod->getDealPrice () . $tdEnd . $tdStart . $mod->getDealStartDate () . $tdEnd . $tdStart . $mod->getDealEndDate () . '</td><br/>
                                     </tr>';
                    $i ++;
                }
                
                $template = static::EMAIL_TO_RECIPIENT_TEMPLATE_NOTIFY_XML_PATH;
                $reciptent = static::XML_PATH_EMAIL_RECIPIENT;
                /**
                 * To create new object
                 */
                $postObject = new Varien_Object ();
                /**
                 * set data to send in the email template
                 */
                $postObject->setData ( 'deal_details', $dealEndDetails );
                $from_namee = Mage::getStoreConfig ( 'trans_email/ident_general/name' );
                $postObject->setData ( 'owner_name', $from_namee );
                
                try {
                    
                    $templateId = Mage::getStoreConfig ( $template );
                    $reciptentTo = Mage::getStoreConfig ( $reciptent );
                    /**
                     * fetch sender email Admin
                     */
                    $from_email = Mage::getStoreConfig ( 'trans_email/ident_general/email' );
                    /**
                     * fetch sender name Admin
                     */
                    $from_name = Mage::getStoreConfig ( 'trans_email/ident_general/name' );
                    $sender = array (
                            'name' => $from_name,
                            'email' => $from_email 
                    );
                    $mailTemplate = Mage::getModel ( 'core/email_template' );
                    
                    /**
                     * Send Transactional Email
                     */
                    $mailTemplate->sendTransactional ( $templateId, $sender, $reciptentTo, $from_name, $postObject->getData () );
                } catch ( Exception $e ) {
                    $this->_getSession ()->addError ( Mage::helper ( $superDeals )->__ ( "Email can not be send !" ) );
                }
            }
        }
    }
    
    /**
     * check order status of payapal
     *
     * return void
     */
    public function salesOrderStatusAfter(Varien_Event_Observer $observer) {
        $Complete = 'Complete';
        $smallComplete = 'complete';
        $orderNoWithEqual = 'order_no =?';
        $event = $observer->getEvent ();
        $quantity = '';
        $order_id = $event->getOrder ()->getId ();
        $order = Mage::getModel ( 'sales/order' )->load ( $order_id );
        $ordrId = $event->getOrder ()->getIncrementId ();
        $items = $order->getAllItems ();
        $tablePrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
        $orderTable = $tablePrefix . 'superdeals_orders';
        foreach ( $items as $item ) {
            $product_id = $item->getProductId ();
            $sku = $item->getSku ();
            $quantity = $item->getQtyOrdered ();
            $obj = Mage::getModel ( 'catalog/product' );
            $_product = $obj->load ( $product_id );
            $productName = $_product->getName ();
            $status = $event->getStatus ();
            $originalPrice = $_product->getPrice ();
            $specialPrice = $_product->getSpecialPrice ();
            $dealSpecialFromDate = $_product->getData ( 'special_from_date' );
            $dealSpecialToDate = $_product->getData ( 'special_to_date' );
            $now = Mage::getModel ( 'core/date' )->timestamp ( time () );
            $now = date ( "Y-m-d" . ' 00:00:00', $now );
            $saveAmount = ($originalPrice - $specialPrice) * $quantity;
            /**
             * check condition if not empty of special price and greaterthan special due date from current date and not empty of special due date
             */
            
            $isCheckSpecialFlag = $this->isCheckSpecialFlag ( $specialPrice, $dealSpecialToDate, $now );
            if ($isCheckSpecialFlag == 1) {
                
                $fields = array ();
                
                Mage::getModel ( 'superdeals/dealz' )->getCollection ()->addFieldToFilter ( 'duplicate', 1 )->addFieldToFilter ( 'order_no', $ordrId );
                /**
                 * check condition status is procession or closed or complete
                 */
                $isCheckStatus = $this->isCheckStatus ( $status, $smallComplete );
                
                if ($isCheckStatus == 1) {
                    $customArray = array (
                            'productName' => $productName,
                            'status' => $status,
                            'smallComplete' => $smallComplete,
                            'item' => $item,
                            'complete' => $Complete,
                            'orderNoWithEqual' => $orderNoWithEqual,
                            'ordrId' => $ordrId,
                            'orderTable' => $orderTable,
                            'order_id' => $order_id 
                    );
                    $product = $this->getCheckStatus ( $customArray );
                    /**
                     * check condition product is not null
                     */
                    if ($product != null) {
                        $tPrefix = ( string ) Mage::getConfig ()->getTablePrefix (); // get table prefix
                        $statisticsTable = $tPrefix . 'superdeals_reports';
                        $write = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
                        $selectResult = $write->query ( "select max(serial_id) from $statisticsTable where deal_id = '$productName'" );
                        $serialId = $selectResult->fetch ( PDO::FETCH_COLUMN );
                        $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_read' );
                        $select = $connection->select ()->from ( $statisticsTable, array (
                                'quantity' 
                        ) )->where ( 'serial_id=?', $serialId );
                        $oldQuantityArray = $connection->fetchRow ( $select );
                        $oldQuantity = $oldQuantityArray ['quantity'];
                        
                        /**
                         * TO GET PRODUCT STATUS FROM SUPERDEALS_ORDERS TABLE
                         */
                        
                        $connection->select ()->from ( $orderTable, array (
                                'product_status',
                                'status' 
                        ) )->where ( 'order_no=?', $ordrId );
                        
                        /**
                         * check condition status is equal to hold
                         */
                        
                        $newQuantity = $this->newQuantity ( $status, $oldQuantity, $quantity );
                        $newSales = $newQuantity * $specialPrice;
                        /**
                         * Calculate save amount
                         */
                        $saveAmount = ($originalPrice - $specialPrice) * $newQuantity;
                        $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
                        $connection->beginTransaction ();
                        $fields = array ();
                        $fields ['quantity'] = $newQuantity;
                        $fields ['total_sales'] = $newSales;
                        $fields ['save_amount'] = $saveAmount;
                        $fields ['status'] = 'Active';
                        $where = $connection->quoteInto ( 'serial_id=?', $serialId );
                        $connection->update ( $statisticsTable, $fields, $where );
                        $connection->commit ();
                    } else {
                        /**
                         * Collection starts here
                         */
                        $model = Mage::getModel ( 'superdeals/dealstatistics' );
                        $model->setProductId ( $product_id )->setDealId ( $productName )->setSku ( $sku )->setQuantity ( $quantity )->setActualPrice ( $originalPrice )->setDealPrice ( $specialPrice )->setTotalSales ( $newSales )->setSaveAmount ( $saveAmount )->setDealStartDate ( $dealSpecialFromDate )->setDealEndDate ( $dealSpecialToDate )->setStatus ( 'Active' );
                        $model->save ();
                    }
                }
            }
            
            $this->updateOrderStatus ( $status, $orderTable, $ordrId );
        }
    }
    
    /**
     *
     *
     *
     *
     * Update status and product status
     *
     * @param string $status            
     * @param string $smallComplete            
     * @param array $dupp            
     * @param array $item            
     * @param string $Complete            
     * @param array $connection            
     * @param string $orderNoWithEqual            
     * @param number $ordrId            
     * @param string $orderTable            
     * @param number $order_id            
     */
    public function updateProductStatus($customUpdateArray) {
        $fields = array ();
        $status = $customUpdateArray ['status'];
        $smallComplete = $customUpdateArray ['smallComplete'];
        $dupp = $customUpdateArray ['dupp'];
        $item = $customUpdateArray ['item'];
        $connection = $customUpdateArray ['connection'];
        $orderNoWithEqual = $customUpdateArray ['orderNoWithEqual'];
        $Complete = $customUpdateArray ['complete'];
        $ordrId = $customUpdateArray ['ordrId'];
        $orderTable = $customUpdateArray ['orderTable'];
        $order_id = $customUpdateArray ['order_id'];
        
        /**
         * check condition status is closed
         */
        if ($status == 'closed') {
            $dup = Mage::getModel ( 'superdeals/dealz' );
            $dupp = $dup->load ( $order_id );
            $dupQty = $dupp->getQuantity ();
            $quantity = $dupQty;
            $fields ['status'] = 'Closed';
            $fields ['product_status'] = 'Closed';
            $where = $connection->quoteInto ( $orderNoWithEqual, $ordrId );
            $connection->update ( $orderTable, $fields, $where );
            $connection->commit ();
        }
        
        /**
         * check condition status is processiong
         */
        if ($status == 'processing') {
            $quantity = $item->getQtyToShip ();
            $fields ['status'] = 'Processing';
            $fields ['product_status'] = $Complete;
            $fields ['duplicate'] = 1;
            $where = $connection->quoteInto ( $orderNoWithEqual, $ordrId );
            $connection->update ( $orderTable, $fields, $where );
            $connection->commit ();
        }
        
        /**
         * check condition if status is completed and dupp value is not equal to 1
         */
        if ($status == $smallComplete && $dupp != 1) {
            $quantity = $item->getQtyInvoiced ();
            $fields ['status'] = $Complete;
            $fields ['product_status'] = $Complete;
            $where = $connection->quoteInto ( $orderNoWithEqual, $ordrId );
            $connection->update ( $orderTable, $fields, $where );
            $connection->commit ();
        }
        
        /**
         * check condition if status is complete
         */
        if ($status == $smallComplete) {
            $fields ['status'] = $Complete;
            $fields ['product_status'] = $Complete;
            $where = $connection->quoteInto ( $orderNoWithEqual, $ordrId );
            $connection->update ( $orderTable, $fields, $where );
            $connection->commit ();
        }
    }
    
    /**
     *
     *
     *
     *
     * Calculate new quantity
     *
     * @param string $status            
     * @param number $oldQuantity            
     * @param number $quantity            
     * @return number $newQuantity
     */
    public function newQuantity($status, $oldQuantity, $quantity) {
        /**
         * To check status
         */
        if ($status == 'holded') {
            $newQuantity = $oldQuantity - $quantity;
        } else {
            $newQuantity = $oldQuantity + $quantity;
        }
        return $newQuantity;
    }
    
    /**
     * Is checking for status
     *
     * @param string $status            
     * @param string $smallComplete            
     * @return number $isCheckStatus
     */
    public function isCheckStatus($status, $smallComplete) {
        $isCheckStatus = 0;
        if ($status == $smallComplete || $status == 'processing' || $status == 'closed') {
            $isCheckStatus = 1;
        }
        return $isCheckStatus;
    }
    
    /**
     * Is check special price flag
     *
     * @param number $specialPrice            
     * @param string $dealSpecialToDate            
     * @param string $now            
     * @param string $dealSpecialToDate            
     * @return number $isCheckSpecialFlag
     */
    public function isCheckSpecialFlag($specialPrice, $dealSpecialToDate, $now) {
        $isCheckSpecialFlag = 0;
        if ((! empty ( $specialPrice )) && (($dealSpecialToDate > $now || empty ( $dealSpecialToDate )))) {
            $isCheckSpecialFlag = 1;
        }
        return $isCheckSpecialFlag;
    }
    
    /**
     * Update order status
     *
     * @param string $status            
     * @param string $orderTable            
     * @param number $ordrId            
     */
    public function updateOrderStatus($status, $orderTable, $ordrId) {
        $fields = array ();
        switch ($status) {
            case 'canceled' :
                $fields ['status'] = 'Canceled';
                break;
            case 'holded' :
                $fields ['status'] = 'On Hold';
                break;
            case 'fraud' :
                $fields ['status'] = 'Suspected Fraud';
                break;
            case 'payment_review' :
                $fields ['status'] = 'Payment Review';
                break;
            case 'pending' :
                $fields ['status'] = 'Pending';
                break;
            case 'pending_payment' :
                $fields ['status'] = 'Pending Payment';
                break;
            case 'pending_paypal' :
                $fields ['status'] = 'Pending PayPal';
                break;
            default :
                $fields = array ();
        }
        if (count ( $fields ) >= 1) {
            $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
            $connection->beginTransaction ();
            $where = $connection->quoteInto ( 'order_no=?', $ordrId );
            $connection->update ( $orderTable, $fields, $where );
            $connection->commit ();
        }
    }
    
    /**
     * Save deal order in custom table
     *
     * return void
     */
    public function afterSalesOrderSaved(Varien_Event_Observer $observer) {
        $event = $observer->getEvent ();
        /**
         * get Order ID
         */
        $orderId = $event->getOrder ()->getId ();
        /**
         * Load Order Id
         */
        $order = Mage::getModel ( 'sales/order' )->load ( $orderId );
        /**
         * Load Customer
         */
        $customer = Mage::getsingleton ( 'customer/customer' )->load ( $order->getCustomerId () );
        $items = $order->getAllItems ();
        foreach ( $items as $item ) {
            $productId = $item->getProductId ();
            /**
             * Get Product Data
             */
            $obj = Mage::getModel ( 'catalog/product' );
            $product = $obj->load ( $productId );
            /**
             * Get Name
             */
            $productName = $product->getName ();
            /**
             * Get Price
             */
            $originalPrice = $product->getPrice ();
            /**
             * Get Special Price
             */
            $specialPrice = $product->getSpecialPrice ();
            $dealSpecialToDate = $product->getData ( 'special_to_date' );
            $now = Mage::getModel ( 'core/date' )->timestamp ( time () );
            $now = date ( "Y-m-d" . ' 00:00:00', $now );
            /**
             * get email of a customer
             */
            $email = $customer->getEmail ();
            /**
             * get Firstname of a customer
             */
            $firstname = $customer->getFirstname ();
            /**
             * get lastname of customer
             */
            $lastname = $customer->getLastname ();
            /**
             * Checking whether customer loggedin or not
             */
            if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
                /**
                 * To get Email Id of a customer
                 */
                $email = $order->getBillingAddress ()->getEmail ();
                /**
                 * To get Firstname of a customer
                 */
                $firstname = $order->getBillingAddress ()->getFirstname ();
                /**
                 * To get Last name of a customer
                 */
                $lastname = $order->getBillingAddress ()->getLastname ();
            }
            /**
             * check condition if special price is not empty and due date is greater than to current date
             */
            if (! empty ( $specialPrice ) && (($dealSpecialToDate > $now || empty ( $dealSpecialToDate )))) {
                $model = Mage::getModel ( 'superdeals/dealz' );
                $model->setCustomerId ( $firstname . " " . $lastname )->setCustomerMailId ( $email )->setOrderNo ( $order->getIncrementId () )->setDealId ( $productName )->setQuantity ( $item->getQtyToInvoice () )->setActualPrice ( $originalPrice )->setDealPrice ( $specialPrice )->setPurchaseDate ( $order->getCreated_at () )->setStatus ( 'Pending' );
                $model->save ();
            }
        }
    }
    
    /**
     * Save data in deal statistics table
     *
     * return void
     */
    public function afterProductSaved(Varien_Event_Observer $observer) {
        $product = $observer->getProduct ();
        $dealName = $product->getName ();
        /**
         * Get Product original price
         */
        $dealOriginalPrice = $product->getPrice ();
        $dealprice = floatval ( $dealOriginalPrice );
        /**
         * Get Deal special Price
         */
        $dealSpecialPrice = $product->getSpecialPrice ();
        /**
         * Get Deal Starting Date
         */
        $dealSpecialFromDate = $product->getData ( 'special_from_date' );
        /**
         * Get Deal End Date
         */
        $dealSpecialToDate = $product->getData ( 'special_to_date' );
        /**
         * get table prefix
         */
        $tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
        $statisticsTable = $tPrefix . 'superdeals_reports';
        /**
         * check condition if special to date is empty
         */
        if (empty ( $dealSpecialToDate )) {
            $dealSpecialToDate = NULL;
        }
        $now = Mage::getModel ( 'core/date' )->timestamp ( time () );
        $now = date ( "Y-m-d" . ' 00:00:00', $now );
        $product_id = $product->getEntityId ();
        $sku = $product->getSku ();
        $sample = Mage::getModel ( 'superdeals/dealstatistics' )->getCollection ()->addFieldToFilter ( 'deal_id', $dealName )->addFieldToFilter ( 'product_id', $product_id )->addFieldToFilter ( 'sku', $sku )->addFieldToFilter ( 'actual_price', $dealprice )->addFieldToFilter ( 'deal_price', $dealSpecialPrice )->addFieldToFilter ( 'deal_start_date', $dealSpecialFromDate )->addFieldToFilter ( 'status', 'Active' );
        /**
         * check condition if special to date is not empty
         */
        if (! empty ( $dealSpecialToDate )) {
            $sample->addFieldToFilter ( 'deal_end_date', $dealSpecialToDate );
        }
        $count = count ( $sample );
        $checkDates = empty ( $dealSpecialToDate ) && ($dealprice > $dealSpecialPrice);
        if (($count == 0) && ((! empty ( $dealSpecialPrice )) && ($dealSpecialToDate > $now || $checkDates))) {
            
            /**
             * Update Old Deal With same name to inactive
             */
            $connections = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
            $connections->beginTransaction ();
            $fields = array ();
            $fields ['status'] = 'Inactive';
            $where = $connections->quoteInto ( 'product_id=?', $product_id );
            $connections->update ( $statisticsTable, $fields, $where );
            $connections->commit ();
            $collection = Mage::getModel ( 'superdeals/dealstatistics' );
            $data = array (
                    'product_id' => $product_id,
                    'deal_id' => $dealName,
                    'sku' => $sku,
                    'quantity' => 0,
                    'actual_price' => $dealOriginalPrice,
                    'deal_price' => $dealSpecialPrice,
                    'deal_start_date' => $dealSpecialFromDate,
                    'deal_end_date' => $dealSpecialToDate,
                    'status' => "Active" 
            );
            $collection->setData ( $data );
            $collection->save ();
        }
        $now = Mage::getModel ( 'core/date' )->timestamp ( time () );
        $now = date ( "Y-m-d", $now );
        $model = Mage::getModel ( 'superdeals/dealstatistics' )->getCollection ()->addFieldToFilter ( 'deal_end_date', array (
                'lteq' => $now 
        ) );
        /**
         * check condition if model is not empty
         */
        if (! empty ( $model )) {
            $connections = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
            $connections->beginTransaction ();
            $fields = array ();
            $fields ['status'] = 'Inactive';
            $where = $connections->quoteInto ( 'deal_end_date<=?', $now );
            $connections->update ( $statisticsTable, $fields, $where );
            $connections->commit ();
        }
    }
    /**
     * Function to get check status
     */
    public function getCheckStatus($customArray) {
        /**
         * Getting Customer Details
         */
        $productName = $customArray ['productName'];
        $status = $customArray ['status'];
        $smallComplete = $customArray ['smallComplete'];
        $item = $customArray ['item'];
        $orderNoWithEqual = $customArray ['orderNoWithEqual'];
        $Complete = $customArray ['complete'];
        $ordrId = $customArray ['ordrId'];
        $orderTable = $customArray ['orderTable'];
        $order_id = $customArray ['order_id'];
        $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
        $connection->beginTransaction ();
        $dup = Mage::getModel ( 'superdeals/dealz' )->getCollection ()->addFieldToFilter ( 'duplicate', 1 )->addFieldToFilter ( 'order_no', $ordrId );
        $dupp = count ( $dup );
        $customUpdateArray = array (
                'status' => $status,
                'smallComplete' => $smallComplete,
                'dupp' => $dupp,
                'item' => $item,
                'complete' => $Complete,
                'connection' => $connection,
                'orderNoWithEqual' => $orderNoWithEqual,
                'ordrId' => $ordrId,
                'orderTable' => $orderTable,
                'order_id' => $order_id 
        );
        $this->updateProductStatus ( $customUpdateArray );
        /**
         * get table prefix
         */
        $tPrefix = ( string ) Mage::getConfig ()->getTablePrefix ();
        $statisticsTable = $tPrefix . 'superdeals_reports';
        /**
         * get db connection
         */
        $connection = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_read' );
        
        $select = $connection->select ()->from ( $statisticsTable, array (
                'deal_id' 
        ) )->where ( 'deal_id=?', str_replace ( "'", "\'", $productName ) );
        
        $productArray = $connection->fetchRow ( $select );
        return $productArray ['deal_id'];
    }
}