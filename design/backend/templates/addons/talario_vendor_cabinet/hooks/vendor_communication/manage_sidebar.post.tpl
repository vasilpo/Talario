{include file="addons/talario_vendor_cabinet/components/cabinet_style.tpl"}
{if $runtime.company_id && $communication_type == "vendor_to_admin"}
    <div class="talario-message-help">
        <strong>Связь с Таларио</strong>
        <p class="muted">Если удобнее написать по почте: <a href="mailto:partners@talario.ru">partners@talario.ru</a></p>
    </div>
{/if}
