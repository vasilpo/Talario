<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2025   *
* / /_\ | | _____  _| |_/ /_ __ __ _ _ __   __| |_ _ __   __ _   | |_ ___  __ _ _ __ ___   *
* |  _  | |/ _ \ \/ / ___ \ '__/ _` | '_ \ / _` | | '_ \ / _` |  | __/ _ \/ _` | '_ ` _ \  *
* | | | | |  __/>  <| |_/ / | | (_| | | | | (_| | | | | | (_| |  | ||  __/ (_| | | | | | | *
* \_| |_/_|\___/_/\_\____/|_|  \__,_|_| |_|\__,_|_|_| |_|\__, |  \___\___|\__,_|_| |_| |_| *
*                                                         __/ |                            *
*                                                        |___/                             *
* ---------------------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license and accept    *
* to the terms of the License Agreement can install and use this program.                  *
* ---------------------------------------------------------------------------------------- *
* website: https://cs-cart.alexbranding.com                                                *
*   email: info@alexbranding.com                                                           *
*******************************************************************************************/
return [
'lm_contacts' => '<div class="ut2-pn">
<div class="ty-dropdown-box">
<div class="ut2-btn-contacts" onclick="$(this).toggleClass(\'open\');">
<i class="ut2-icon-baseline-phone"></i>
<div class="ut2-pn__items-full ty-dropdown-box__content hidden">
<a href="javascript:void(0);" rel="nofollow" class="ut2-btn-close hidden" onclick="$(this).parent().prev().removeClass(\'open\');"><i class="ut2-icon-baseline-close"></i></a>
<div class="ut2-pn__items">
{* Change phones numbers *}
<a href="tel:+88005550000"><span>+8(800)</span> 555-00-00</a>
<a href="tel:+88005550001"><span>+8(800)</span> 555-00-01</a>
<a href="tel:+88005550002"><span>+8(800)</span> 555-00-02</a>
</div>
</div>
</div>
<div class="ut2-pn__wrap ">
<div class="ut2-pn__items">
{* Change phone number *}
<a href="tel:+88005559595"><span>+8(800)</span> 555-95-95</a>
</div>
<div class="ut2-pn__link">
{if $addons.call_requests.status == "A"}{include file="addons/call_requests/blocks/abt__ut2_call_request.tpl"}{/if}
</div>
</div>
</div>
</div>',
];
