{$enable_minify_critical_css = $enable_minify_critical_css|default:true}
{capture name="critical_css" assign=critical_css}{strip}
    <style>
        {hook name="index:critical_css"}
            {* Disable shifting elements on page load *}
            {include file="design/backend/css/tygh/critical.css"}

            {if $backoffice_color_scheme === "BackofficeColorSchemeVariants::DARK"|enum}
                input, textarea, button {
                    --cs-critical-css-border-color: #8f9499;
                }
            {elseif $backoffice_color_scheme === "BackofficeColorSchemeVariants::SYSTEM"|enum}
                @media (prefers-color-scheme: light) {
                    input, textarea, button {
                        --cs-critical-css-border-color: #dde4ef;
                    }
                }
                @media (prefers-color-scheme: dark) {
                    input, textarea, button {
                        --cs-critical-css-border-color: #8f9499;
                    }
                }
            {/if}
        {/hook}
    </style>
{/strip}{/capture}
{* Minify CSS *}
{if $enable_minify_critical_css}
{$critical_css = $critical_css|regex_replace:
    "/[\r\n\t]/" : ""|replace:
    "  ":" "|replace:
    "  ":" "|replace:
    " {ldelim}":"{ldelim}"|replace:
    " {rdelim}":"{rdelim}"|replace:
    "{ldelim} ":"{ldelim}"|replace:
    "{rdelim} ":"{rdelim}"|replace:
    ", ":","|replace:
    ": ":":"|replace:
    "; ":";"
}
{/if}
{* Insert CSS *}
{$critical_css nofilter}
