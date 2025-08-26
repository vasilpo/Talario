<script>
    (function (_, $) {
        var elementsScroll = {$elements_to_scroll}; // Number of elements to scroll per click

        function simpleSlider(context) {
            var slider = context.attr('id') === '{$block_id}' ? context : context.find('#{$block_id}');

            if (slider.length) {
                this.id = slider.attr('id');
                const isRTL = $('html').attr('dir') === 'rtl';
                const $scrollContainer = $('.ut2-scroll-content', slider);
                const $scrollLeft = $('.ut2-scroll-left', slider);
                const $scrollRight = $('.ut2-scroll-right', slider);
                const $items = $('.ut2-scroll-item', slider);

                let isScrolling = false;
                let currentIndex = 0; // Tracks the index of the first visible item

                function getContainerPadding() {
                    const style = getComputedStyle($scrollContainer[0]);
                    return {
                        left: parseFloat(style.paddingLeft) || 0,
                        right: parseFloat(style.paddingRight) || 0
                    };
                }

                function getItemFullWidth(index) {
                    if (index >= $items.length) return 0;
                    const $item = $items.eq(index);
                    const style = getComputedStyle($item[0]);
                    const width = $item.outerWidth(false); // Width without margins
                    const margin = parseFloat(isRTL ? style.marginLeft : style.marginRight) || 0;
                    return width + margin;
                }

                let padding = getContainerPadding();

                function updateNavigation() {
                    const scrollLeft = $scrollContainer.scrollLeft();
                    const scrollWidth = $scrollContainer[0].scrollWidth;
                    const clientWidth = $scrollContainer[0].clientWidth;
                    const maxScrollLeft = Math.max(0, scrollWidth - clientWidth);

                    if (isRTL) {
                        const atStart = scrollLeft >= 0; // At the start (rightmost position)
                        const atEnd = Math.abs(scrollLeft) >= maxScrollLeft; // At the end (leftmost position)
                        $scrollLeft.toggle(!atStart);  // Left button (scroll right) visible if not at start
                        $scrollRight.toggle(!atEnd);   // Right button (scroll left) visible if not at end
                    } else {
                        const atStart = scrollLeft <= 0; // At the start (leftmost position)
                        const atEnd = scrollLeft >= maxScrollLeft; // At the end (rightmost position)
                        $scrollLeft.toggle(!atStart);  // Left button visible if not at start
                        $scrollRight.toggle(!atEnd);   // Right button visible if not at end
                    }
                }

                function scrollByStep(direction) {
                    if (isScrolling) return;

                    isScrolling = true;
                    const scrollWidth = $scrollContainer[0].scrollWidth;
                    const clientWidth = $scrollContainer[0].clientWidth;
                    const maxScrollLeft = Math.max(0, scrollWidth - clientWidth);
                    let targetIndex;

                    if (isRTL) {
                        if (direction === 'right') {
                            targetIndex = Math.min(currentIndex + elementsScroll, $items.length - 1); // Move left in RTL
                        } else {
                            targetIndex = Math.max(currentIndex - elementsScroll, 0);                // Move right in RTL
                        }
                    } else {
                        if (direction === 'right') {
                            targetIndex = Math.min(currentIndex + elementsScroll, $items.length - 1); // Move right in LTR
                        } else {
                            targetIndex = Math.max(currentIndex - elementsScroll, 0);                // Move left in LTR
                        }
                    }

                    let targetScroll = 0;
                    for (let i = 0; i < targetIndex; i++) {
                        targetScroll += getItemFullWidth(i);
                    }

                    if (isRTL) {
                        targetScroll = -targetScroll; // Invert for RTL
                        if (Math.abs(targetScroll) > maxScrollLeft) {
                            targetScroll = -maxScrollLeft; // Ensure full scroll to end
                        }
                        targetScroll = Math.max(-maxScrollLeft, Math.min(0, targetScroll));
                    } else {
                        if (targetScroll > maxScrollLeft) {
                            targetScroll = maxScrollLeft; // Ensure full scroll to end
                        }
                        targetScroll = Math.max(0, Math.min(maxScrollLeft, targetScroll));
                    }

                    if (maxScrollLeft === 0) {
                        isScrolling = false;
                        return;
                    }

                    $scrollContainer.animate({ scrollLeft: targetScroll }, 300, () => {
                        currentIndex = targetIndex; // Update the current index after scrolling
                        updateNavigation();
                        isScrolling = false;
                    });
                }

                $scrollRight.on('click', function (e) {
                    e.preventDefault();
                    scrollByStep('right');
                });

                $scrollLeft.on('click', function (e) {
                    e.preventDefault();
                    scrollByStep('left');
                });

                $scrollContainer.on('scroll', function () {
                    if (!isScrolling) {
                        updateNavigation();
                    }
                });

                let resizeTimeout;
                $(window).on('resize', () => {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(() => {
                        padding = getContainerPadding();
                        updateNavigation();
                    }, 200);
                });

                setTimeout(updateNavigation, 100);

                return this;
            }
        }

        $.ceEvent('on', 'ce.commoninit', function (context) {
            let slider = simpleSlider(context);
            if (slider) {
                $.ceEvent('on', 'ce.ajaxdone', function (...args) {
                    if (args[3]?.html?.[slider.id]) {
                        slider = simpleSlider($('#' + slider.id));
                    }
                });
            }
        });

    }(Tygh, Tygh.$));
</script>