<!DOCTYPE html>
<html lang="{$smarty.const.CART_LANGUAGE}" dir="{$language_direction}" class="ty-pwa-offline__html">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{__("pwa.offline.window_title")}</title>

        <style>
            :root {
                --ty-color-slate-800: #1d293d;
                --ty-color-gray-500: #6a7282;
                --ty-color-white: #fff;
                --ty-spacing: .25rem;
                --ty-text-sm: .875rem;
                --ty-text-sm--line-height: calc(1.25/.875);
                --ty-text-xl: 1.25rem;
                --ty-text-7xl: 4.5rem;
                --ty-text-7xl--line-height: 1;
                --ty-font-weight-medium: 500;
                --ty-font-weight-semibold: 600;
                --ty-radius-md: .375rem;
                --ty-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                --ty-outline-style: solid;
            }

            *,
            :after,
            :before {
                box-sizing: border-box;
                margin: 0;
                border: 0 solid;
            }

            .ty-pwa-offline__html {
                height: 100%;
                font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            }

            .ty-pwa-offline__body {
                height: 100%;
            }

            .ty-pwa-offline-main {
                display: grid;
                min-height: 100%;
                place-items: center;
                background-color: var(--ty-color-white);
            }

            .ty-pwa-offline-main__inner {
                text-align: center;
            }

            .ty-pwa-offline__title {
                margin-top: calc(var(--ty-spacing) * 4);
                font-size: var(--ty-text-5xl);
                line-height: var(--ty-text-5xl--line-height);
                font-weight: var(--ty-font-weight-semibold);
                letter-spacing: var(--tracking-tight);
                text-wrap: balance;
                color: var(--ty-color-gray-900);
            }

            @media (min-width: 40rem) {
                .ty-pwa-offline__title {
                    font-size: var(--ty-text-7xl);
                    line-height: var(--ty-text-7xl--line-height);
                }
            }

            .ty-pwa-offline__description {
                margin-top: calc(var(--ty-spacing) * 6);
                font-size: var(--ty-text-lg);
                line-height: var(--ty-text-lg--line-height);
                font-weight: var(--ty-font-weight-medium);
                text-wrap: pretty;
                color: var(--ty-color-gray-500);
                font-size: var(--ty-text-xl);
                line-height: calc(var(--ty-spacing) * 8);
            }

            .ty-pwa-offline__button-wrapper {
                margin-top: calc(var(--ty-spacing) * 10);
                display: flex;
                align-items: center;
                justify-content: center;
                column-gap: calc(var(--ty-spacing) * 6);
            }

            .ty-pwa-offline__button {
                font: inherit;
                border-radius: var(--ty-radius-md);
                background-color: var(--ty-color-slate-800);
                padding-inline: calc(var(--ty-spacing) * 3.5);
                padding-block: calc(var(--ty-spacing) * 2.5);
                font-size: var(--ty-text-sm);
                line-height: var(--ty-text-sm--line-height);
                font-weight: var(--ty-font-weight-semibold);
                color: var(--ty-color-white);
                box-shadow: var(--ty-shadow); 
            }

            .ty-pwa-offline__button:hover {
                background-color: var(--ty-color-slate-800);
            }

            .ty-pwa-offline__button:focus-visible {
                outline-style: var(--ty-outline-style);
                outline-width: 2px;
                outline-offset: 2px;
                outline-color: var(--ty-color-slate-800);
            }

        </style>
    </head>
    <body class="ty-pwa-offline__body">
        <main class="ty-pwa-offline-main">
            <div class="ty-pwa-offline-main__inner">
                <h1 class="ty-pwa-offline__title">{__("pwa.offline.page_title")}</h1>
                <p class="ty-pwa-offline__description">{__("pwa.offline.page_description")}</p>
                <div class="ty-pwa-offline__button-wrapper">
                    <button type="button" class="ty-pwa-offline__button">{__("pwa.offline.reload_button")}</button>
                </div>
            </div>
        </main>

        <script data-no-defer>
            document.querySelector('button').addEventListener('click', () => {
                window.location.reload();
            });

            window.addEventListener('online', () => {
                window.location.reload();
            });

            async function checkNetworkAndReload() {
                try {
                    const response = await fetch('.');
                    if (response.status >= 200 && response.status < 500) {
                        window.location.reload();
                        return;
                    }
                } catch {
                }
                window.setTimeout(checkNetworkAndReload, 2500);
            }

            checkNetworkAndReload();
        </script>
    </body>
</html>