<?php


function fn_settings_variants_addons_rus_ofd_ferma_setting_status()
{
    return fn_get_simple_statuses(STATUSES_ORDER);
}

function fn_settings_variants_addons_rus_ofd_ferma_setting_nalog()
{
    return array(
        'Common'                =>  __('rus_ofd_ferma.setting_nalog.common'),       
        'SimpleIn'              =>  __('rus_ofd_ferma.setting_nalog.simple_in'),
        'SimpleInOut'           =>  __('rus_ofd_ferma.setting_nalog.simple_in_out'),       
        'Unified'               =>  __('rus_ofd_ferma.setting_nalog.unified'),       
        'UnifiedAgricultural'   =>  __('rus_ofd_ferma.setting_nalog.unified_agricultural'),        
        'Patent'                =>  __('rus_ofd_ferma.setting_nalog.patent')        
    );
}

function fn_settings_variants_addons_rus_ofd_ferma_setting_nds()
{
    return array(
        'Vat0'      			=>  __('rus_ofd_ferma.setting_nds.vat0'),       
        'Vat10'     			=>  __('rus_ofd_ferma.setting_nds.vat10'),
        'Vat18'     			=>  __('rus_ofd_ferma.setting_nds.vat18'),       
        'CalculatedVat10110'    =>  __('rus_ofd_ferma.setting_nds.vat10110'),       
        'CalculatedVat18118'    =>  __('rus_ofd_ferma.setting_nds.vat18118'),       
    );
}