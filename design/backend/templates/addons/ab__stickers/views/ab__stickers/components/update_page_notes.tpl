{strip}
<div class="sidebar-row">
<h6>{__("ab__stickers.available_placeholders.header")}</h6>
<p class="muted" style="word-break: break-word;">{__("ab__stickers.available_placeholders")}</p>
{foreach fn_ab__stickers_get_sticker_available_placeholders($sticker_data) as $placeholder}
{$placeholder_title_lang_var = $placeholder.title|default:"ab__stickers.placeholders.{str_replace(['[', ']'], '', $placeholder.name)}"}
<p>
<code>{$placeholder.name}</code>
{if fn_is_lang_var_exists($placeholder_title_lang_var)}
&nbsp;- {__($placeholder_title_lang_var)}
{/if}
</p>
{/foreach}
{capture name="available_tags"}
<br/>
{foreach fn_ab__stickers_get_available_tags() as $tag}
<code>{$tag}</code>{if !$tag@last}, {else}<br/><br/>{/if}
{/foreach}
{/capture}
<h6>{__("ab__stickers.available_tags.header")}</h6>
<p class="muted" style="word-break: break-word;">{__("ab__stickers.available_tags")}</p>
{__("ab__stickers.available_tags.content", ["[available_tags]" => $smarty.capture.available_tags])}
{capture name="available_formula_modifiers"}
<br/>
{foreach fn_ab__stickers_get_available_formula_modifiers() as $modifier}
<code>{$modifier}</code>
{$modifier_lang_var = "ab__stickers.formulas.modifier.$modifier"}
{if fn_is_lang_var_exists($modifier_lang_var)}
&nbsp;- {__($modifier_lang_var)}
{/if}
{if !$modifier@last}<br/>{else}<br/><br/>{/if}
{/foreach}
{/capture}
<h6>{__("ab__stickers.formulas.header")}</h6>
<p class="muted" style="word-break: break-word;">{__("ab__stickers.formulas")}</p>
{__("ab__stickers.formulas.content", [
"[available_formula_modifiers]" => $smarty.capture.available_formula_modifiers
])}
<p>{__("ab__stickers.update_page_details", ["[href_demo]" => "ab__stickers.demodata"|fn_url])}</p>
</div>
{/strip}