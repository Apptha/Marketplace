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
class Apptha_Marketplace_Helper_Subscription extends Mage_Core_Helper_Abstract {
    
    /**
     * Function to check th
     * 
     * @param unknown $sellerId            
     * @return number
     */
    public function checkSubscribed($sellerId) {
        $subscribed = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $sellerId, 'seller_id' );
        /**
         * check condition get subscribed id is not false
         */
        if ($subscribed->getId ()) {
        /**
         * Get Subscription paid date
         * @var unknown
         */
            $subscriptionStartDate = $subscribed->getPaidDate ();
            /**
             * Get plan id
             * @var unknown
             */
            $planId = $subscribed->getPlanId ();
            /**
             * Get status
             * @var text
             */
            $status = $subscribed->getStatus ();
            $adminApproval = $subscribed->getAdminApproval ();
            /**
             * Load subscription plan object using plan id
             * @var unknown
             */
            $getPlanInfo = Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId, 'plan_id' );
            /**
             * Get subscription period
             * @var id
             */
            $subscriptionPeriod = $getPlanInfo->getSubscriptionPeriod ();
            /**
             * Get validity period
             * @var text
             */
            $validityPeriod = $getPlanInfo->getValidityPeriod ();
            /**
             * check condition offer period is not equal to empty and validity period is not equal to empty
             */
            if ($getPlanInfo->getOfferPeriod () != '' && $getPlanInfo->getOfferValidityPeriod () != '') {
                $offerPeriod = $getPlanInfo->getOfferPeriod ();
                $offerValidityPeriod = $getPlanInfo->getOfferValidityPeriod ();
            }            
            $subscriptionEndDate = $this->getSubscriptionEndDate($subscriptionPeriod,$offerPeriod,$validityPeriod,$offerValidityPeriod,$subscriptionStartDate);                      
            $currentDate = Mage::getModel ( 'core/date' )->date ( "Y-m-d" );
            
            /**
             *
             * @return 1 plan active now
             *        
             * @return 2 plan has expired
             *        
             * @return 3 not approved by admin
             */
            
