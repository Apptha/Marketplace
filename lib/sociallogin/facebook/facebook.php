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

require_once "base_facebook.php";

/**
 * Extends the BaseFacebook class with the intent of using
 * PHP sessions to store user ids and access tokens.
 */
class Slogin_Facebook extends BaseFacebook
{
  const FBSS_COOKIE_NAME = 'fbss';

  // We can set this to a high number because the main session
  // expiration will trump this.
  const FBSS_COOKIE_EXPIRE = 31556926; // 1 year

  // Stores the shared session ID if one is set.
  protected $sharedSessionID;

  /**
   * Identical to the parent constructor, except that
   * we start a PHP session to store the user ID and
   * access token if during the course of execution
   * we discover them.
   *
   * @param Array $config the application configuration. Additionally
   * accepts "sharedSession" as a boolean to turn on a secondary
   * cookie for environments with a shared session (that is, your app
   * shares the domain with other apps).
   * @see BaseFacebook::__construct in facebook.php
   */
  public function __construct($config) {
    if (!session_id()) {
      session_start();
    }
    parent::__construct($config);
    if (!empty($config['sharedSession'])) {
      $this->initSharedSession();
    }
  }

  protected static $kSupportedKeys =
    array('state', 'code', 'access_token', 'user_id');

  protected function initSharedSession() {
    $cookie_name = $this->getSharedSessionCookieName();
    if (isset($_COOKIE[$cookie_name])) {
      $data = $this->parseSignedRequest($_COOKIE[$cookie_name]);
      if ($data && !empty($data['domain']) &&
          self::isAllowedDomain($this->getHttpHost(), $data['domain'])) {
        // good case
        $this->sharedSessionID = $data['id'];
        return;
      }
      // ignoring potentially unreachable data
    }
    // evil/corrupt/missing case
    $base_domain = $this->getBaseDomain();
    $this->sharedSessionID = md5(uniqid(mt_rand(), true));
    $cookie_value = $this->makeSignedRequest(
      array(
        'domain' => $base_domain,
        'id' => $this->sharedSessionID,
      )
    );
    $_COOKIE[$cookie_name] = $cookie_value;
    if (!headers_sent()) {
      $expire = time() + self::FBSS_COOKIE_EXPIRE;
      setcookie($cookie_name, $cookie_value, $expire, '/', '.'.$base_domain);
    } else {
      // @codeCoverageIgnoreStart
      self::errorLog(
        'Shared session ID cookie could not be set! You must ensure you '.
        'create the Facebook instance before headers have been sent. This '.
        'will cause authentication issues after the first request.'
      );
      // @codeCoverageIgnoreEnd
    }
  }

  /**
   * Provides the implementations of the inherited abstract
   * methods.  The implementation uses PHP sessions to maintain
   * a store for authorization codes, user ids, CSRF states, and
   * access tokens.
   */
  protected function setPersistentData($key, $value) {
    if (!in_array($key, self::$kSupportedKeys)) {
      self::errorLog('Unsupported key passed to setPersistentData.');
      return;
    }

    $session_var_name = $this->constructSessionVariableName($key);
    $_SESSION[$session_var_name] = $value;
  }

  protected function getPersistentData($key, $default = false) {
    if (!in_array($key, self::$kSupportedKeys)) {
      self::errorLog('Unsupported key passed to getPersistentData.');
      return $default;
    }

    $session_var_name = $this->constructSessionVariableName($key);
    return isset($_SESSION[$session_var_name]) ?
      $_SESSION[$session_var_name] : $default;
  }

  protected function clearPersistentData($key) {
    if (!in_array($key, self::$kSupportedKeys)) {
      self::errorLog('Unsupported key passed to clearPersistentData.');
      return;
    }

    $session_var_name = $this->constructSessionVariableName($key);
    unset($_SESSION[$session_var_name]);
  }

  protected function clearAllPersistentData() {
    foreach (self::$kSupportedKeys as $key) {
      $this->clearPersistentData($key);
    }
    if ($this->sharedSessionID) {
      $this->deleteSharedSessionCookie();
    }
  }

  protected function deleteSharedSessionCookie() {
    $cookie_name = $this->getSharedSessionCookieName();
    unset($_COOKIE[$cookie_name]);
    $base_domain = $this->getBaseDomain();
    setcookie($cookie_name, '', 1, '/', '.'.$base_domain);
  }

  protected function getSharedSessionCookieName() {
    return self::FBSS_COOKIE_NAME . '_' . $this->getAppId();
  }

  protected function constructSessionVariableName($key) {
    $parts = array('fb', $this->getAppId(), $key);
    if ($this->sharedSessionID) {
      array_unshift($parts, $this->sharedSessionID);
    }
    return implode('_', $parts);
  }
}
