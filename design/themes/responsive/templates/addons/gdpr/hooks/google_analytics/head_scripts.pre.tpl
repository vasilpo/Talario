{if $addons.gdpr.gdpr_cookie_consent !== "Addons\\Gdpr\\CookiesPolicyManager::COOKIE_POLICY_NONE"|enum}
    {$load_script = false scope = parent}

    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('consent', 'default', {
            ad_storage: 'denied',
            analytics_storage: 'denied',
            ad_user_data: 'denied',
            ad_personalization: 'denied'
        });
        gtag('set', 'ads_data_redaction', true);

        function loadGtagScript() {
            $.getScript("https://www.googletagmanager.com/gtag/js?id={$addons.google_analytics.tracking_code}");
        }
    </script>
    <script type="text/plain" data-type="application/javascript" data-name="google-ads">
        loadGtagScript();

        gtag('consent', 'update', {
            ad_storage: 'granted',
            ad_user_data: 'granted',
            ad_personalization: 'granted'
        });
    </script>
    <script type="text/plain" data-type="application/javascript" data-name="google-analytics">
        loadGtagScript();

        gtag('consent', 'update', { analytics_storage: 'granted'});
    </script>
{/if}
