{if $qr_code_url}
    {$size = "Addons\\QrOrder\\ImageSettings::SIZE"|enum}
    <div class="ty-center">
        <img src="{$qr_code_url}" width={$size} height={$size} />
    </div>
{/if}
