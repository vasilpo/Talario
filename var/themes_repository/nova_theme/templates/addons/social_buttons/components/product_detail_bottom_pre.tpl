{$dropdown_id = $block.snapping_id}
{$social_buttons_class = "ty-social-buttons--dropdown"}
<div class="ty-dropdown-box ty-social-buttons__dropdown-box">
    <div id="sw_dropdown_{$dropdown_id}" class="ty-dropdown-box__title ty-social-buttons__dropdown-box-title cm-combination">
        <button type="button" class="ty-btn ty-btn__text ty-social-buttons__dropdown-box-btn" title="{__("sb_share")}">{include_ext file="common/icon.tpl" class="ty-icon-bubble ty-social-buttons__dropdown-box-title-icon"} {__("sb_share")}</button>
    </div>
    <div id="dropdown_{$dropdown_id}" class="cm-popup-box ty-dropdown-box__content ty-social-buttons__dropdown-box-content hidden">

{* Export *}
{$social_buttons_class = $social_buttons_class scope=parent}