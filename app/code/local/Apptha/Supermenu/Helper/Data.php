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
 * Super Menu extension Helper file
 */
class Apptha_Supermenu_Helper_Data extends Mage_Core_Helper_Abstract {
	
	/**
	 * Function to display header menu for web
	 *
	 * This Function will return Header menu display hierarchy for web
	 *
	 * @return string
	 */
	public function getSuperMenuData() {
		$menuData = array ();
		$blockObjClassName = Mage::getConfig ()->getBlockClassName ( 'supermenu/navigation' );
		/**
		 * Creating block object 
		 * Check the object is categories
		 */
		$blockObj = new $blockObjClassName ();
		$categories = $blockObj->getStoreCategories ();
		if (is_object ( $categories )) {
			$categories = $blockObj->getStoreCategories ()->getNodes ();
		}
		if (Mage::getStoreConfig ( 'supermenu/general/ajax_load_content' )) {
			$menuAjaxUrl = str_replace ( 'http:', '', Mage::getUrl ( 'supermenu/supermenuajax' ) );
		} else {
			$menuAjaxUrl = '';
		}
		/**
		 * Assign the values for $menuData array variable 
		 */
		$menuData ['_block'] = $blockObj;
		$menuData ['_categories'] = $categories;
		$menuData ['_menuAjaxUrl'] = $menuAjaxUrl;
		$menuData ['_showHomeLink'] = Mage::getStoreConfig ( 'supermenu/general/show_home_link' );
		$menuData ['_popupWidth'] = Mage::getStoreConfig ( 'supermenu/popup/width' );
		$menuData ['_popupTopOffset'] = Mage::getStoreConfig ( 'supermenu/popup/top_offset' );
		$menuData ['_popupDelayBeforeDisplaying'] = Mage::getStoreConfig ( 'supermenu/popup/delay_displaying' );
		$menuData ['_popupDelayBeforeHiding'] = Mage::getStoreConfig ( 'supermenu/popup/delay_hiding' );
		$menuData ['_rtl'] = 0;
		/**
		 * Return the $menuData array
		 */
		return $menuData;
	}
	
	/**
	 * Function to display header top menu label
	 *
	 * This Function will return Header top menu
	 *
	 * @return string
	 */
	public function getSuperMenuContent() {
		
		$topMenuContent = '';
		/**
		 * Get Menu Data
		 */
		$menuData = Mage::helper ( 'supermenu' )->getSuperMenuData ();
		extract ( $menuData );
		/**
		 * Assign the home menu link
		 */
		$homeLinkUrl = Mage::getBaseUrl ( Mage_Core_Model_Store::URL_TYPE_WEB );
		$homeLinkText = $this->__ ( 'Home' );
		$homeLink = '';
		if ($_showHomeLink) {
			$homeLink = '<div class="category_menu"><ul><li class="menu"><div class="parentMenu menu0"><a href="' . $homeLinkUrl . '"><span>' . $homeLinkText . '</span></a></div></li>';
		}
		/**
		 * Menu Content
		 */
		$popupSuperMenuContent = '';
		foreach ( $_categories as $_category ) {
			$_block->drawSupermenuItem ( $_category );
		}
		/**
		 * Top menu content.
		 */
		$topMenuArray = $_block->getTopMenuArray ();
		if (count ( $topMenuArray )) {
			$topMenuContent = implode ( "\n", $topMenuArray );
		}
		/**
		 * Super menu content.
		 */
		$popupMenuArray = $_block->getPopupMenuArray ();
		if (count ( $popupMenuArray )) {
			$popupSuperMenuContent = implode ( "\n", $popupMenuArray );
		}
		/**
		 * Result
		 * Return boolean value
		 */
		$topMenu = '
' . $homeLink . '
' . $topMenuContent . '
<div class="clearBoth"></div>';
		return array (
				'topMenu' => $topMenu,
				'popupMenu' => $popupSuperMenuContent 
		);
	}
	
	/**
	 * Checking for mobile device or not
	 */
	public function isMobile() {
		$regex_match = "/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|" . "htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|" . "blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|" . "symbian|smartphone|mmp|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|" . "jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220" . ")/i";
		/**
		 * Check the mobile model name match with current server
		 * return boolean value
		 */
		if (preg_match ( $regex_match, strtolower ( $_SERVER ['HTTP_USER_AGENT'] ) )) {
			return TRUE;
		}
		/**
		 * Check the server profile
		 * return boolean value
		 */
		if ((strpos ( strtolower ( $_SERVER ['HTTP_ACCEPT'] ), 'application/vnd.wap.xhtml+xml' ) > 0) or ((isset ( $_SERVER ['HTTP_X_WAP_PROFILE'] ) or isset ( $_SERVER ['HTTP_PROFILE'] )))) {
			return TRUE;
		}
		/**
		 * Assign the string from server user agent 
		 */
		$mobile_ua = strtolower ( substr ( $_SERVER ['HTTP_USER_AGENT'], 0, 4 ) );
		/**
		 * $mobile_agents - array variable
		 * Create array which is holds an mobile agents
		 * 
		 */
		$mobile_agents = array (
				'w3c ',
				'acs-',
				'alav',
				'alca',
				'amoi',
				'audi',
				'avan',
				'benq',
				'bird',
				'blac',
				'blaz',
				'brew',
				'cell',
				'cldc',
				'cmd-',
				'dang',
				'doco',
				'eric',
				'hipt',
				'inno',
				'ipaq',
				'java',
				'jigs',
				'kddi',
				'keji',
				'leno',
				'lg-c',
				'lg-d',
				'lg-g',
				'lge-',
				'maui',
				'maxo',
				'midp',
				'mits',
				'mmef',
				'mobi',
				'mot-',
				'moto',
				'mwbp',
				'nec-',
				'newt',
				'noki',
				'oper',
				'palm',
				'pana',
				'pant',
				'phil',
				'play',
				'port',
				'prox',
				'qwap',
				'sage',
				'sams',
				'sany',
				'sch-',
				'sec-',
				'send',
				'seri',
				'sgh-',
				'shar',
				'sie-',
				'siem',
				'smal',
				'smar',
				'sony',
				'sph-',
				'symb',
				't-mo',
				'teli',
				'tim-',
				'tosh',
				'tsm-',
				'upg1',
				'upsi',
				'vk-v',
				'voda',
				'wap-',
				'wapa',
				'wapi',
				'wapp',
				'wapr',
				'webc',
				'winw',
				'winw',
				'xda ',
				'xda-' 
		);
		/**
		 * Check array with mobiel agents.
		 * Return boolean value
		 */
		if (in_array ( $mobile_ua, $mobile_agents )) {
			return TRUE;
		}
		/**
		 *  Check the browser details 
		 */
		if (isset ( $_SERVER ['ALL_HTTP'] ) && strpos ( strtolower ( $_SERVER ['ALL_HTTP'] ), 'OperaMini' ) > 0) {
		    /**
		     * Return boolean value
		     */
			return TRUE;
		}
		/**
		 * Return boolean value
		 */
		return FALSE;
	}
}
