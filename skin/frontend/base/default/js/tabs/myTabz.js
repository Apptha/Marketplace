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
var navTabClass = 'TabNav';
var navTabSelectedClass = 'selectedTab';
var defaultTabId = 'defaultTab';

var preventDefaultEventonTabSelect = 1; // true==1 stop the url from being updated.


function showTab(e){
    var target, id, liElm, aElm, targetId; 
    elm = $(e);   
    // alert(elm.tagName);
    if (elm.tagName    == 'A' ) {   // in case the anchore link eack clicked
        liElm = elm.parentNode;
        aElm = elm ;
    }  else {   
        liElm = elm;
        aElm = elm.firstDescendant();   
    }
    target = aElm.readAttribute('href').strip();
    liElm.siblings().invoke('removeClassName', navTabSelectedClass); //de-select all tabs
    liElm.addClassName(navTabSelectedClass); // make our new tab selected
    if (target.startsWith('#'))  { //only if the anchor link is within the current document
        targetId = target.substring(1);
        var targetElm = $(targetId);  
        targetElm.siblings().invoke('hide');
        targetElm.show();           
    }
}

function showTabEvent(Event) {
    var elm = Event.element(); 
    showTab(elm);    
    if (preventDefaultEventonTabSelect) Event.preventDefault(); 
}

function markEvent(event) { // for debuging events
    var node = Event.element(event); // the node that was clicked on
    node.style.color = "green";
}

// this sets up the showTabEvent on the click events, after the dom has been loaded
document.observe('dom:loaded', function () {
    $$('.'+navTabClass+' li').each ( function (e)   {       
        e.observe('click',showTabEvent);   
        //      e.observe('click',markEvent);          
    } );
    if ($(defaultTabId) == null) { // if the default Tab Id is NOT used in the pages markup
        // set the default tab based on the first li of the list with the TabNav Class
        showTab($$('.'+navTabClass).first().firstDescendant());  
    }  else { // set the default tab based on the Id
        showTab($(defaultTabId));
    }    
})


function setNewStyleSheet (ss ) { 
    $$('link[rel="stylesheet"]').invoke('remove');
 
        $$('link[rel="stylesheet"]').invoke('remove');
        var attrs = {
            type 	: "text/css",
            href 	: ss,
            rel	:"stylesheet"
        };
 
        var sslink = new Element('link', attrs);
        $$('head').invoke('insert',sslink);

}