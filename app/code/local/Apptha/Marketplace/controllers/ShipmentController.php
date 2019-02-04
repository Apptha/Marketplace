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
class Apptha_Marketplace_ShipmentController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
    }

    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession() {
        /**
         * Return session value
         */
        return Mage::getSingleton ( 'core/session' );
    }
    /**
     * Initialize shipment items QTY
     */
    protected function _getItemQtys() {
        /**
         * Inilize shipment data
         */
        $data = $this->getRequest ()->getParam ( 'shipment' );
        /**
         * Prepare shipment qtys
         */
        if (isset ( $data ['items'] )) {
            $qtys = $data ['items'];
        } else {
            $qtys = array ();
        }
        /**
         * Return shipment qty
         */
        return $qtys;
    }

    /**
     * Initialize shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment bool
     */
    protected function _initShipment() {
        $shipment = false;
        /**
         * Get params value.
         */
        $shipmentId = $this->getRequest ()->getParam ( 'shipment_id' );
        /**
         * Get order id
         * @var int
         */
        $orderId = $this->getRequest ()->getParam ( 'order_id' );
        if ($shipmentId) {
        /**
         * load shipment details using shipment id
         * @var unknown
         */
            $shipment = Mage::getModel ( 'sales/order_shipment' )->load ( $shipmentId );
        } elseif ($orderId) {
            $order = Mage::getModel ( 'sales/order' )->load ( $orderId );

            /**
             * Check order existing
             */
            if (! $order->getId ()) {
                $this->_getSession ()->addError ( $this->__ ( 'The order no longer exists.' ) );
                return false;
            }

            $savedQtys = $this->_getItemQtys ();

            $shipment = Mage::getModel ( 'sales/service_order', $order )->prepareShipment ( $savedQtys );

            $tracks = $this->getRequest ()->getPost ( 'tracking' );
            if ($tracks) {
                /**
                 * Get tacking details.
                 *
                 * If track number is empty throw error message.
                 */
                foreach ( $tracks as $data ) {
                /**
                 * Check whether tracking number empty or not
                 */
                    if (empty ( $data ['number'] )) {
                        Mage::throwException ( $this->__ ( 'Tracking number cannot be empty.' ) );
                    }
                    $track = Mage::getModel ( 'sales/order_shipment_track' )->addData ( $data );
                    $shipment->addTrack ( $track );
                }
            }
        }
        Mage::register ( 'current_shipment', $shipment );
        return $shipment;
    }
    /**
     * Save shipment
     * We can save only new shipment.
     * Existing shipments are not editable
     *
     * @return null
     */
    public function savePostAction() {
        $isNeedCreateLabel = '';
        try {
            $updateOrderId = $this->getRequest ()->getParam ( 'order_id' );
            /**
             * Get marketplace commission details
             */
            $collection = Mage::getModel ( 'marketplace/commission' )->getCollection ()->addFieldToFilter ( 'seller_id', Mage::getSingleton ( 'customer/session' )->getId () )->addFieldToFilter ( 'order_id', $updateOrderId )->getFirstItem ();
            /**
             * Checking manage order enable for seller
             */
            $orderStatusFlag = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/order_manage' );
            /**
             * Checking for seller order management enable or not
             */
            if (count ( $collection ) <= 0 || $orderStatusFlag != 1) {
                $this->_getSession ()->addError ( $this->__ ( 'You do not have permission to access this page' ) );
                $this->_redirect ( 'marketplace/order/vieworder/orderid/' . $updateOrderId );
                return;
            }
            /**
             * Get shipment info
             */
            $data = $this->getRequest ()->getParam ( 'shipment' );
            $shipment = $this->_initShipment ();
            if (! $shipment) {
                $this->_forward ( 'noRoute' );
                return;
            }
            /**
             * Register shipment
             */
            $shipment->register ();
            $comment = '';
            if (! empty ( $data ['comment_text'] )) {
                $shipment->addComment ( $data ['comment_text'], isset ( $data ['comment_customer_notify'] ), isset ( $data ['is_visible_on_front'] ) );
                if (isset ( $data ['comment_customer_notify'] )) {
                    $comment = $data ['comment_text'];
                }
            }
            $shipment->setEmailSent ( true );
            /**
             * Set customer notify.
             */
            $shipment->getOrder ()->setCustomerNoteNotify ( true );
            $responseAjax = new Varien_Object ();
            $isNeedCreateLabel = isset ( $data ['create_shipping_label'] ) && $data ['create_shipping_label'];
            if ($isNeedCreateLabel && $this->_createShippingLabel ( $shipment )) {
                $responseAjax->setOk ( true );
            }
            /**
             * Save shipment
             */
            $this->_saveShipment ( $shipment );

            /**
             * Send shipment email
             */
            $shipment->sendEmail ( true, $comment );
            /**
             * Initilize shipment label
             */
            $shipmentCreatedMessage = $this->__ ( 'The shipment has been created.' );
            $labelCreatedMessage = $this->__ ( 'The shipping label has been created.' );
            $savedQtys = $this->_getItemQtys ();
            Mage::getModel ( 'marketplace/order' )->updateSellerOrderItemsBasedOnSellerItems ( $savedQtys, $updateOrderId, 0 );
            $this->_getSession ()->addSuccess ( $isNeedCreateLabel ? $shipmentCreatedMessage . ' ' . $labelCreatedMessage : $shipmentCreatedMessage );
        } catch ( Mage_Core_Exception $e ) {
            /**
             * Thorugh the exception error message.
             */
            if ($isNeedCreateLabel) {
                $responseAjax->setError ( true );
                $responseAjax->setMessage ( $e->getMessage () );
            } else {
                /**
                 * When error occured, redirect to the view order page.
                 */
                $this->_getSession ()->addError ( $e->getMessage () );
                $this->_redirect ( 'marketplace/order/vieworder/orderid/' . $updateOrderId );
            }
        } catch ( Exception $e ) {
            Mage::logException ( $e );
            if ($isNeedCreateLabel) {
                $responseAjax->setError ( true );
                $responseAjax->setMessage ( /**
                 * Through error when create shipping label.
                 */
                Mage::helper ( 'sales' )->__ ( 'An error occurred while creating shipping label.' ) );
            } else {
                $this->_getSession ()->addError ( $this->__ ( 'Cannot save shipment.' ) );
                $this->_redirect ( 'marketplace/order/vieworder/orderid/' . $updateOrderId );
            }
        }
        if ($isNeedCreateLabel) {
            $this->getResponse ()->setBody ( $responseAjax->toJson () );
        } else {
            /**
             * Redirect to the view order page.
             */
            $this->_redirect ( 'marketplace/order/vieworder/orderid/' . $updateOrderId );
        }
    }
    /**
     * Save shipment and order in one transaction
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return Mage_Adminhtml_Sales_Order_ShipmentController
     */
    protected function _saveShipment($shipment) {
        /**
         * set is in product status
         */
        $shipment->getOrder ()->setIsInProcess ( true );
        /**
         * Save shipment for order
         */
        Mage::getModel ( 'core/resource_transaction' )->addObject ( $shipment )->addObject ( $shipment->getOrder () )->save ();
        return $this;
    }

    /**
     * Create shipping label for specific shipment with validation.
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     * @return bool
     */
    protected function _createShippingLabel(Mage_Sales_Model_Order_Shipment $shipment) {
/**
 * Checing for shipment
 */
$returnFlag = 0;
if (! $shipment) {
$returnFlag = 1;
} else {
/**
 * Inilize carrier for shipment
 */
$carrier = $shipment->getOrder ()->getShippingCarrier ();
if (! $carrier->isShippingLabelsAvailable ()) {
$returnFlag = 1;
}
}
if ($returnFlag == 1) {
return false;
}
/**
 * Set shippment package.
 */
$shipment->setPackages ( $this->getRequest ()->getParam ( 'packages' ) );
$response = Mage::getModel ( 'shipping/shipping' )->requestToShipment ( $shipment );
if ($response->hasErrors ()) {
Mage::throwException ( $response->getErrors () );
}
/**
 * Checking for response
 */
if (! $response->hasInfo ()) {
return false;
}
$labelsContent = array ();
$trackingNumbers = array ();
/**
 * Get tracking information.
 */
$info = $response->getInfo ();
/**
 * Increment foreach loop
 */
foreach ( $info as $inf ) {
/**
 * Check whether tracking number empty or not
 */
if (! empty ( $inf ['tracking_number'] ) && ! empty ( $inf ['label_content'] )) {
$labelsContent [] = $inf ['label_content'];
$trackingNumbers [] = $inf ['tracking_number'];
}
}
$outputPdf = $this->_combineLabelsPdf ( $labelsContent );
$shipment->setShippingLabel ( $outputPdf->render () );
$carrierCode = $carrier->getCarrierCode ();
$carrierTitle = Mage::getStoreConfig ( 'carriers/' . $carrierCode . '/title', $shipment->getStoreId () );
/**
 * Checking for tracking numbers
 */
if ($trackingNumbers) {
/**
 * Increment foreach loop
 */
foreach ( $trackingNumbers as $trackingNumber ) {
$track = Mage::getModel ( 'sales/order_shipment_track' )->setNumber ( $trackingNumber )->setCarrierCode ( $carrierCode )->setTitle ( $carrierTitle );
/**
 * Adding trackings
 */
$shipment->addTrack ( $track );
}
}
return true;
}
    /**
     * Create invoice for cod and checkmo payment method
     */
    public function invoiceAction() {
        $this->chekcingForMarketplaceSellerOrNot ();
        /**
         * Get order Id
         * @var unknown
         */
        $orderId = $this->getRequest ()->getParam ( 'id' );
        /**
         * Getting order product ids
         */
        $orderPrdouctIds = Mage::helper ( 'marketplace/vieworder' )->getOrderProductIds ( Mage::getSingleton ( 'customer/session' )->getId (), $orderId );
        /**
         * Getting cancel order items
         */
        $cancelOrderItemProductIds = Mage::helper ( 'marketplace/vieworder' )->cancelOrderItemProductIds ( Mage::getSingleton ( 'customer/session' )->getId (), $orderId );

        /**
         * Getting the sonfiguration for order manage.
         */
        $orderStatusFlag = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/order_manage' );
        /**
         * Check whether product id count is greater than one
         */
        if (count ( $orderPrdouctIds ) >= 1 && $orderStatusFlag == 1) {
            $order = Mage::getModel ( 'sales/order' )->load ( $orderId );
            $itemsarray = $itemsArr = array ();
            /**
             * prepare invoice items
             */
            foreach ( $order->getAllItems () as $item ) {
                $qty = 0;
                /**
                 * Prepare invoice qtys
                 */
                $itemProductId = $item->getProductId ();
                $itemId = $item->getItemId ();
                /**
                 * check whether item is in array
                 */
                if (in_array ( $itemProductId, $orderPrdouctIds ) && ! in_array ( $itemProductId, $cancelOrderItemProductIds )) {
                    $itemsArr [] = $itemId;
                    /**
                     * Qty ordered for that item
                     */
                    $qty = $item->getQtyOrdered () - $item->getQtyInvoiced ();
                }
                $itemsarray [$itemId] = $qty;
            }

            try {
                /**
                 * Create invoice
                 */
                if ($order->canInvoice ()) {
                    /**
                     * Generate invoice for shippment.
                     */
                    Mage::getModel ( 'sales/order_invoice_api' )->create ( $order->getIncrementId (), $itemsarray, '', 1, 1 );
                    Mage::getModel ( 'marketplace/order' )->updateSellerOrderItemsBasedOnSellerItems ( $itemsArr, $orderId, 1 );
                    /**
                     * add success message
                     */
                    Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'The invoice has been created.' ) );
                }
                $this->_redirect ( 'marketplace/order/vieworder/orderid/' . $orderId );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                $this->_redirect ( 'marketplace/order/vieworder/orderid/' . $orderId );
            }
        } else {
            /**
             * Checkk the permission for generate invoice.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You do not have permission to access this page' ) );
            $this->_redirect ( 'marketplace/order/manage' );
            return false;
        }
    }
    /**
     * Create credit memo for cod and checkmo payment method
     */
    public function refundAction() {
        $this->chekcingForMarketplaceSellerOrNot ();
        /**
         * Initilize refund variables
         */
        $orderId = $this->getRequest ()->getParam ( 'id' );
        $produtId = $this->getRequest ()->getParam ( 'item' );
        $sellerId = Mage::getSingleton ( 'customer/session' )->getId ();
        /**
         * Collect the produts from the order details.
         */
        $orderPrdouctIds = Mage::helper ( 'marketplace/vieworder' )->getOrderProductIds ( Mage::getSingleton ( 'customer/session' )->getId (), $orderId );

        $orderStatusFlag = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/order_manage' );
        /**
         * Check whether product id is in array
         */
        if (in_array ( $produtId, $orderPrdouctIds ) && $orderStatusFlag == 1) {

            Mage::getModel ( 'marketplace/order' )->updateSellerRequest ( $produtId, $orderId, Mage::getSingleton ( 'customer/session' )->getId (), '', 2 );

            try {

                /**
                 * Sending order email
                 */
                $templateId = ( int ) Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/order_cancel_request_notification_template_selection' );
                $adminEmailId = Mage::getStoreConfig ( 'marketplace/marketplace/admin_email_id' );
                $toMailId = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/email" );
                $toName = Mage::getStoreConfig ( "trans_email/ident_$adminEmailId/name" );
                /**
                 * Select email template
                 */
                if ($templateId) {
                    $emailTemplate = Mage::helper ( 'marketplace/marketplace' )->loadEmailTemplate ( $templateId );
                } else {
                    $emailTemplate = Mage::getModel ( 'core/email_template' )->loadDefault ( 'marketplace_cancel_order_admin_email_template_selection' );
                }
                /**
                 * Load product collection for cancel
                 */
                $productCollection = Mage::getModel ( 'catalog/product' )->getCollection ()->addAttributeToSelect ( '*' )->addUrlRewrite ()->addAttributeToFilter ( 'entity_id', array (
                        'eq' => $produtId
                ) );

                $productDetails = "<ul>";
                /**
                 * Prepare product details for cancel email
                 */
                foreach ( $productCollection as $product ) {
                    $productDetails .= "<li>";
                    $productDetails .= "<div><a href='{$product->getProductUrl()}'>{$product->getName()}</a><div>";
                    $productDetails .= "</li>";
                }

                $productDetails .= "</ul>";
                /**
                 * load order details
                 * @var object
                 */
                $incrementId = Mage::getModel ( 'sales/order' )->load ( $orderId )->getIncrementId ();

                /**
                 * Get seller information.
                 */
                $customer = Mage::getModel ( 'customer/customer' )->load ( $sellerId );
                /**
                 * Initilize variable for send mail to seller
                 */
                $sellerEmail = $customer->getEmail ();
                $sellerName = $customer->getName ();
                $recipient = $toMailId;
                $emailTemplate->setSenderName ( $sellerName );
                $emailTemplate->setSenderEmail ( $sellerEmail );

                /**
                 * Prepare temail templave variables
                 */
                $emailTemplateVariables = array (
                        'ownername' => $toName,
                        'productdetails' => $productDetails,
                        'order_id' => $incrementId,
                        'customer_email' => $sellerEmail,
                        'customer_firstname' => $sellerName,
                        'reason' => $this->__ ( 'Buyer wants to refund the item' ),
                        'requesttype' => $this->__ ( 'refund' ),
                        'requestperson' => $this->__ ( 'Seller' )
                );
                $emailTemplate->setDesignConfig ( array (
                        'area' => 'frontend'
                ) );
                /**
                 * Sending email to admin
                 */
                $emailTemplate->getProcessedTemplate ( $emailTemplateVariables );
                $emailTemplate->send ( $recipient, $toName, $emailTemplateVariables );

                /**
                 * Redirect to order view page
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( "The item refund request has been sent." ) );
                $this->_redirect ( 'marketplace/order/vieworder/orderid/' . $orderId );
            } catch ( Exception $e ) {
                /**
                 * when error message occured, redirected to view order page.
                 */
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                $this->_redirect ( 'marketplace/order/vieworder/orderid/' . $orderId );
            }
        } else {
            /**
             * Check the permission.
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You do not have permission to access this page' ) );
            $this->_redirect ( 'marketplace/order/manage' );
            return false;
        }
    }
     /**
     * Create credit memo for cod and checkmo payment method
     */
    public function cancelAction() {
        $this->chekcingForMarketplaceSellerOrNot ();
        $orderId = $this->getRequest ()->getParam ( 'id' );
        $produtId = $this->getRequest ()->getParam ( 'item' );
        $sellerId = Mage::getSingleton ( 'customer/session' )->getId ();
        /**
         * Prepare product collection for cancel
         */
        $products = Mage::getModel ( 'marketplace/commission' )->getCollection ();
        $products->addFieldToSelect ( '*' );
        $products->addFieldToFilter ( 'seller_id', $sellerId );
        $products->addFieldToFilter ( 'order_id', $orderId );
        $products->addFieldToFilter ( 'product_id', $produtId );
       $collectionId = $products->getFirstItem ()->getId ();
        $orderStatusFlag = Mage::getStoreConfig ( 'marketplace/admin_approval_seller_registration/order_manage' );
        if (! empty ( $collectionId ) && $orderStatusFlag == 1) {
            try {
                $data = array (
                        'order_status' => 'canceled',
                        'customer_id' => 0,
                        'credited' => 1,
                        'item_order_status' => 'canceled'
                );
                $commissionModel = Mage::getModel ( 'marketplace/commission' )->load ( $collectionId )->addData ( $data );
                $commissionModel->setId ( $collectionId )->save ();

                /**
                 * Load order details based on the order id.
                 */
                $_order = Mage::getModel ( 'sales/order' )->load ( $orderId );
                /**
                 * Update order items to cancel status
                 */
                foreach ( $_order->getAllItems () as $item ) {
                    if ($this->getRequest ()->getParam ( 'item' ) == $item->getId ()) {
                        $item->cancel ();
                    }
                }
                /**
                 * Send cancel notification for admin
                 */
                Mage::helper('marketplace/general')->sendCancelOrderAdminNotification($orderId,$produtId,$sellerId);

                Mage::helper('marketplace/general')->sendCancelOrderBuyerNotification($_order);
                /**
                 * Redirect to order view page
                 */
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( $this->__ ( 'The item has been cancelled.' ) ) );
                $this->_redirect ( 'marketplace/order/vieworder/orderid/' . $orderId );
            } catch ( Exception $e ) {
                Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( $e->getMessage () ) );
                $this->_redirect ( 'marketplace/order/vieworder/orderid/' . $orderId );
            }
        } else {
            /**
             * Return to order manage page
             */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You do not have permission to access this page' ) );
            $this->_redirect ( 'marketplace/order/manage' );
            return false;
        }
    }
     /**
     * Checking for marketplace seller or not
     */
    public function chekcingForMarketplaceSellerOrNot() {
        /**
         * Initilize customer and seller group id
         */
        $customerGroupIdVal = $sellerGroupIdVal = $customerStatusValue = '';
        /**
         * Get customer group id
         * @var int
         */
        $customerGroupIdVal = Mage::getSingleton ( 'customer/session' )->getCustomerGroupId ();
        $sellerGroupIdVal = Mage::helper ( 'marketplace' )->getGroupId ();
        /**
         * Get customer status
         * @var int
         */
        $customerStatusValue = Mage::getSingleton ( 'customer/session' )->getCustomer ()->getCustomerstatus ();
        /**
         * Checking for customer group id and seller group id
         */
        if (! Mage::getSingleton ( 'customer/session' )->isLoggedIn () && $customerGroupIdVal != $sellerGroupIdVal) {
            /**
             * add error message
             */
        Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'You must have a Seller Account to access this page' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return false;
        }
        /**
         * Checking whether customer approved or not
         */
        if ($customerStatusValue != 1) {
        /**
         * add error message
         */
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Admin Approval is required. Please wait until admin confirms your Seller Account' ) );
            $this->_redirect ( 'marketplace/seller/login' );
            return false;
        }
    }
}