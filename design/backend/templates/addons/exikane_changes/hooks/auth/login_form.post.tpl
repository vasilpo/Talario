{style src="addons/exikane_changes/styles.less"}
<script>
    (function (_, $) {
        $.ceEvent('on', 'ce.commoninit', function (context) {
            var $context = $(context || document);
            var $input = $context.find('form[name="main_login_form"] #password');

            if (!$input.length || $input.data('exikaneAdminToggle')) {
                return;
            }

            $input.data('exikaneAdminToggle', true);

            if (!$input.closest('.exikane-password-wrap').length) {
                $input.wrap('<div class="exikane-password-wrap"></div>');
            }

            var $wrapper = $input.closest('.exikane-password-wrap');
            if ($wrapper.find('> .exikane-password-toggle').length) {
                return;
            }

            var labels = {
                show: '{__("exikane_changes.show_password")|escape:"javascript"}',
                hide: '{__("exikane_changes.hide_password")|escape:"javascript"}'
            };

            var $toggle = $(
                '<button type="button" class="exikane-password-toggle" aria-label="' + labels.show + '" aria-pressed="false">' +
                    '<i class="icon-eye-open"></i>' +
                '</button>'
            );

            $input.after($toggle);

            $toggle.on('click', function () {
                var isPassword = $input.attr('type') === 'password';
                var iconClass = isPassword ? 'icon-eye-close' : 'icon-eye-open';
                var label = isPassword ? labels.hide : labels.show;

                $input.attr('type', isPassword ? 'text' : 'password');
                $toggle.attr('aria-pressed', isPassword ? 'true' : 'false');
                $toggle.attr('aria-label', label);
                $toggle.find('i').attr('class', iconClass);
            });
        });
    }(Tygh, Tygh.$));
</script>
