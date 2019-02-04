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
var ConfigurableSwatchesList = null;

(function($j){

    ConfigurableSwatchesList = {
        swatchesByProduct: {},

        init: function()
        {
            var that = this;
            $j('.configurable-swatch-list li').each(function() {
                that.initSwatch(this);
                var $swatch = $j(this);
                if ($swatch.hasClass('filter-match')) {
                    that.handleSwatchSelect($swatch);
                }
            });
        },

        initSwatch: function(swatch)
        {
            var that = this;
            var $swatch = $j(swatch);
            var productId;
            if (productId = $swatch.data('product-id')) {
                if (typeof(this.swatchesByProduct[productId]) == 'undefined') {
                    this.swatchesByProduct[productId] = [];
                }
                this.swatchesByProduct[productId].push($swatch);

                $swatch.find('a').on('click', function() {
                    that.handleSwatchSelect($swatch);
                    return false;
                });
            }
        },

        handleSwatchSelect: function($swatch)
        {
            var productId = $swatch.data('product-id');
            var label;
            if (label = $swatch.data('option-label')) {
                ConfigurableMediaImages.swapListImageByOption(productId, label);
            }

            $j.each(this.swatchesByProduct[productId], function(key, $productSwatch) {
                $productSwatch.removeClass('selected');
            });

            $swatch.addClass('selected');
        }
    }

    $j(document).on('configurable-media-images-init', function(){
        ConfigurableSwatchesList.init();
    });


})(jQuery);
