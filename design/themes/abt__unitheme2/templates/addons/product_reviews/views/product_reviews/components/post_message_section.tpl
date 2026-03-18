{*
    $message_title
    $message_body
*}

{if $message_body}

    <div class="ty-product-review-post-message-section ty-dl" data-ca-product-review="postMessageSection">

        {if $message_body}
            <span class="ty-product-review-post-message-section__body">
                {if $message_title}
                    <span class="ty-product-review-post-message-section__title  ty-strong ty-float-left">{$message_title nofilter}</span>
                {/if}

                {include file="common/content_more.tpl" text=$message_body|escape|nl2br}
            </span>
        {/if}

    </div>
{/if}
