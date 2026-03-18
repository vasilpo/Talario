{if $auth.user_id}
{$dropdown_box_item_class = ($block.wrapper === "blocks/wrappers/onclick_dropdown.tpl") ? "ty-dropdown-box__item" : ""}
<li class="ty-account-info__item {$dropdown_box_item_class}"><a class="ty-account-info__a" href="{"reward_points.userlog"|fn_url}" rel="nofollow">{__("my_points")}&nbsp;<span class="ty-reward-points-count">({$user_info.points|default:"0"})</span></a></li>
{/if}