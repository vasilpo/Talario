{if $search.talario_search_relevance_fallback}
    <div class="ty-alert ty-alert--notice">
        {__("talario_search_relevance.fallback_notice")}
    </div>
{/if}

{if $talario_related_lessons}
    <section class="ty-search-related" aria-label="Похожие направления">
        <h2 class="ty-subheader">Похожие направления</h2>
        <p>Другие единоборства, которые могут вам подойти. Это отдельные предложения, не результаты точного запроса.</p>
        <ul class="ty-simple-list">
            {foreach $talario_related_lessons as $lesson}
                <li><a href="{"products.view?product_id=`$lesson.product_id`"|fn_url}">{$lesson.product|escape}</a></li>
            {/foreach}
        </ul>
    </section>
{/if}
