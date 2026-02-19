(function (_, $) {
    'use strict';

    $(document).on('click', '.cm-exikane-password-toggle', function () {
        // Toggle only the password field that belongs to the clicked eye button.
        var $toggle = $(this);
        var $password_input = $toggle.siblings('[data-ca-password-toggle-field]').first();
        var show_label = _.tr('exikane_changes.show_password') || $toggle.attr('aria-label');
        var hide_label = _.tr('exikane_changes.hide_password') || $toggle.attr('aria-label');
        var is_password;

        if (!$password_input.length) {
            return;
        }

        is_password = $password_input.prop('type') === 'password';
        $password_input.prop('type', is_password ? 'text' : 'password');

        // Keep accessibility state in sync with the current visibility mode.
        $toggle.attr('aria-pressed', is_password ? 'true' : 'false');
        $toggle.attr('aria-label', is_password ? hide_label : show_label);
    });
}(Tygh, Tygh.$));
