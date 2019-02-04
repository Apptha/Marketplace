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
?>
<?php
/**
 * This class contains form tab action
 */
class Apptha_Bannerslider_Block_Adminhtml_Bannerslider_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs{

/**
 * Set form id and title
 */
  public function __construct(){
      parent::__construct();
      $this->setId('bannerslider_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('bannerslider')->__('Banner Information'));
  }

  /**
   * Set layout block
   */
  protected function _beforeToHtml(){
      $this->addTab('form_section', array(
          'label'     => Mage::helper('bannerslider')->__('Banner Information'),
          'title'     => Mage::helper('bannerslider')->__('Banner Information'),
          'content'   => $this->getLayout()->createBlock('bannerslider/adminhtml_bannerslider_edit_tab_form')->toHtml(),
      ));  
     
      return parent::_beforeToHtml();
  }
}