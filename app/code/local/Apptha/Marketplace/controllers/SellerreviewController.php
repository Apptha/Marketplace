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
 * This file contains seller review page functionality
 */
class Apptha_Marketplace_SellerreviewController extends Mage_Core_Controller_Front_Action {
    
    /**
     * Function to display all reviews in seller profile page
     *
     * Display seller review page
     *
     * @return void
     */
    function allreviewAction() {
        $this->loadLayout ();
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'All Review' ) );
        $this->renderLayout ();
    }
    /**
     * Function to display customer review to seller
     *
     * Display customer review page
     *
     * @return void
     */
    function customerreviewAction() {
       
        $this->loadLayout ();
        $this->getLayout ()->getBlock ( 'head' )->setTitle ( $this->__ ( 'Customer Review' ) );
        $this->renderLayout ();
    }
    /**
     * Function to save reviews and ratings
     *
     * Save seller review
     *
     * @return void
     */
    function savereviewAction() {
        $data = $this->getRequest ()->getPost ();
        $id = $data ['seller_id'];
        $url = Mage::getModel ( 'marketplace/sellerreview' )->backUrl ( $id );
        $saveReview = Mage::getModel ( 'marketplace/sellerreview' )->saveReview ( $data );
        if ($saveReview == 1) {
            $needAdmin = Mage::getStoreConfig ( 'marketplace/seller_review/need_approval' );
            if ($needAdmin == 1) {
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your review has been accepted for moderation.' ) );
            } else {
                Mage::getSingleton ( 'core/session' )->addSuccess ( $this->__ ( 'Your review has been successfully posted.' ) );
            }
            
            $this->_redirectUrl ( $url );
        } else {
            Mage::getSingleton ( 'core/session' )->addError ( $this->__ ( 'Sorry there was an error occured while submiting your review' ) );
            $this->_redirectUrl ( $url );
        }
    }

}