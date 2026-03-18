<script>
    (function (_, $) {
        const ANIMATION_DURATION = 300; // Duration of scroll animation in ms
        const RESIZE_DEBOUNCE = 200;   // Debounce delay for resize event in ms
        const DEFAULT_ELEMENTS_SCROLL = 1; // Default number of elements to scroll

        var elementsScroll = Number.isInteger(parseInt('{$elements_to_scroll}'))
            ? parseInt('{$elements_to_scroll}')
            : DEFAULT_ELEMENTS_SCROLL;

        function simpleSlider(context) {
            var slider = context.attr('id') === '{$block_id}' ? context : context.find('#{$block_id}');

            if (!slider.length) {
                return null;
            }

            const isRTL = $('html').attr('dir') === 'rtl';
            const $scrollContainer = $('.ut2-scroll-content', slider);
            const $scrollLeft = $('.ut2-scroll-left', slider);
            const $scrollRight = $('.ut2-scroll-right', slider);
            const $items = $('.ut2-scroll-item', slider).filter(':not(.owl-cloned)');

            if (!$items.length) {
                $scrollLeft.hide();
                $scrollRight.hide();
                return null;
            }

            let isScrolling = false;
            let currentIndex = 0; // Tracks the index of the first visible item
            let itemWidths = [];  // Cached item widths including margins
            let padding = { left: 0, right: 0 }; // Cached container padding

            function getContainerPadding() {
                const style = getComputedStyle($scrollContainer[0]);
                return {
                    left: parseFloat(style.paddingLeft) || 0,
                    right: parseFloat(style.paddingRight) || 0
                };
            }

            function cacheItemWidths() {
                itemWidths = $items.toArray().map(item => {
                    const rect = item.getBoundingClientRect();
                    const style = getComputedStyle(item);
                    const width = rect.width; // Use bounding rect for accurate width
                    const margin = parseFloat(isRTL ? style.marginLeft : style.marginRight) || 0;
                    return width + margin;
                });
            }

            function getItemFullWidth(index) {
                return index < itemWidths.length ? itemWidths[index] : 0;
            }

            function updateCurrentIndex() {
                const scrollLeft = $scrollContainer.scrollLeft();
                let cumulativeWidth = 0;
                currentIndex = 0;

                for (let i = 0; i < itemWidths.length; i++) {
                    cumulativeWidth += itemWidths[i];
                    if (isRTL) {
                        if (-scrollLeft < cumulativeWidth) {
                            currentIndex = i;
                            break;
                        }
                    } else {
                        if (scrollLeft < cumulativeWidth) {
                            currentIndex = i;
                            break;
                        }
                    }
                }
            }

            function updateNavigation() {
                const scrollLeft = $scrollContainer.scrollLeft();
                const scrollWidth = $scrollContainer[0].scrollWidth;
                const clientWidth = $scrollContainer[0].clientWidth;
                const maxScrollLeft = Math.max(0, scrollWidth - clientWidth);

                let normalizedScrollLeft = scrollLeft;
                if (isRTL) {
                    normalizedScrollLeft = Math.max(-maxScrollLeft, Math.min(0, scrollLeft));
                } else {
                    normalizedScrollLeft = Math.max(0, Math.min(maxScrollLeft, scrollLeft));
                }

                if (normalizedScrollLeft !== scrollLeft) {
                    $scrollContainer.scrollLeft(normalizedScrollLeft);
                }

                if (isRTL) {
                    const atStart = normalizedScrollLeft >= 0;
                    const atEnd = Math.abs(normalizedScrollLeft) >= maxScrollLeft - 1;
                    $scrollLeft.toggle(!atStart);  // Show left button (scroll right) if not at start
                    $scrollRight.toggle(!atEnd);   // Show right button (scroll left) if not at end
                } else {
                    const atStart = normalizedScrollLeft <= 0;
                    const atEnd = normalizedScrollLeft >= maxScrollLeft - 1;
                    $scrollLeft.toggle(!atStart);  // Show left button if not at start
                    $scrollRight.toggle(!atEnd);   // Show right button if not at end
                }
            }

            function scrollByStep(direction) {
                if (isScrolling) return;

                isScrolling = true;
                cacheItemWidths();
                const scrollWidth = $scrollContainer[0].scrollWidth;
                const clientWidth = $scrollContainer[0].clientWidth;
                const maxScrollLeft = Math.max(0, scrollWidth - clientWidth);
                let targetIndex;

                if (isRTL) {
                    targetIndex = direction === 'right'
                        ? Math.min(currentIndex + elementsScroll, $items.length - 1)
                        : Math.max(currentIndex - elementsScroll, 0);
                } else {
                    targetIndex = direction === 'right'
                        ? Math.min(currentIndex + elementsScroll, $items.length - 1)
                        : Math.max(currentIndex - elementsScroll, 0);
                }

                let targetScroll = 0;
                for (let i = 0; i < targetIndex; i++) {
                    targetScroll += getItemFullWidth(i);
                }

                if (isRTL) {
                    targetScroll = -targetScroll;
                    if (Math.abs(targetScroll) > maxScrollLeft) {
                        targetScroll = -maxScrollLeft; // Cap at the end
                    }
                    targetScroll = Math.max(-maxScrollLeft, Math.min(0, targetScroll));
                } else {
                    if (targetScroll > maxScrollLeft) {
                        targetScroll = maxScrollLeft; // Cap at the end
                    }
                    targetScroll = Math.max(0, Math.min(maxScrollLeft, targetScroll));
                }

                if (maxScrollLeft === 0) {
                    isScrolling = false;
                    return;
                }

                $scrollContainer.stop().animate({ scrollLeft: targetScroll }, ANIMATION_DURATION, () => {
                    currentIndex = targetIndex;
                    updateNavigation();
                    isScrolling = false;
                });
            }

            padding = getContainerPadding();
            cacheItemWidths();

            $scrollRight.off('.simpleSlider');
            $scrollLeft.off('.simpleSlider');
            $scrollContainer.off('.simpleSlider');
            $(window).off('.simpleSlider');

            $scrollRight.on('click.simpleSlider', function (e) {
                e.preventDefault();
                scrollByStep('right');
            });

            $scrollLeft.on('click.simpleSlider', function (e) {
                e.preventDefault();
                scrollByStep('left');
            });

            $scrollContainer.on('scroll.simpleSlider', function () {
                if (!isScrolling) {
                    cacheItemWidths(); // Recalculate widths to account for OWL changes
                    updateCurrentIndex();
                    updateNavigation();
                }
            });

            let resizeTimeout;
            $(window).on('resize.simpleSlider', () => {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(() => {
                    padding = getContainerPadding();
                    cacheItemWidths();
                    updateCurrentIndex();
                    updateNavigation();
                }, RESIZE_DEBOUNCE);
            });

            const $owlCarousel = $scrollContainer.closest('.owl-carousel');
            if ($owlCarousel.length) {
                $owlCarousel.on('translated.owl.carousel.simpleSlider', () => {
                    cacheItemWidths();
                    updateCurrentIndex();
                    updateNavigation();
                });
            }

            const observer = new MutationObserver(() => {
                cacheItemWidths();
                updateCurrentIndex();
                updateNavigation();
            });
            observer.observe($scrollContainer[0], { childList: true, subtree: true });

            setTimeout(() => {
                cacheItemWidths();
                updateCurrentIndex();
                updateNavigation();
            }, 100);

            return { id: slider.attr('id') };
        }

        $.ceEvent('on', 'ce.commoninit', function (context) {
            let slider = simpleSlider(context);
            if (slider) {
                $.ceEvent('on', 'ce.ajaxdone', function (elms, scripts, params, response) {
                    if (response?.html?.[slider.id]) {
                        slider = simpleSlider($('#' + slider.id));
                    }
                });
            }
        });

    }(Tygh, Tygh.$));
</script>