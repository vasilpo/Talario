{capture name="mainbox"}
{include file="addons/talario_vendor_cabinet/components/white_shell.tpl"}
<div class="talario-cabinet">
    <div class="talario-dashboard__header">
        <nav class="talario-filters" aria-label="{__("talario_vendor_cabinet.filters")}">
            <a class="btn {if $talario_filter === "all"}btn-primary{/if}" href="{"talario_classes.manage"|fn_url}">{__("all")}</a>
            <a class="btn {if $talario_filter === "active"}btn-primary{/if}" href="{"talario_classes.manage?talario_status=active"|fn_url}">{__("talario_vendor_cabinet.published")}</a>
            <a class="btn {if $talario_filter === "pending"}btn-primary{/if}" href="{"talario_classes.manage?talario_status=pending"|fn_url}">{__("talario_vendor_cabinet.pending")}</a>
            <a class="btn {if $talario_filter === "disabled"}btn-primary{/if}" href="{"talario_classes.manage?talario_status=disabled"|fn_url}">{__("talario_vendor_cabinet.disabled_classes")}</a>
        </nav>
        <a class="btn btn-primary btn-large" href="{"talario_classes.add"|fn_url}">+ {__("talario_vendor_cabinet.add_class")}</a>
    </div>
    {if $talario_products}
        <div class="talario-class-grid">
        {foreach $talario_products as $product}
            <article class="talario-class-card">
                <div class="talario-class-card__image">
                    {include file="common/image.tpl" image=$product.main_pair.icon|default:$product.main_pair.detailed image_width=320 image_height=200 alt=$product.product}
                </div>
                <div class="talario-class-card__body">
                    <div class="talario-class-card__heading">
                        <h3>{$product.product}</h3>
                        <div class="dropdown">
                            <a class="dropdown-toggle talario-more" data-toggle="dropdown" aria-label="{__("more")}">•••</a>
                            <ul class="dropdown-menu pull-right">
                                <li><a href="{"talario_classes.update?product_id=`$product.product_id`"|fn_url}">{__("edit")}</a></li>
                                <li><a href="{"talario_classes.schedule?product_id=`$product.product_id`"|fn_url}">Расписание</a></li>
                            </ul>
                        </div>
                    </div>
                    {if $product.talario_age}<p class="muted">{__("talario_vendor_cabinet.age")}: {$product.talario_age}</p>{/if}
                    <div class="talario-class-card__meta">
                        <strong>{include file="common/price.tpl" value=$product.price}</strong>
                        <span class="label {if $product.status === "A"}label-success{elseif $product.status === "R"}label-warning{/if}">
                            {if $product.status === "A"}Опубликовано{elseif $product.status === "R"}На проверке{elseif $product.status === "H"}Черновик{elseif $product.status === "D"}Приостановлено{else}На доработке{/if}
                        </span>
                    </div>
                    {if $product.premoderation_reason}<div class="alert alert-warning">Комментарий Таларио: {$product.premoderation_reason}</div>{/if}
                    <div class="talario-class-card__actions">
                        <a class="btn btn-block" href="{"talario_classes.update?product_id=`$product.product_id`"|fn_url}">{__("edit")}</a>
                        <a class="btn btn-block" href="{"talario_classes.schedule?product_id=`$product.product_id`"|fn_url}">Расписание</a>
                    </div>
                </div>
            </article>
        {/foreach}
        </div>
        {include file="common/pagination.tpl" save_current_page=true search=$talario_search}
    {else}
        <div class="no-items">{__("no_data")}</div>
    {/if}
</div>
{/capture}
{include file="common/mainbox.tpl" title=__("talario_vendor_cabinet.classes") content=$smarty.capture.mainbox}
