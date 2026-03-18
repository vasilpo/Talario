<script>
(function(_, $) {

    /* Do not put this code to document.ready, because it should be
       initialized first
    */
    $.ceRebuildStates('init', {
        default_country: '{$settings.Checkout.default_country|escape:javascript}',
        states: {$states|json_encode nofilter}
    });


    {literal}
    $.ceFormValidator('setZipcode', {
        US: {
            regexp: /^(\d{5})(-\d{4})?$/,
            format: '01342 (01342-5678)'
        },
        CA: {
            regexp: /^(\w{3} ?\w{3})$/,
            format: 'K1A OB1 (K1AOB1)'
        },
        RU: {
            regexp: /^(\d{6})?$/,
            format: '123456'
        }
    });
    {/literal}

    const selectors = [
        'input[name="user_data[firstname]"]',
        'input[name="user_data[lastname]"]'
    ];

    selectors.forEach(function (selector) {
        const $input = $(selector);
        const inputId = $input.attr('id');

        if (inputId) {
            const $label = $('label[for="' + inputId + '"]');
            $label.addClass('cm-text-validator');
        }
    });

    $(document).ready(function() {
        $.ceFormValidator('registerValidator', {
            class_name: 'cm-text-validator',
            message: _.tr('js_validator_not_valid_text_field'),
            func: function (id) {
                const value = $('#' + id).val().trim();

                if (value === '') return true;

                {literal}
                const regex = /^[\p{L}\p{M}\s'\-\d]+$/u;
                {/literal}

                return regex.test(value);
            }
        });
    });

}(Tygh, Tygh.$));
</script>
