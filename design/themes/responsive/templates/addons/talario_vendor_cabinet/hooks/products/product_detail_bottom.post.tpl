{if $product.talario_preview_variations || $product.talario_preview_existing_variations}
    <div class="ty-product-detail__talario-preview-variations">
        <h3>Варианты занятия — предварительный просмотр</h3>
        <ul>
            {foreach $product.talario_preview_existing_variations as $variation}
                <li{if $variation.deleted} class="ty-muted"{/if}>
                    {$variation.product|default:"Вариант"}: {include file="common/price.tpl" value=$variation.price}
                    {if $variation.deleted} — будет удалён{/if}
                </li>
            {/foreach}
            {foreach $product.talario_preview_variations as $variation}
                <li>
                    {", "|implode:$variation.variant_names}: {include file="common/price.tpl" value=$variation.price}
                    — новый вариант
                </li>
            {/foreach}
        </ul>
    </div>
{/if}
