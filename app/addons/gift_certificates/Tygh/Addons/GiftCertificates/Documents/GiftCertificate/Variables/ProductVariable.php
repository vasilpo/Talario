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


namespace Tygh\Addons\GiftCertificates\Documents\GiftCertificate\Variables;

use Tygh\SmartyEngine\Core;
use Tygh\Template\IVariable;
use Tygh\Template\Snippet\Table\ItemContext;
use Tygh\Tools\Formatter;

/**
 * Class ProductVariable
 * @package Tygh\Addons\GiftCertificates\Documents\GiftCertificate\Variables
 */
class ProductVariable implements IVariable
{
    public $product_id;
    public $name;
    public $url;
    public $amount;
    public $product_options_value;
    public $product_options;
    public $options;

    /** @var ItemContext */
    protected $context;

    /**
     * ProductVariable constructor.
     *
     * @param ItemContext   $context    Instance of column context.
     * @param array         $config     Config data.
     * @param Core          $view       Instance of smarty renderer.
     * @param Formatter     $formatter  Instance of formatter.
     */
    public function __construct(ItemContext $context, array $config, Core $view, Formatter $formatter)
    {
        $product = $context->getItem();

        $this->context = $context;
        $this->product_id = $product['product_id'];
        $this->name = $product['product'];
        $this->amount = $product['amount'];
        $this->product_options = isset($product['product_options']) ? $product['product_options'] : array();
        $this->product_options_value = isset($product['product_options_value']) ? $product['product_options_value'] : array();

        if ($this->product_options_value) {
            $view->assign('product_options', $this->product_options_value);
            $this->options = $view->displayMail('common/options_info.tpl', false, 'A', null, $this->context->getLangCode());
        }

        $url = "products.view?product_id={$product['product_id']}";

        if (fn_allowed_for('ULTIMATE') && !empty($product['company_id'])) {
            $url .= "&company_id={$product['company_id']}";
        }

        $this->url = fn_url($url, 'C', 'http');
    }
}