(function (_, $) {
    $.ceEvent('on', 'ce.commoninit', function() {
        const $input  = document.getElementById('search_input'),
              $button = document.getElementById('on_dropdown_sticky_item_search');

        if (!$input || !$button) {
            return;
        }

        $button.addEventListener('click', (event) => {
            event.stopPropagation();
            event.preventDefault();
            event.stopImmediatePropagation();

            setTimeout(() => {
                $input.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 10);
        });
    });
})(Tygh, Tygh.$);