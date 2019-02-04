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
var MenuLoaded = false;
var MobileMenuLoaded = false;

function InitPopupContent()
{
    if (MenuLoaded) return;
    var xMenu = $('supermenu');
    if (typeof PopupMenuContent != 'undefined') xMenu.innerHTML = PopupMenuContent + xMenu.innerHTML;
    MenuLoaded = true;
}

function InitMobileMenuContent()
{
    if (MobileMenuLoaded) return;
    var xMenu = $('menu-content');
    if (typeof MobileMenuContent != 'undefined') xMenu.innerHTML = MobileMenuContent;
    MobileMenuLoaded = true;
}

function ShowMenuPopup(objMenu, event, popupId)
{
    InitPopupContent();
    if (typeof SupermenuTimerHide[popupId] != 'undefined') clearTimeout(SupermenuTimerHide[popupId]);
    objMenu = $(objMenu.id); var popup = $(popupId); if (!popup) return;
    if (!!ActiveMenu) {
        HideMenuPopup(objMenu, event, ActiveMenu.popupId, ActiveMenu.menuId);
    }
    ActiveMenu = {menuId: objMenu.id, popupId: popupId};
    if (!objMenu.hasClassName('active')) {
        SupermenuTimerShow[popupId] = setTimeout(function() {
            objMenu.addClassName('active');
            var popupWidth = CUSTOMMENU_POPUP_WIDTH;
            if (!popupWidth) popupWidth = popup.getWidth();
            var pos = PopupPos(objMenu, popupWidth);
            popup.style.top = pos.top + 'px';
            popup.style.left = pos.left + 'px';
            SetPopupZIndex(popup);
            if (CUSTOMMENU_POPUP_WIDTH)
                popup.style.width = CUSTOMMENU_POPUP_WIDTH + 'px';
            // --- Static Block width ---
            var block2 = $(popupId).select('div.block2');
            if (typeof block2[0] != 'undefined') {
                var wStart = block2[0].id.indexOf('_w');
                if (wStart > -1) {
                    var w = block2[0].id.substr(wStart+2);
                } else {
                    var w = 0;
                    $(popupId).select('div.block1 div.column').each(function(item) {
                        w += $(item).getWidth();
                    });
                }
                if (w) block2[0].style.width = w + 'px';
            }
            // --- change href ---
            var MenuAnchor = $(objMenu.select('a')[0]);
            ChangeTopMenuHref(MenuAnchor, true);
            // --- show popup ---
            if (typeof jQuery == 'undefined') {
                popup.style.visibility = 'visible';
            } else {
                //jQuery('#' + popupId).stop(true, true).fadeIn();
                popup.style.visibility = 'visible';
                jQuery('#' + popupId).addClass("menu-level");
            }
        }, CUSTOMMENU_POPUP_DELAY_BEFORE_DISPLAYING);
    }
}

function HideMenuPopup(element, event, popupId, menuId)
{
    if (typeof SupermenuTimerShow[popupId] != 'undefined') clearTimeout(SupermenuTimerShow[popupId]);
    var element = $(element); var objMenu = $(menuId) ;var popup = $(popupId); if (!popup) return;
    var CurrentMouseTarget = getCurrentMouseTarget(event);
    if (!!CurrentMouseTarget) {
        if (!IsChildOf(element, CurrentMouseTarget) && element != CurrentMouseTarget) {
            if (!IsChildOf(popup, CurrentMouseTarget) && popup != CurrentMouseTarget) {
                if (objMenu.hasClassName('active')) {
                    SupermenuTimerHide[popupId] = setTimeout(function() {
                        objMenu.removeClassName('active');
                        // --- change href ---
                        var MenuAnchor = $(objMenu.select('a')[0]);
                        ChangeTopMenuHref(MenuAnchor, false);
                        // --- hide popup ---
                        if (typeof jQuery == 'undefined') {
                            popup.style.visibility = 'hidden';
                        } else {
                            //jQuery('#' + popupId).stop(true, true).fadeOut();
                        	popup.style.visibility = 'hidden';
                        	jQuery('#' + popupId).removeClass("menu-level");
                        }
                    }, CUSTOMMENU_POPUP_DELAY_BEFORE_HIDING);
                }
            }
        }
    }
}

function PopupOver(element, event, popupId, menuId)
{
    if (typeof SupermenuTimerHide[popupId] != 'undefined') clearTimeout(SupermenuTimerHide[popupId]);
}

function PopupPos(objMenu, w)
{
    var pos = objMenu.cumulativeOffset();
    var wraper = $('supermenu');
    var posWraper = wraper.cumulativeOffset();
    var xTop = pos.top - posWraper.top
    if (CUSTOMMENU_POPUP_TOP_OFFSET) {
        xTop += CUSTOMMENU_POPUP_TOP_OFFSET;
    } else {
        xTop += objMenu.getHeight();
    }
    var res = {'top': xTop};
    if (CUSTOMMENU_RTL_MODE) {
        var xLeft = pos.left - posWraper.left - w + objMenu.getWidth();
        if (xLeft < 0) xLeft = 0;
        res.left = xLeft;
    } else {
        var wWraper = wraper.getWidth();
        var xLeft = pos.left - posWraper.left;
        if ((xLeft + w) > wWraper) xLeft = wWraper - w;
        if (xLeft < 0) xLeft = 0;
        res.left = xLeft;
    }
    return res;
}

