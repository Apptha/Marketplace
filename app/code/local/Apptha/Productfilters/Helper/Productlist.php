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
 * @package    	Apptha_Advancedfilters
 * @version     1.0.0
 * @author      Apptha Team <developers@contus.in>
 * @copyright   Copyright (c) 2014 Apptha. (http://www.apptha.com)
 * @license     http://www.apptha.com/LICENSE.txt
 *
 */
class Apptha_Productfilters_Helper_Productlist extends Mage_ConfigurableSwatches_Helper_Productlist
{
 	public function convertLayerBlock($blockName)
    {
    	
        if (Mage::helper('configurableswatches')->isEnabled()
            && ($block = Mage::app()->getLayout()->getBlock($blockName))
            && $block instanceof Mage_Catalog_Block_Layer_View
        ) {

            // First, set a new template for the attribute that should show as a swatch
        	$result=Mage::getSingleton('core/design_package')->getPackageName();
        	if ($layer = $block->getLayer()) {
                foreach ($layer->getFilterableAttributes() as $attribute) {
                	if(Mage::helper('productfilters')->isEnabled())
                	{
                    if (Mage::helper('configurableswatches')->attrIsSwatchType($attribute)) {
                        $block->getChild($attribute->getAttributeCode() . '_filter')
                            ->setTemplate('productfilters/catalog/layer/filter/appthaswatch.phtml');
                    }
                	}
					elseif(!($result=="default"))
                	{
                		if (Mage::helper('configurableswatches')->attrIsSwatchType($attribute)) {
                			$block->getChild($attribute->getAttributeCode() . '_filter')
                			->setTemplate('configurableswatches/catalog/layer/filter/swatches.phtml');
                		}
                	} 
                }
            }

            // Then set a specific renderer block for showing "currently shopping by" for the swatch attribute
            // (block class takes care of determining which attribute is applicable)
            if ($stateRenderersBlock = $block->getChild('state_renderers')) {
                $swatchRenderer = Mage::app()->getLayout()
                    ->addBlock('configurableswatches/catalog_layer_state_swatch', 'product_list.swatches');
                $swatchRenderer->setTemplate('configurableswatches/catalog/layer/state/swatch.phtml');
                $stateRenderersBlock->append($swatchRenderer);
            }
        }
    }
}