            if ($adminApproval == 1) {
                if (strtotime ( $currentDate ) < strtotime ( $subscriptionEndDate ) && ($status != '') && ($status != 'expire')) {
                    return 1;
                } else {
                    return 2;
                }
            } else {
                return 3;
            }
            /**
             * End if clause
             */
        }
    }
    /**
     * Get plan info from model file
     * 
     * @param unknown $sellerId            
     * @return Ambigous <Mage_Core_Model_Abstract, Mage_Core_Model_Abstract>
     */
    public function getPlanInfo($sellerId) {
    /**
     * load subscription info by seller id
     */
        return Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $sellerId, 'seller_id' );
    }
    
    /**
     * Get subscription plan details
     *
     * Passes plan id to get the date
     *
     * @param $planid Result
     *            a array of info
     * @return array
     */
    function getSubscriptionPlanInfo($planId) {
    /**
     * load plan details
     */
        return Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId );
    }
    /**
     * Function to check subscription for half period
     * 
     * @param unknown $sellerId            
     * @return number
     */
    public function checkSubscribtionHalfPeriod($sellerId) {
        $subscribed = Mage::getModel ( 'marketplace/subscribedinfo' )->load ( $sellerId, 'seller_id' );
        /**
         * check condition subscription id is not false
         */
        if ($subscribed->getId ()) {
            $subscriptionStartDate = $subscribed->getOldActiveDate ();
            $planId = $subscribed->getPlanId ();
            $status = $subscribed->getStatus ();
            $adminApproval = $subscribed->getAdminApproval ();
            $currentDate = Mage::getModel ( 'core/date' )->date ( "Y-m-d" );
            /**
             * check condition admin approval is equal to 1 and status is not equal to expire
             */
            if (($adminApproval == 1) && ($status != 'expire') && (strtotime ( $currentDate ) >= strtotime ( $subscriptionStartDate ))) {
                $getPlanInfo = Mage::getModel ( 'marketplace/subscriptionplans' )->load ( $planId, 'plan_id' );
                $subscriptionPeriod = $getPlanInfo->getSubscriptionPeriod ();
                $validityPeriod = $getPlanInfo->getValidityPeriod ();
                $subscripedEndDate = date ( "Y-m-d", strtotime ( "+ $validityPeriod month", strtotime ( $subscriptionStartDate ) ) );
                /**
                 * check condition subscription period is equal to 2
                 */
                if ($subscriptionPeriod == 2) {
                    $subscripedEndDate = date ( "Y-m-d", strtotime ( "+ $validityPeriod year", strtotime ( $subscriptionStartDate ) ) );
                }
                $differentDate = ceil ( abs ( strtotime ( $subscripedEndDate ) - strtotime ( $subscriptionStartDate ) ) / 86400 );
                $halfPeriod = round ( $differentDate / 2 );
                $currentPeriod = ceil ( abs ( strtotime ( $subscriptionStartDate ) - strtotime ( $currentDate ) ) / 86400 );
                /**
                 * check condition curren period is less than half period
                 */
                if ($currentPeriod < $halfPeriod) {
                    return 1;
                }
                /**
                 * check condition curren time equal to subscription start date
                 */
                if (strtotime ( $currentDate ) == strtotime ( $subscriptionStartDate )) {
                    return 1;
                }
            }
        }
    }
    
    public function getSubscriptionEndDate($subscriptionPeriod,$offerPeriod,$validityPeriod,$offerValidityPeriod,$subscriptionStartDate){
        $subscriptionEndDate = '';
        /**
        * check condition subscription period is equal to 1 and offer period also equal to 1
         */
        if ($subscriptionPeriod == 1 && $offerPeriod == 1) {
                    $subscriptionEndDate = date ( "Y-m-d", strtotime ( '+' . $validityPeriod + $offerValidityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
            }
        /**
         * check condition subsciption period is equal to 2 and offer period also equal to 2
         */
        if ($subscriptionPeriod == 2 && $offerPeriod == 2) {
        $subscriptionEndDate = date ( "Y-m-d", strtotime ( '+' . $validityPeriod + $offerValidityPeriod . " years ", strtotime ( $subscriptionStartDate ) ) );
        }
        /**
        * check condition suubscription period is equal to 1 and offer period is equal to 2
         */
        if ($subscriptionPeriod == 1 && $offerPeriod == 2) {
            $subscriptionEndDate = date ( "Y-m-d", strtotime ( '+' . $offerValidityPeriod . " years " . $validityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
        }
         
        /**
         * check condition subscription period is equal to 2 and offer period is equal to 1
         */
        if ($subscriptionPeriod == 2 && $offerPeriod == 1) {
            $subscriptionEndDate = date ( "Y-m-d", strtotime ( '+' . $validityPeriod . " years " . $offerValidityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
        }
        /**
         * check condition subscription period is equal to 2 and offer period is equal to 0
         */
        if ($subscriptionPeriod == 2 && $offerPeriod == 0) {
            $subscriptionEndDate = date ( "Y-m-d", strtotime ( '+' . $validityPeriod . " years ", strtotime ( $subscriptionStartDate ) ) );
        }
        /**
         * check condition subscription period is equal to 1 and offer period is equal to 0
         */
        if ($subscriptionPeriod == 1 && $offerPeriod == 0) {
            $subscriptionEndDate = date ( "Y-m-d", strtotime ( '+' . $validityPeriod . ' months', strtotime ( $subscriptionStartDate ) ) );
        }
         
        return $subscriptionEndDate;
    }
    
    /**
     * Get subcribed date
     * Passed seller id to get the seller customer registered date
     *
     * @param $sellerId Return
     *         subscribed date
     * @return date
     */
    public function subscribedDate($sellerId) {
    /**
     * get seller/designer subscribed date
     */
    $collection = Mage::getModel('marketplace/subscribedinfo')->load ( $sellerId, 'seller_id' );
    /**
     * Get paid date
     */
    if ($collection) {
    return date ( "jS F Y", strtotime ( $collection->getPaidDate () ) );
    }
    }
}