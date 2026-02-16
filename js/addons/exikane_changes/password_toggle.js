(function (_, $) {
    'use strict';

    function getLabels() {
        return {
            show: _.tr('exikane_changes.show_password') || 'Show password',
            hide: _.tr('exikane_changes.hide_password') || 'Hide password'
        };
    }

    function wrapInput($input) {
        if ($input.data('exikanePasswordToggle')) {
            return;
        }

        $input.data('exikanePasswordToggle', true);
        $input.wrap('<div class="exikane-password-wrap"></div>');

        var labels = getLabels();
        var $toggle = $(
            '<button type="button" class="exikane-password-toggle" aria-label="' + labels.show + '" aria-pressed="false">' +
                '<i class="ty-icon-eye-open"></i>' +
            '</button>'
        );

        $input.after($toggle);

        $toggle.on('click', function () {
            var isPassword = $input.attr('type') === 'password';
            var iconClass = isPassword ? 'ty-icon-eye-close' : 'ty-icon-eye-open';
            var label = isPassword ? labels.hide : labels.show;

            $input.attr('type', isPassword ? 'text' : 'password');
            $toggle.attr('aria-pressed', isPassword ? 'true' : 'false');
            $toggle.attr('aria-label', label);
            $toggle.find('i').attr('class', iconClass);
        });
    }

    function init(context) {
        var $context = $(context || document);

        $context.find('form').each(function () {
            var $form = $(this);
            var hasLogin = $form.find('input[name="user_login"]').length > 0;
            var isOrderRegister = $form.attr('name') === 'order_register_form';
            var isProfileRegister = $form.attr('name') === 'profiles_register_form';
            var isProfileUpdate = $form.attr('name') === 'profile_form';

            if (!hasLogin && !isOrderRegister && !isProfileRegister && !isProfileUpdate) {
                return;
            }

            $form.find('input[type="password"]').each(function () {
                wrapInput($(this));
            });
        });
    }

    $.ceEvent('on', 'ce.commoninit', function (context) {
        init(context);
    });
}(Tygh, Tygh.$));
