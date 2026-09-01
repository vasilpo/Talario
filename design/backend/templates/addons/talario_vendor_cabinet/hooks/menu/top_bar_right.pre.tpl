{if $runtime.controller === "talario_dashboard" || $runtime.controller === "talario_locations" || $runtime.controller === "talario_classes" || $runtime.controller === "talario_finance" || $runtime.controller === "talario_messages" || $runtime.controller === "talario_notifications" || $runtime.controller === "talario_profile" || $runtime.controller === "talario_documents" || $runtime.controller === "ec_table_booking_system" || $runtime.controller === "vendor_communication"}
{literal}
<style>
/* Единый визуальный слой кабинета партнёра. Не затрагивает администратора. */
.cs-main-menu{--cs-menu-sidebar-color:#f8f2e8!important;--cs-menu-sidebar-bottom-toolbar-color:#f8f2e8!important;--cs-content-background:#f8f2e8!important;background:#f8f2e8!important;border-right:1px solid #eee5d9!important}.cs-main-menu__header{height:82px!important;background:#f8f2e8!important}.cs-main-menu__logo-wrapper{padding-left:18px!important}.cs-main-menu__outer{background:#f8f2e8!important}.cs-main-menu .main-menu-1__link{margin:3px 10px!important;border-radius:8px!important;color:#4b5563!important}.cs-main-menu .main-menu-1__link:hover{background:#fcf7ee!important}.cs-main-menu .main-menu-1__link--active,.cs-main-menu .main-menu-1__link--icon--active{background:#fff6df!important;color:#705d38!important}.cs-main-menu .main-menu-1__icon--active{color:#bf8d18!important}.top-bar,.top-bar__inner{background:#f8f2e8!important;border-bottom:1px solid #eee5d9!important}.top-bar__right{gap:10px!important}.talario-partner-identity{display:flex!important;flex-direction:column!important;align-items:flex-end!important;justify-content:center!important;min-width:130px!important;margin-right:4px!important;color:#2d3540!important;line-height:1.25!important}.talario-partner-identity strong{max-width:180px!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important;font-size:12px!important}.talario-partner-identity span{max-width:180px!important;overflow:hidden!important;text-overflow:ellipsis!important;white-space:nowrap!important;color:#808895!important;font-size:11px!important}.main-content,.content-wrap{background:#fffdf9!important}
.talario-cabinet{max-width:1280px;color:#252b33;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif}.talario-cabinet .talario-todo,.talario-cabinet .talario-panel,.talario-cabinet .talario-class-card{margin:0 0 18px;padding:22px;border:1px solid #e8ebf1;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(36,47,65,.04)}.talario-cabinet h2{margin:0 0 6px;color:#2a3038;font-size:19px;line-height:1.3}.talario-cabinet .muted,.talario-cabinet .description{color:#818896!important}.talario-dashboard__header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin:0 0 18px}.talario-cabinet .btn{border-color:#e0e5ec;border-radius:8px;background:#fff;color:#4b5563;text-shadow:none}.talario-cabinet .btn:hover{background:#f7f9fc}.talario-cabinet .btn-primary{border:0;background:#f7c62f;color:#3f3210;font-weight:700;box-shadow:0 4px 10px rgba(196,150,20,.16)}.talario-cabinet .btn-primary:hover{background:#edb91c;color:#3f3210}.talario-cabinet .buttons-container{border-top:1px solid #edf0f4;background:transparent}.talario-cabinet input[type="text"],.talario-cabinet input[type="number"],.talario-cabinet input[type="email"],.talario-cabinet textarea,.talario-cabinet select{border:1px solid #dfe4eb;border-radius:8px;background:#fff;box-shadow:none}.talario-cabinet input:focus,.talario-cabinet textarea:focus,.talario-cabinet select:focus{border-color:#91b8e6;box-shadow:0 0 0 3px rgba(91,149,214,.12)}.talario-cabinet input[disabled]{background:#f7f9fc;color:#606a77}.talario-stats{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;margin:0 0 18px}.talario-stats--two{grid-template-columns:repeat(2,minmax(0,1fr));max-width:680px}.talario-stat{padding:19px;border:1px solid #e8ebf1;border-radius:12px;background:#fff;box-shadow:0 3px 12px rgba(36,47,65,.04);color:#2d3540;text-decoration:none}.talario-stat strong{display:block;margin-bottom:5px;font-size:25px;line-height:1.1}.talario-stat span{color:#7f8794;font-size:13px}.talario-filters{gap:7px}.talario-filters .btn-primary{background:#edf3fb;color:#397abd;box-shadow:none}.talario-class-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}.talario-class-card{overflow:hidden;padding:0!important}.talario-class-card__image{height:190px;background:#f3f5f8;overflow:hidden}.talario-class-card__image img{width:100%;height:100%;object-fit:cover}.talario-class-card__body{padding:18px}.talario-class-card__heading,.talario-class-card__meta{display:flex;align-items:flex-start;justify-content:space-between;gap:12px}.talario-class-card__heading h3{margin:0 0 8px;font-size:17px;line-height:1.35}.talario-class-card__meta{align-items:center;margin:14px 0}.talario-class-card__actions{display:flex;gap:8px}.talario-class-card__actions .btn{flex:1}.talario-notifications-list{display:flex;flex-direction:column;gap:10px}.talario-notification{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:16px 18px;border:1px solid #e8ebf1;border-radius:10px;background:#fff}.talario-notification--unread{border-color:#b9d2ec;background:#fbfdff}.talario-notification__date{margin-top:7px;color:#9299a5;font-size:12px}.talario-cabinet .table{margin:0;border:1px solid #edf0f4;border-radius:9px}.talario-cabinet .table th{color:#818a97;font-size:12px;font-weight:600}.talario-cabinet .table td{color:#4b5563}.talario-cabinet .no-items{padding:25px 0;color:#818896}.talario-message-card{max-width:620px}.talario-message-card p{margin:0 0 16px}.talario-document-list td{padding:15px 17px}
@media(max-width:980px){.talario-stats,.talario-class-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(max-width:600px){.talario-dashboard__header{align-items:stretch;flex-direction:column}.talario-stats,.talario-stats--two,.talario-class-grid{grid-template-columns:1fr}.talario-class-card__actions{flex-direction:column}.talario-partner-identity{display:none!important}.talario-notification{flex-direction:column}.talario-cabinet .control-label{float:none;width:auto;text-align:left}.talario-cabinet .controls{margin-left:0}}
.cs-main-menu,.cs-main-menu__header,.cs-main-menu__outer,.cs-main-menu__inner,.cs-main-menu__primary,.cs-main-menu__secondary{background:#f8f2e8!important}.cs-main-menu .main-menu-1__link--active,.cs-main-menu .main-menu-1__link--icon--active,.cs-main-menu .main-menu-2__link--active,.cs-main-menu .main-menu-3__link--active{background:#fff6df!important;color:#705d38!important;box-shadow:inset 3px 0 0 #f0c966!important}.cs-main-menu .main-menu-1__link--active .cs-icon__svg,.cs-main-menu .main-menu-1__link--active .main-menu-1__icon{color:#bf8d18!important}.cs-main-menu .main-menu-1__link:hover{background:#fcf7ee!important}

</style>
{/literal}
{/if}
{$talario_header_company_id = $runtime.company_id|default:$auth.company_id}
{if !$talario_header_company_id && $auth.user_id}
    {$talario_header_user = $auth.user_id|fn_get_user_info}
    {$talario_header_company_id = $talario_header_user.company_id}
{/if}
{if $talario_header_company_id}
    {$talario_header_company = $talario_header_company_id|fn_get_company_data}
{/if}
<div class="talario-partner-identity">
    <strong>{$talario_header_company.company|default:$talario_header_user.company|default:$user_info.company|default:"Партнёр"}</strong>
</div>
{literal}
<style>
/* Компактная шапка кабинета: одно название партнёра. */
html body.vendor-area #top_bar {
    display: block !important;
    min-height: 56px;
    background: #f8f2e8 !important;
    border-bottom: 1px solid #eee5d9 !important;
}
html body.vendor-area #top_bar .top-bar__inner {
    min-height: 56px;
    padding: 0 24px;
    background: #f8f2e8 !important;
}
html body.vendor-area #top_bar_left,
html body.vendor-area #top_bar_search {
    display: none !important;
}
html body.vendor-area #top_bar_right {
    display: flex !important;
    align-items: center;
    position: absolute;
    left: calc(var(--main-menu-width) + 24px);
    right: 24px;
    top: 0;
    bottom: 0;
    min-width: 0;
}
html body.vendor-area .talario-partner-identity {
    margin-right: auto !important;
    max-width: calc(100vw - var(--main-menu-width) - 190px);
}
html body.vendor-area .talario-partner-identity {
    display: flex !important;
    flex-direction: row !important;
    align-items: baseline !important;
    gap: 6px;
    min-width: 0 !important;
    margin: 0 14px 0 0 !important;
    color: #292621 !important;
}
html body.vendor-area .talario-partner-identity strong,
html body.vendor-area .talario-partner-identity span {
    max-width: none !important;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
html body.vendor-area .talario-partner-identity strong {
    font-size: 13px !important;
}
html body.vendor-area .talario-partner-identity span {
    color: #77736d !important;
    font-size: 12px !important;
}

/* Единая поверхность меню: правило находится вне ограничений контроллеров. */
html body.vendor-area {
    --cs-menu-sidebar-color: #f8f2e8 !important;
    --cs-menu-sidebar-bottom-toolbar-color: #f8f2e8 !important;
}
html body.vendor-area .cs-main-menu {
    --cs-menu-sidebar-color: #f8f2e8 !important;
    --cs-menu-sidebar-bottom-toolbar-color: #f8f2e8 !important;
    --cs-content-background: #f8f2e8 !important;
    background-color: #f8f2e8 !important;
}
html body.vendor-area .cs-main-menu,
html body.vendor-area .cs-main-menu__header,
html body.vendor-area .cs-main-menu__outer,
html body.vendor-area .cs-main-menu__inner,
html body.vendor-area .cs-main-menu__primary,
html body.vendor-area .cs-main-menu__secondary,
html body.vendor-area .cs-main-menu__bottom-toolbar {
    background: #f8f2e8 !important;
    background-image: none !important;
}
html body.vendor-area .cs-main-menu .logo-menu__btn,
html body.vendor-area .cs-main-menu .logo-menu__btn-inner {
    background: #f8f2e8 !important;
}
html body.vendor-area .cs-main-menu .logo-menu__logo,
html body.vendor-area .cs-main-menu .logo-menu__logo-short {
    mix-blend-mode: multiply;
}
</style>
{/literal}
