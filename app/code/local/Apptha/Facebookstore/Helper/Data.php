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
 * @package     Apptha_FacebookStore
 * @version    	0.1.1
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 * */
?>
<?php
class Apptha_Facebookstore_Helper_Data extends Mage_Core_Helper_Abstract{
      /**
	 * Function to get domain key
	 * @param unknown $tkey
	 */
	public function domainKey($tkey) {
		$message = "EM-MFSMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
		
		for($i = 0; $i < strlen ( $tkey ); $i ++) {
			$key_array [] = $tkey [$i];
		}
		$enc_message = "";
		$kPos = 0;
		$chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
		for($i = 0; $i < strlen ( $chars_str ); $i ++) {
			$chars_array [] = $chars_str [$i];
		}
		for($i = 0; $i < strlen ( $message ); $i ++) {
			$char = substr ( $message, $i, 1 );
			
			$offset = $this->getOffset ( $key_array [$kPos], $char );
			$enc_message .= $chars_array [$offset];
			$kPos ++;
			if ($kPos >= count ( $key_array )) {
				$kPos = 0;
			}
		}
		
		return $enc_message;
	}
	/**
	 * Function to get license
	 */
	public function license() {
		return 'license';
	}
	/**
	 * Function to get Offset
	 * @param unknown $start
	 * @param unknown $end
	 */
	public function getOffset($start, $end) {
		$chars_str = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
		for($i = 0; $i < strlen ( $chars_str ); $i ++) {
			$chars_array [] = $chars_str [$i];
		}
		
		for($i = count ( $chars_array ) - 1; $i >= 0; $i --) {
			$lookupObj [ord ( $chars_array [$i] )] = $i;
		}
		
		$sNum = $lookupObj [ord ( $start )];
		$eNum = $lookupObj [ord ( $end )];
		
		$offset = $eNum - $sNum;
		
		if ($offset < 0) {
			$offset = count ( $chars_array ) + ($offset);
		}
		
		return $offset;
	}
	/**
	 * Function to generate osc domain
	 * @return string
	 */
	public function genenrateOscdomain() {
		$strDomainName = Mage::getBaseUrl ();
		preg_match ( "/^(http:\/\/)?([^\/]+)/i", $strDomainName, $subfolder );
		preg_match ( "/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $subfolder [2], $matches );
		if (isset ( $matches ['domain'] )) {
			$customerurl = $matches ['domain'];
		} else {
			$customerurl = "";
		}
		$customerurl = str_replace ( "www.", "", $customerurl );
		$customerurl = str_replace ( ".", "D", $customerurl );
		$customerurl = strtoupper ( $customerurl );
		if (isset ( $matches ['domain'] )) {
			$response = $this->domainKey ( $customerurl );
		} else {
			$response = "";
		}
		return $response;
	}
}