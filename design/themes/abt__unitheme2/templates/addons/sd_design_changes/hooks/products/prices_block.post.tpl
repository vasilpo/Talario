{if $show_trial_lesson|default:true && $price|floatval|round:2 == 1.00 && $product.list_price|floatval}
    <span class="sd-price-trial-label">
       {__("sd_design_changes.trial_lesson")}
    </span>
{/if}