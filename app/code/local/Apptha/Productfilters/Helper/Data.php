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
 * @package     Apptha_Advancedfilters
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
class Apptha_Productfilters_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Check if module is enabled or not
     *
     * @return boolean
     */
    public function isEnabled()
    {

        /* if(Mage::helper('productfilters')->checkProductfilterKey()!= ''){

            $msg = Mage::helper('productfilters')->checkProductfilterKey();
            Mage::app()->getResponse()->setBody($msg);
            return;
        } */
    return Mage::getStoreConfigFlag('advancedproductfilters/catalog/enabled');

    }

    /**
     * Check if ajax is enabled
     *
     * @return boolean
     */
    public function isAjaxEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return Mage::getStoreConfigFlag('advancedproductfilters/catalog/ajax_enabled');
    }

    /**
     * Check if multipe choice filters is enabled
     *
     * @return boolean
     */
    public function isMultipleChoiceFiltersEnabled()
    {
         if (!$this->isEnabled()) {
            return false;
        }
        return Mage::getStoreConfigFlag('advancedproductfilters/catalog/multiple_choise_filters');
    }
    /**
     * Check if Price Slider is enabled
     *
     * @return boolean
     */
    public function isPriceSliderEnabled()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return Mage::getStoreConfigFlag('advancedproductfilters/catalog/price_slider');
    }
    /**
     * Check if we are in the catalog search
     *
     * @return boolean
     */
    public function isCatalogSearch()
    {
        $pathInfo = $this->_getRequest()->getPathInfo();
        if (stripos($pathInfo, '/catalogsearch/result') !== false) {
            return true;
        }
        return false;
    }

    public function getCatagoryParentId($id){

        $catagory = Mage::getModel('catalog/category');
        $catagory->load($id);
        return $catagory->getParentId();
    }

  /*   public function getNewArrivalsDate()
    {
        if (!$this->isEnabled()) {
            return false;
        }
        $date = Mage::getStoreConfig('advancedproductfilters/catalog/new_from');
        $fromDate = Mage::getModel('core/date')->date('Y-m-d', strtotime($date));

        return $fromDate;
    } */

     public function getTreeCategories($parentId,$productcollection, $isChild)
    {
        $allCats = Mage::getModel('catalog/category')->getCollection()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active','1')
            ->addAttributeToFilter('include_in_menu','1')
            ->addAttributeToFilter('parent_id',array('eq' => $parentId))
            ->addAttributeToSort('position', 'asc');

        $class = ($isChild) ? "sub-cat-list" : "cat-list";
        $html .= '<ul class="'.$class.'">';

        foreach($allCats as $category) {

            $subcats = $category->getChildren();
            $subhtml = '';
            if(!empty($subcats)){
                $subhtml = $this->getTreeCategories($category->getId(),$productcollection, true);
            }

            $_productCollection =  clone $productcollection;
            $productIdsCollection = $_productCollection->getAllIds();

            $_testproductCollection = Mage::getResourceModel('catalog/product_collection')
                                        ->addAttributeToFilter('entity_id',array('in'=>$productIdsCollection))
                                        ->addCategoryFilter($category);

            if(Mage::getStoreConfig('cataloginventory/options/show_out_of_stock')=='0')
            {
                Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($_testproductCollection);
            }

            $html .= '<li><div class="ajaxloader product_categories_list">';
                          if( (!empty($subhtml))){
                              $html .= '<span class="accor"></span>';
                          }else{
                              $html .= '<span class="no-accor"></span>';
                          }

                          if (count($_testproductCollection) <= 0)
                          {
                              $html.='<span class="product_block_count_zero">';
                              $html.= $category->getName().'<span>('.count($_testproductCollection).')</span>';
                              $html.='</span>';
                          }
                          else{
                          $html .= '<a data-url="'.$category->getUrl().'"
                               name="'.cat.'" class="ajaxfilter"  value="'.$category->getId().'"
                                href="'.$category->getUrl().'">'.$category->getName().'</a>'.
                        '<span>('.count($_testproductCollection).')</span>';
                          }

                        if(!empty($subhtml)){
                            $html .= $subhtml;
                        }

            $html .= '</div></li>';
        }
        $html .= '</ul>';
        return $html;
    }


    /**
     * Function to get the domain key
     *
     * Return domain key
     * @return string
     */

    public function domainKey($tkey)
    {
        $message = "EM-MKTPMP0EFIL9XEV8YZAL7KCIUQ6NI5OREH4TSEB3TSRIF2SI1ROTAIDALG-JW";
        $stringLength = strlen($tkey);
        for($i = 0; $i < $stringLength; $i++) {
            $keyArray[] = $tkey[$i];
        }
        $encMessage = "";
        $kPos = 0;
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $strLen = strlen($charsStr);
        for($i = 0; $i < $strLen; $i++) {
            $charsArray[] = $charsStr[$i];
        }
        $lenMessage = strlen($message);
        $count = count($keyArray);
        for($i = 0; $i < $lenMessage; $i++) {
            $char   = substr($message, $i, 1);
            $offset = $this->getOffset($keyArray[$kPos], $char);
            $encMessage .= $charsArray[$offset];
            $kPos++;

            if ($kPos >= $count) {
                $kPos = 0;
            }
        }
        return $encMessage;
    }

    /**
     *
     * Return offset key
     * @return string
     */
    public function getOffset($start, $end)
    {
        $charsStr = "WJ-GLADIATOR1IS2FIRST3BEST4HERO5IN6QUICK7LAZY8VEX9LIFEMP0";
        $strLen = strlen($charsStr);
        for ($i = 0; $i < $strLen; $i++) {
            $charsArray[] = $charsStr[$i];
        }
        for ($i = count($charsArray) - 1; $i >= 0; $i--) {
            $lookupObj[ord($charsArray[$i])] = $i;
        }
        $sNum   = $lookupObj[ord($start)];
        $eNum   = $lookupObj[ord($end)];
        $offset = $eNum - $sNum;
        if ($offset < 0) {
            $offset = count($charsArray) + ($offset);
        }
        return $offset;
    }
}