{script src="js/addons/ab__deal_of_the_day/lib/flipclock.min.js"}
<script>
    (function (_, $) {
        $(document).ready(function() {
            var total_seconds = Number({$total_seconds});

            _.ab__dotd.clock_{$block.block_id} = $('#ab__deal_of_the_day_{$block.block_id}').FlipClock(total_seconds, {
                countdown: true,
                clockFace: (total_seconds > 86400) ? 'DailyCounter' : 'HourlyCounter',
                lang: {
                    'years'   : '{__('ab__dotd.countdown.simple.years')}',
                    'months'  : '{__('ab__dotd.countdown.simple.months')}',
                    'days'    : '{__('ab__dotd.countdown.simple.days')}',
                    'hours'   : '{__('ab__dotd.countdown.simple.hours')}',
                    'minutes' : '{__('ab__dotd.countdown.simple.minutes')}',
                    'seconds' : '{__('ab__dotd.countdown.simple.seconds')}'
                },
                callbacks: {
                    start: function() {
                        $(".flip-clock-divider", $(this.factory.$el)).wrap("<div class='ab-flip-clock-item'></div>");
                        var deviderWraps = $(".ab-flip-clock-item", $(this.factory.$el));
                        for (var i = 0; i < deviderWraps.length; i++) {
                            var count = 0;
                            while (!$(deviderWraps[i]).next().hasClass("ab-flip-clock-item")) {
                                var elem = $(deviderWraps[i]).next().detach();
                                $(deviderWraps[i]).append(elem);
                                if (i === deviderWraps.length-1 && ++count === 2) {
                                    $(".flip-clock-wrapper").addClass("wrapped");
                                    break;
                                }
                            }
                        }
                    }
                }
            });
        });
    })(Tygh, Tygh.$);
</script>