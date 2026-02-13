{if $display_on}
        <ul class="ty-extended-social-buttons">
            {foreach from=$settings_pr key="provider" item="data"}
                <li class="ty-extended-social-buttons__items">{include file="addons/extended_social_buttons/components/providers_handler.tpl" provider=$provider data=$data}</li>
            {/foreach}
        </ul>
{/if}