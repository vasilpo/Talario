{if $talario_article_marketplace_pilot}
    <section class="talario-article-marketplace" data-talario-article-marketplace-block>
        <div class="talario-article-marketplace__intro">
            <div>
                <h2 class="talario-article-marketplace__title">{$talario_article_marketplace_pilot.title}</h2>
                <p class="talario-article-marketplace__text">{$talario_article_marketplace_pilot.text}</p>
            </div>
            <a class="ty-btn ty-btn__primary"
               href="{$talario_article_marketplace_pilot.target}"
               data-talario-article-marketplace="inline">Посмотреть занятия</a>
        </div>

        {if $talario_article_marketplace_products}
            <div class="talario-article-marketplace__cards">
                {foreach $talario_article_marketplace_products as $product}
                    <a class="talario-article-marketplace__card"
                       href="{"products.view?product_id="|cat:$product.product_id|fn_url}"
                       data-talario-article-marketplace="cards">
                        <div class="talario-article-marketplace__card-title">{$product.product}</div>
                        {if $product.price !== null}
                            <div class="talario-article-marketplace__price">
                                {$product.price|format_price:$currencies.$secondary_currency nofilter}
                            </div>
                        {/if}
                        <span class="talario-article-marketplace__more">Подробнее →</span>
                    </a>
                {/foreach}
            </div>
        {/if}
    </section>

    <style>
        .talario-article-marketplace {
            margin: 28px 0 8px;
            padding: 22px;
            border: 1px solid #e8e3d7;
            border-radius: 16px;
            background: #fffdf8;
        }
        .talario-article-marketplace__intro {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }
        .talario-article-marketplace__title {
            margin: 0 0 6px;
            font-size: 22px;
            line-height: 1.25;
        }
        .talario-article-marketplace__text {
            margin: 0;
            color: #5d5d5d;
        }
        .talario-article-marketplace__cards {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 10px;
            margin-top: 18px;
        }
        .talario-article-marketplace__card {
            display: flex;
            min-height: 116px;
            padding: 14px;
            border: 1px solid #ece8df;
            border-radius: 12px;
            background: #fff;
            color: inherit;
            flex-direction: column;
            text-decoration: none;
        }
        .talario-article-marketplace__card-title {
            font-weight: 600;
            line-height: 1.3;
        }
        .talario-article-marketplace__price {
            margin-top: 8px;
            font-weight: 700;
        }
        .talario-article-marketplace__more {
            margin-top: auto;
            padding-top: 10px;
            color: #8a6a13;
        }
        @media (max-width: 767px) {
            .talario-article-marketplace {
                padding: 16px;
            }
            .talario-article-marketplace__intro {
                align-items: stretch;
                flex-direction: column;
            }
            .talario-article-marketplace__cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
{/if}
