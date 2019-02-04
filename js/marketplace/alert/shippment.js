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
var trackingControl,shipping;
trackingControl = {
    index : 0,
    add : function () {
        this.index++;
        var data = {index:this.index};
        Element.insert($('track_row_container'), {bottom: this.template.evaluate(data)});
        $('trackingC' + this.index).disabled = false;
        $('trackingT' + this.index).disabled = false;
        $('trackingN' + this.index).disabled = false;
        $('trackingN' + this.index).addClassName('required-entry');
        this.bindCurrierOnchange();
    },
    deleteRow : function(event) {
        var row = Event.findElement(event, 'tr');
        if (row) {
            row.parentNode.removeChild(row)
        }
    },
    bindCurrierOnchange : function() {
        var elems = $('tracking_numbers_table').select('.select');
        elems.each(function (elem) {
            if (!elem.onchangeBound) {
                elem.onchangeBound = true;
                elem.valueInput = $(elem.parentNode.parentNode).select('.number-title')[0];
                elem.observe('change', this.currierOnchange);
            }
        }.bind(this));
    },
    currierOnchange : function(event) {
        var elem = Event.element(event);
        var option = elem.options[elem.selectedIndex];        
        var trackingTilteForSeller = document.getElementById("trackingTitleForSeller").value;
        if (option.value && option.value != 'custom') {
            elem.valueInput.value = option.text + ' - ' + trackingTilteForSeller;
        }
        else {
            elem.valueInput.value = trackingTilteForSeller;
        }
    }
}

shipping = {
		show: function(event){
			var elem = Event.element(event);
			$('shipping_tracking').show();
		}
}

