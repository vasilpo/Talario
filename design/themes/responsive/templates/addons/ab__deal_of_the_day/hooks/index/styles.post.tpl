{style src="addons/ab__deal_of_the_day/vars.less"}
{style src="addons/ab__deal_of_the_day/styles.less"}
{if $language_direction == 'rtl'}{style src="addons/ab__deal_of_the_day/rtl.less"}{/if}

{if $addons.ab__deal_of_the_day.countdown_type === "flipclock"}
    {style src="addons/ab__deal_of_the_day/flipclock.less"}
{else}
    {style src="addons/ab__deal_of_the_day/jscounter.less"}
{/if}