function ChangeTopMenuHref(MenuAnchor, state)
{
    if (state) {
        MenuAnchor.href = MenuAnchor.rel;
    } else if (IsMobile.any()) {
        MenuAnchor.href = 'javascript:void(0);';
    }
}

function IsChildOf(parent, child)
{
    if (child != null) {
        while (child.parentNode) {
            if ((child = child.parentNode) == parent) {
                return true;
            }
        }
    }
    return false;
}

function SetPopupZIndex(popup)
{
    $$('.wp-custom-menu-popup').each(function(item){
       item.style.zIndex = '9999';
    });
    popup.style.zIndex = '10000';
}

function getCurrentMouseTarget(xEvent)
{
    var CurrentMouseTarget = null;
    if (xEvent.toElement) {
        CurrentMouseTarget = xEvent.toElement;
    } else if (xEvent.relatedTarget) {
        CurrentMouseTarget = xEvent.relatedTarget;
    }
    return CurrentMouseTarget;
}

function getCurrentMouseTargetMobile(xEvent)
{
    if (!xEvent) var xEvent = window.event;
    var CurrentMouseTarget = null;
    if (xEvent.target) CurrentMouseTarget = xEvent.target;
        else if (xEvent.srcElement) CurrentMouseTarget = xEvent.srcElement;
    if (CurrentMouseTarget.nodeType == 3) // defeat Safari bug
        CurrentMouseTarget = CurrentMouseTarget.parentNode;
    return CurrentMouseTarget;
}

/* Mobile */
function MenuButtonToggle()
{
    $('menu-content').toggle();
}

function GetMobileSubMenuLevel(id)
{
    var rel = $(id).readAttribute('rel');
    return parseInt(rel.replace('level', ''));
}

function SubMenuToggle(obj, activeMenuId, activeSubMenuId)
{
    var currLevel = GetMobileSubMenuLevel(activeSubMenuId);
    // --- hide submenus ---
    $$('.wp-custom-menu-submenu').each(function(item) {
        if (item.id == activeSubMenuId) return;
        var xLevel = GetMobileSubMenuLevel(item.id);
        if (xLevel >= currLevel) {
            $(item).hide();
        }
    });
    // --- reset button state ---
    $('supermenu-mobile').select('span.button').each(function(xItem) {
        var subMenuId = $(xItem).readAttribute('rel');
        if (!$(subMenuId).visible()) {
            $(xItem).removeClassName('open');
        }
    });
    // ---
    if ($(activeSubMenuId).getStyle('display') == 'none') {
        $(activeSubMenuId).show();
        $(obj).addClassName('open');
    } else {
        $(activeSubMenuId).hide();
        $(obj).removeClassName('open');
    }
}

function ResetMobileMenuState()
{
    $('menu-content').hide();
    $$('.wp-custom-menu-submenu').each(function(item) {
        $(item).hide();
    });
    $('supermenu-mobile').select('span.button').each(function(item) {
        $(item).removeClassName('open');
    });
}

function SupermenuMobileToggle()
{
    var w = window,
        d = document,
        e = d.documentElement,
        g = d.getElementsByTagName('body')[0],
        x = w.innerWidth || e.clientWidth || g.clientWidth,
        y = w.innerHeight|| e.clientHeight|| g.clientHeight;

    if ((x < 800 && IsMobile.any()) && MobileMenuEnabled) {
        InitMobileMenuContent();
        $('supermenu').hide();
        $('supermenu-mobile').show();
        // --- ajax load ---
        if (MoblieMenuAjaxUrl) {
            new Ajax.Request(
                MoblieMenuAjaxUrl, {
                    asynchronous: true,
                    method: 'post',
                    onSuccess: function(transport) {
                        if (transport && transport.responseText) {
                            try {
                                response = eval('(' + transport.responseText + ')');
                            } catch (e) {
                                response = {};
                            }
                        }
                        MobileMenuContent = response;
                        MobileMenuLoaded = false;
                        InitMobileMenuContent();
                    }
                }
            );
            MoblieMenuAjaxUrl = null;
        }
    } else {
        $('supermenu-mobile').hide();
        ResetMobileMenuState();
        $('supermenu').show();
        // --- ajax load ---
        if (MenuAjaxUrl) {
            new Ajax.Request(
                MenuAjaxUrl, {
                    asynchronous: true,
                    method: 'post',
                    onSuccess: function(transport) {
                        if (transport && transport.responseText) {
                            try {
                                response = eval('(' + transport.responseText + ')');
                            } catch (e) {
                                response = {};
                            }
                        }
                        if ($('supermenu')) $('supermenu').update(response.topMenu);
                        PopupMenuContent = response.popupMenu;
                    }
                }
            );
            MenuAjaxUrl = null;
        }
    }

    if ($('supermenu-loading')) $('supermenu-loading').remove();
}

var IsMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (IsMobile.Android() || IsMobile.BlackBerry() || IsMobile.iOS() || IsMobile.Opera() || IsMobile.Windows());
    }
};