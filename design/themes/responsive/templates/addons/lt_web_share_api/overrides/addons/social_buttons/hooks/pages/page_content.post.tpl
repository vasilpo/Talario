{if $display_button_block && !$subpages}
    <div class="ty-social-buttons clearfix">
        <a
            href="{$config.current_url|fn_url}"
            class="ty-muted ty-web-api-share"
            data-ca-web-share="trigger"
        >
            {__("sb_share")}
        </a>
    </div>
{/if}
