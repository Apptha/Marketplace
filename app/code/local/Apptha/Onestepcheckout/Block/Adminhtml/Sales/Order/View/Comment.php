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
 * Sales Order comment management
 * This class used for managing the comments which have been posted for sales order
 */
class Apptha_Onestepcheckout_Block_Adminhtml_Sales_Order_View_Comment extends Mage_Adminhtml_Block_Sales_Order_View_Items {
    /**
     * get html value function
     */
    public function _toHtml() {
        $html = parent::_toHtml ();
        $comment = $this->getCommentHtml ();
        return $html . $comment;
    }
    
    /**
     * Get comment from order and return as html formatted string
     *
     * @return string
     */
    public function getCommentHtml() {
        $comment = $this->getOrder ()->getOnestepcheckoutCustomercomment ();
        $feedback = $this->getOrder ()->getOnestepcheckoutCustomerfeedback ();
        $html = '';
        /**
         * check condition comment enable is not equal empty and comment
         */
        if ($this->isShowCustomerCommentEnabled () && $comment) {
            $html .= '<div id="customer_comment" class="giftmessage-whole-order-container"><div class="entry-edit">';
            $html .= '<div class="entry-edit-head"><h4>' . $this->helper ( 'onestepcheckout' )->__ ( 'Customer Comment' ) . '</h4></div>';
            $html .= '<fieldset>' . nl2br ( $this->helper ( 'onestepcheckout' )->htmlEscape ( $comment ) ) . '</fieldset>';
            $html .= '</div></div>';
        }
        /**
         * check condition customer feedback enable is not equal to empty
         */
        if ($this->isShowCustomerFeedbackEnabled () || $this->isShowCustomerFeedbackTextEnabled ()) {
            $html .= '<div id="customer_feedback" class="giftmessage-whole-order-container"><div class="entry-edit">';
            $html .= '<div class="entry-edit-head"><h4>' . $this->helper ( 'onestepcheckout' )->__ ( 'How did you hear about us' ) . '</h4></div>';
            $html .= '<fieldset>' . nl2br ( Mage::helper ( 'core' )->escapeHtml ( $feedback ) ) . '</fieldset>';
            $html .= '</div></div>';
        }
        return $html;
    }
    /**
     * Check customer comment option is enabled or not
     *
     * @return int
     */
    public function isShowCustomerCommentEnabled() {
        return Mage::getStoreConfig ( 'onestepcheckout/display_option/display_comments', $this->getOrder ()->getStore () );
    }
    /**
     * Check customer feedback option is enabled or not
     *
     * @return int
     */
    public function isShowCustomerFeedbackEnabled() {
        return Mage::getStoreConfig ( 'onestepcheckout/feedback/enable_feedback', $this->getOrder ()->getStore () );
    }
    /**
     * Check customer feedback text option is enabled or not
     *
     * @return int
     */
    public function isShowCustomerFeedbackTextEnabled() {
        return Mage::getStoreConfig ( 'onestepcheckout/feedback/enable_feedback_freetext', $this->getOrder ()->getStore () );
    }
}


