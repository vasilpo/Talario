{if !$auth.user_id}
    {include file="addons/exikane_changes/components/guest_banner.tpl"
        banner_title=__("exikane_changes.guest_banner_title")
        banner_text=__("exikane_changes.guest_banner_text")
        banner_button_text=__("exikane_changes.guest_banner_button")
        banner_action_style="link"
    }
{/if}
