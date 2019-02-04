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
/*global window */
/* added the social login links into default login links */
document.observe("dom:loaded", function() {

    var i;
    try {
    	var elements = document.getElementsByClassName("link-compare");
        for (i = 0; i < elements.length; i++) {
        	var url = elements[i].href;
        	var id = url.substring(url.lastIndexOf("product")+8,url.lastIndexOf("/uenc"));
        	url = JSON.stringify(url);
        	elements[i].href = 'javascript:ajaxCompare('+url+','+id+')';
        }
    }

    catch (exception)
    {
        alert(exception);
    }
});

// Ajax Compare starts here
function ajaxCompare(url,id){
	url = url.replace("catalog/product_compare/add","marketplace/general/ajaxCompare");
	url += 'isAjax/1/';
	jQuery.ajax( {
		url : url,
		dataType : 'json',
		success : function(data) {
			if(data.status == 'ERROR'){
				alert(data.message);
			}else{
				if(jQuery('.block-compare').length){
                    jQuery('.block-compare').replaceWith(data.sidebar);
                    jQuery('.compare-success-message').replaceWith(data.message).css('color','red');

                }else{
                    if(jQuery('.col-right').length){
                    	jQuery('.col-right').prepend(data.sidebar);
                    	jQuery('.compare-success-message').replaceWith(data.message).css('color','red');

                    }
                }
			}
		}
	});
}

function ajaxRemove(url,id){ 
	url = url.replace("catalog/product_compare/remove","marketplace/general/remove");
	url += 'isAjax/1/';
	jQuery.ajax( {
		url : url,
		dataType : 'json',
		success : function(data) {
			if(data.status == 'ERROR'){
				alert(data.message);
			}else{
				
				if(jQuery('.block-compare').length){
                    jQuery('.block-compare').replaceWith(data.sidebar);
                }else{
                    if(jQuery('.col-right').length){
                    	jQuery('.col-right').prepend(data.sidebar);
                    }
                }
			}
		}
	});
}

function ajaxClear(url,id){ 
	jQuery(".block-compare").hide();
	url = url.replace("catalog/product_compare/clear","marketplace/general/clear");
	url += 'isAjax/1/';
	jQuery.ajax( {
		url : url,
		dataType : 'json',
		success : function(data) {
			if(data.status == 'ERROR'){
				alert(data.message);
			}else{
				if(jQuery('.block-compare').length){
                    jQuery('.block-compare').replaceWith(data.sidebar);
                }else{
			         jQuery('body').addClass(".block-compare");
                     jQuery('.block-compare').replaceWith(data.sidebar);
                }
			}
		}
	});
}

function expandColapseCompareSection() {
	jQuery(".block-compare").toggleClass("open");
	 if ( jQuery(".block-compare").hasClass( "open" ) ) {
		 jQuery('.minimize').css('display','block');
		 jQuery(".add").css('display','none');
	    } else {
	     jQuery('.add').css('display','block');
		 jQuery(".minimize").css('display','none');
	    }
}

	function showAll () {
		jQuery(".block-compare").removeClass("open");

		 jQuery('.add').css('display','block');
		 jQuery(".minimize").css('display','none');
		
	}

	



