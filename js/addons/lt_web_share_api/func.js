(function (_, $) {
    var SHARE_TRIGGER_SELECTOR = '[data-ca-web-share="trigger"]';

    function showNotification(type, message) {
        $.ceNotification('show', {
            title: '',
            message: message,
            type: type,
            message_state: 'I'
        });
    }

    function showCopySuccessNotification() {
        showNotification('N', _.tr('lt_web_share_api.link_copied'));
    }

    function showCopyFailedNotification() {
        showNotification('E', _.tr('lt_web_share_api.copy_failed'));
    }

    function copyTextFallback(text) {
        var $temporary_input = $('<input type="text" readonly />');
        var is_copied = false;

        // Use a hidden input to preserve copy fallback for browsers without Clipboard API.
        $temporary_input.css({
            position: 'fixed',
            left: '-9999px',
            top: '-9999px'
        });

        $('body').append($temporary_input);
        $temporary_input.val(text).trigger('focus').trigger('select');

        try {
            is_copied = document.execCommand('copy');
        } catch (error) {
            is_copied = false;
        }

        $temporary_input.remove();

        if (is_copied) {
            showCopySuccessNotification();
        } else {
            showCopyFailedNotification();
        }
    }

    function copyUrlToClipboard(url) {
        if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
            navigator.clipboard.writeText(url).then(function () {
                showCopySuccessNotification();
            }).catch(function () {
                copyTextFallback(url);
            });

            return;
        }

        copyTextFallback(url);
    }

    function shareUrl(url) {
        if (navigator.share && typeof navigator.share === 'function') {
            navigator.share({
                url: url
            }).catch(function (error) {
                // Ignore user-initiated cancel and fall back only on real share failures.
                if (error && error.name === 'AbortError') {
                    return;
                }

                copyUrlToClipboard(url);
            });

            return;
        }

        copyUrlToClipboard(url);
    }

    function onShareClick(event) {
        event.preventDefault();
        shareUrl(window.location.href);
    }

    $.ceEvent('on', 'ce.commoninit', function (context) {
        $(SHARE_TRIGGER_SELECTOR, context)
            .filter(':not(.cm-lt-web-share-api-ready)')
            .addClass('cm-lt-web-share-api-ready')
            .on('click', onShareClick);
    });
})(Tygh, Tygh.$);
