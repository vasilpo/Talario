{if $show_trial_lesson|default:true && $product.price|floatval|round:2 <= 1.00 && $product.list_price}
    <span class="sd-price-trial-label {$class}">
       {__("sd_design_changes.trial_lesson")}
    </span>
{/if}