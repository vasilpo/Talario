<div class="top-bar__btn-wrapper btn-group dropdown-top-menu-item notifications-center__opener-wrapper cm-dropdown-skip-processing"
    data-ca-notifications-center="main"
    data-ca-notifications-center-text-loading="{__("loading")|escape}"
    data-ca-notifications-center-text-show-more="{__("show_more")|escape}"
    data-ca-notifications-center-text-show-less="{__("show_less")|escape}"
    data-ca-notifications-center-text-no-data="{__("notifications_center.no_notifications")|escape}"
    data-ca-notifications-center-text-notifications="{__("notifications_center.notifications")|escape}"
    data-ca-notifications-center-text-mark-all-as-read="{__("notifications_center.mark_all_as_read")|escape}"
>
    <button class="dropdown-toggle dropdown-top-menu-item-link top-bar__btn notifications-center__opener-btn"
        data-toggle="dropdown"
        type="button"
        title="{__("notifications_center.notifications")}"
    >
        <span class="top-bar__btn-inner notifications-center__opener-btn-inner">
            <span>
                {include_ext file="common/icon.tpl" source="bell"}
                <span class="cs-notifications-center__counter"
                    data-ca-notifications-center-counter
                    data-ca-notifications-center-count=""></span>
            </span>
        </span>
    </button>
    <ul class="dropdown-menu pull-right notifications-center__root" data-ca-notifications-center-root>
        <div class="cc-dropdown">
            <div class="cc-dropdown__title-wrapper" text="{__("notifications_center.notifications")}">
                <span class="cc-dropdown__title">{__("notifications_center.notifications")}</span>
            </div>
            <div class="cc-all-read">
                <div class="cc-all-read--inner">{__("loading")}</div>
            </div>
        </div>
    </ul>
</div>
