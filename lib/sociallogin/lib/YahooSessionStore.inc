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
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 * 
 */


/**
 * The session store interface. Developers are free to implement their
 * own session store implementations and pass them to YahooSession::hasSession,
 * YahooSession::requireSession and YahooSession::clearSession.
 *
 * @brief The session store interface.
 */
interface YahooSessionStore {
    /**
     * Indicates if the session store has a request token.
     *
     * @return True if a request token is present, false otherwise.
     */
    function hasRequestToken();

    /**
     * Indicates if the session store has an access token.
     *
     * @return True if an access token is present, false otherwise.
     */
    function hasAccessToken();

    /**
     * Stores the given request token in the session store.
     *
     * @param $token A PHP stdclass object containing the components of
     *               the OAuth request token.
     * @return True on success, false otherwise.
     */
    function storeRequestToken($token);

    /**
     * Fetches and returns the request token from the session store.
     *
     * @return The request token.
     */
    function fetchRequestToken();

    /**
     * Clears the request token from the session store.
     *
     * @return True on success, false otherwise.
     */
    function clearRequestToken();

    /**
     * Stores the given access token in the session store.
     *
     * @param $token A PHP stdclass object containing the components of
     *               the OAuth access token.
     * @return True on success, false otherwise.
     */
    function storeAccessToken($token);

    /**
     * Fetches and returns the access token from the session store.
     *
     * @return The access token.
     */
    function fetchAccessToken();

    /**
     * Clears the access token from the session store.
     *
     * @return True on success, false otherwise.
     */
    function clearAccessToken();
}
