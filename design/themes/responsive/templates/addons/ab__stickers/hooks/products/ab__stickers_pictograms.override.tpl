{$ab_theme_name = fn_get_theme_path('[theme]')}

{if !in_array('ab_theme_name', ['abt__unitheme2', 'abt__youpitheme'])}
    {include file="addons/ab__stickers/views/ab__stickers/components/pictograms_wrapper.tpl" details_page=$details_page product=$product block=$block}
{/if}