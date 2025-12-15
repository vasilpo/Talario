{if $show_trial_lesson|default:true && $product.price|floatval|round:2 == 1.00 && $product.list_price|floatval}
    <span class="sd-price-trial-label sd-price-trial-label--mobile-view">
       {__("sd_design_changes.trial_lesson")}
    </span>
{/if}