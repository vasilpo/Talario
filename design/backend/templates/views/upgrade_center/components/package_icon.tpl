{* Imports *}

{* Constants *}
{$LABEL_LENGTH = 2}

{* Icon size *}
{$icon_width = 60}

{* Image attributes *}
{$image_dir_path = "`$images_dir`/addons/`$package.id`"}
{$icon_class = "packages-package-icon__image packages-package-icon__image--medium"}
{$logo_path_light = "cart_logo_short.svg"}
{$logo_path_dark = "cart_logo_short_white.svg"}

{capture name="icon"}
    {if $package.icon_path}
        <picture>
            <img src="{$images_dir}/{$package.icon_path}"
                width="{$icon_width}"
                height="{$icon_height}"
                class="{$icon_class}"
            />
        </picture>
    {elseif $type == "core" || $type == "hotfix"}
        <picture>
            {if $backoffice_color_scheme === "BackofficeColorSchemeVariants::DARK"|enum}
                <img src="{$images_dir}/{$logo_path_dark}"
                    width="{$icon_width}"
                    height="{$icon_height}"
                    class="{$icon_class}"
                />
            {elseif $backoffice_color_scheme === "BackofficeColorSchemeVariants::SYSTEM"|enum}
                <source media="(prefers-color-scheme: light)" srcset="{$images_dir}/{$logo_path_light}">
                <source media="(prefers-color-scheme: dark)" srcset="{$images_dir}/{$logo_path_dark}">
                <img src="" width="{$icon_width}" height="{$icon_height}" class="{$icon_class}"/>
            {else}
                <img src="{$images_dir}/{$logo_path_light}"
                    width="{$icon_width}"
                    height="{$icon_height}"
                    class="{$icon_class}"
                />
            {/if}
        </picture>
    {elseif $package.has_icon}
        <picture>
            {if $package.has_svg_icon}
                <source srcset="{$image_dir_path}/icon.svg" type="image/svg+xml">
            {/if}
            {if $package.has_avif_icon}
                <source srcset="{$image_dir_path}/icon.avif" type="image/avif">
            {/if}
            {if $package.has_webp_icon}
                <source srcset="{$image_dir_path}/icon.webp" type="image/webp">
            {/if}
            <source srcset="{$image_dir_path}/icon.png" type="image/png"> 
            <img src="{$image_dir_path}/icon.png"
                width="{$icon_width}"
                height="{$icon_width}"
                class="{$icon_class}"
            />
        </picture>
    {else}
        <div class="{$icon_class} packages-package-icon__image--label">
            {$package.name|upper|truncate:$LABEL_LENGTH:""}
        </div>
    {/if}
{/capture}

<div class="packages-package-icon__wrapper packages-package-icon__wrapper--medium
    {if $package.status}packages-package-icon__wrapper--{$package.status|lower}{/if}"
>
    {$smarty.capture.icon nofilter}
</div>