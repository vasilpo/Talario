(function (_, $) {
    function isHomePage() {
        return _.abt__ut2 && _.abt__ut2.controller === 'index' && _.abt__ut2.mode === 'index';
    }

    function resetStickySearchInput($input) {
        if (!$input.length) {
            return;
        }

        var input = $input.get(0);

        // Keep the field in active mode so CS-Cart submits `q`, not `hint_q`.
        $input.removeClass('cm-hint').addClass('cm-hint-focused').val('');

        if (input && input.defaultValue === $input.ceHint('_get_hint_value')) {
            input.defaultValue = '';
        }

        if ((input.name || '').indexOf('hint_') === 0) {
            input.name = input.name.replace(/^hint_/, '');
        }
    }

    function toggleStickySearch($btn) {
        var btnId = $btn.prop('id') || '';
        var prefixMatch = btnId.match(/^(on_|off_|sw_)/);
        var prefix = prefixMatch ? prefixMatch[0] : '';
        var id = btnId.replace(/^(on_|off_|sw_)/, '');
        var $container = id ? $('#' + id) : $();

        if (!$container.length) {
            return;
        }

        var flag = prefix === 'on_' ? false : (prefix === 'off_' ? true : $container.is(':visible'));

        $container.removeClass('hidden');
        $container.toggleBy(flag);

        if ($.ceEvent) {
            $.ceEvent('trigger', 'ce.switch_' + id, [flag]);
        }

        $btn.trigger('ce:combination:switch', [$container, flag]);

        $('#on_' + id).removeClass('hidden').toggleBy(!flag);
        $('#off_' + id).removeClass('hidden').toggleBy(flag);

        if (!flag) {
            var $input = $container.find('input.ty-search-block__input').first();
            if ($input.length) {
                // Always reset the mobile sticky search input on open
                // to avoid persisting the previous query across toggles/page reloads.
                resetStickySearchInput($input);
                setTimeout(function () {
                    $input.trigger('focus');
                }, 0);
            }
        }
    }

    function getSearchButton(target) {
        var selector = '.ut2-sticky-panel__item .ut2-btn-search.cm-abt--ut2-toggle-scroll';
        if (target.closest) {
            return target.closest(selector);
        }
        return $(target).closest(selector).get(0);
    }

    function onSearchClick(e) {
        var btn = getSearchButton(e.target);
        if (!btn) {
            return;
        }
        if (isHomePage()) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();
        if (e.stopImmediatePropagation) {
            e.stopImmediatePropagation();
        }

        toggleStickySearch($(btn));
    }

    $(function () {
        // Capture-phase handler to bypass stopPropagation on the search button.
        document.addEventListener('click', onSearchClick, true);
    });
}(Tygh, Tygh.$));
