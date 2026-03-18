<?php
/***************************************************************************
*                                                                          *
*   © 2012 ООО "Эком Системы"                                              *
*                                                                          *
* Это коммерческое программное обеспечение. Только пользователи, которые   *
* приобрели действующую лицензию и согласились с условиями лицензионного   *
* соглашения, могут устанавливать и использовать эту программу.            *
*                                                                          *
****************************************************************************
* ПОЖАЛУЙСТА, ВНИМАТЕЛЬНО ПРОЧТИТЕ ПОЛНЫЙ ТЕКСТ ЛИЦЕНЗИОННОГО СОГЛАШЕНИЯ   *
* В ФАЙЛЕ "copyright.txt", ПРЕДОСТАВЛЕННОМ ВМЕСТЕ С ЭТИМ ДИСТРИБУТИВОМ.    *
***************************************************************************/

//phpcs:ignore
use Smarty\Exception as SmartyException;
use Smarty\Template;
use Tygh\Registry;

defined('BOOTSTRAP') or die('Access denied');

/**
 * @param array<string, string> $params   Block params
 * @param string                $content  Block content
 * @param Template              $template Smarty template
 *
 * @throws Exception       Internal smarty rendering error.
 * @throws SmartyException If unable to load template.
 *
 * @return string
 */
function smarty_component_vendor_debt_payout_select_grace_period_to_refill_balance(array $params, $content, Template $template)
{
    if (Registry::get('addons.vendor_debt_payout.global_grace_period_to_refill_balance') !== null) {
        return '';
    }
    $variants = [];
    $global_value = sprintf('%s %s', Registry::ifGet('addons.vendor_debt_payout.default_grace_period_to_refill_balance', 0), __('vendor_debt_payout.day_or_days'));
    $value = (!isset($params['value']) || $params['value'] === 'default') ? 'default' : (int) $params['value'];
    $custom_input_styles = isset($params['custom_input_styles']) ? (string) $params['custom_input_styles'] : '';
    $custom_input_attributes = isset($params['custom_input_attributes']) ? (array) $params['custom_input_attributes'] : '';

    if ($value !== 'default') {
        $value_name = sprintf('%s %s', $value, __('vendor_debt_payout.day_or_days'));

        $variants[] = [
            'type'  => 'variant',
            'value' => $value,
            'name'  => $value_name,
        ];
    }

    $variants[] = [
        'type'  => 'inheritance',
        'value' => 'default',
        'name'  => __('default_custom.global', ['[name]' => $global_value])
    ];
    if (fn_check_view_permissions('addons.manage')) {
        $variants[] = [
            'type'  => 'inheritance_edit',
            'value' => null,
            'name'  => __('default_custom.edit_global', ['[name]' => $global_value]),
            'url'   => 'addons.update&addon=vendor_debt_payout'
        ];
    }

    $template->assign([
        'component_id'            => 'lowers_allowed_balance',
        'name'                    => isset($params['input_name']) ? $params['input_name'] : 'plan_data[grace_period_to_refill_balance]',
        'variants'                => $variants,
        'value'                   => $value,
        'show_custom'             => true,
        'custom_input_styles'     => $custom_input_styles,
        'custom_input_attributes' => $custom_input_attributes
    ]);

    /** @var \Tygh\SmartyEngine\Core $smarty */
    $smarty = $template->getSmarty();

    return $smarty->fetch('components/default_custom.tpl', null, null, $template);
}
