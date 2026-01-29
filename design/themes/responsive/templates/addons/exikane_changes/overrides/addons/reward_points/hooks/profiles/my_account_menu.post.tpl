{if $auth.user_id}
    <li class="ty-account-info__item ty-dropdown-box__item">
        <a class="ty-account-info__a" href="{"reward_points.userlog"|fn_url}" rel="nofollow">
            {__("my_points")}&nbsp;<span class="ty-reward-points-count">({$user_info.points|default:"0"})</span>
            <span class="cm-tooltip"
                  title="{__("reward_points_tooltip") nofilter}"
                  style="display: inline-block;
                         width: 16px;
                         height: 16px;
                         line-height: 16px;
                         border-radius: 50%;
                         background: #e8f5e9;
                         color: #4caf50;
                         font-size: 11px;
                         font-weight: bold;
                         text-decoration: none;
                         text-align: center;
                         border: 1px solid #a5d6a7;
                         vertical-align: baseline;
                         margin-left: 4px;
                         ">?</span>
        </a>
    </li>
{/if}