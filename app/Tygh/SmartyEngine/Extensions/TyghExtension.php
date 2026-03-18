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

namespace Tygh\SmartyEngine\Extensions;

use Smarty\BlockHandler\BlockHandlerInterface;
use Smarty\Compile\Modifier\ModifierCompilerInterface;
use Smarty\Extension\Base;
//phpcs:ignore
use Smarty\Filter\FilterInterface;
use Smarty\FunctionHandler\FunctionHandlerInterface;
use Tygh\Embedded;
use Tygh\Enum\SiteArea;
use Tygh\Registry;
use Tygh\SmartyEngine\Blocks\Component;
use Tygh\SmartyEngine\Blocks\Hook;
use Tygh\SmartyEngine\Blocks\InlineScript;
use Tygh\SmartyEngine\Blocks\Notes;
use Tygh\SmartyEngine\Blocks\Scripts;
use Tygh\SmartyEngine\Blocks\Styles;
use Tygh\SmartyEngine\Filters\Output\EmbeddedUrl;
use Tygh\SmartyEngine\Filters\Output\LiveEditorWrapper;
use Tygh\SmartyEngine\Filters\Output\Script as OutputScript;
use Tygh\SmartyEngine\Filters\Output\SecurityHash;
use Tygh\SmartyEngine\Filters\Output\Sharing;
use Tygh\SmartyEngine\Filters\Output\TemplateIds;
use Tygh\SmartyEngine\Filters\Post\Translation;
use Tygh\SmartyEngine\Filters\Pre\Script as PreScript;
use Tygh\SmartyEngine\Filters\Pre\TemplateWrapper;
use Tygh\SmartyEngine\Functions\ArrayToFields;
use Tygh\SmartyEngine\Functions\EvalString;
use Tygh\SmartyEngine\Functions\IncludeExt;
use Tygh\SmartyEngine\Functions\LiveEdit;
use Tygh\SmartyEngine\Functions\RenderBlock;
use Tygh\SmartyEngine\Functions\RenderLocation;
use Tygh\SmartyEngine\Functions\SafeEvalString;
use Tygh\SmartyEngine\Functions\Script;
use Tygh\SmartyEngine\Functions\SetId;
use Tygh\SmartyEngine\Functions\Split;
use Tygh\SmartyEngine\Functions\Style;
use Tygh\SmartyEngine\Modifiers\Translate;

class TyghExtension extends Base
{
    /** @var ModifierCompilerInterface[]  */
    private $modifiers = [];

    /** @var FunctionHandlerInterface[] */
    private $function_handlers = [];

    /** @var string */
    private $area;

    //phpcs:ignore
    /** @var array */
    private $auth;

    /**
     * @param string $area Area
     *
     * //phpcs:ignore
     * @param array  $auth Auth
     */
    //phpcs:ignore
    public function __construct($area, $auth)
    {
        $this->area = $area;
        $this->auth = $auth;
    }

    /**
     * @param string $modifier Modifier
     */
    public function getModifierCompiler(string $modifier): ?ModifierCompilerInterface
    {
        if (isset($this->modifiers[$modifier])) {
            return $this->modifiers[$modifier];
        }

        switch ($modifier) {
            //phpcs:ignore
            case '__': $this->modifiers[$modifier] = new Translate(); break;
        }

        return $this->modifiers[$modifier] ?? null;
    }

    /**
     * @param string $modifier_name Modifier name
     *
     * @return callable|null
     */
    public function getModifierCallback(string $modifier_name)
    {
        $modifiers = [
            'count'            => 'smarty_modifier_count',
            'date_format'      => 'smarty_modifier_date_format',
            'empty_tabs'       => 'smarty_modifier_empty_tabs',
            'enum'             => 'smarty_modifier_enum',
            'format_price'     => 'smarty_modifier_format_price',
            'formatfilesize'   => 'smarty_modifier_formatfilesize',
            'in_array'         => 'smarty_modifier_in_array',
            'normalize_url'    => 'smarty_modifier_normalize_url',
            'puny_decode'      => 'smarty_modifier_puny_decode',
            'render_tag_attrs' => 'smarty_modifier_render_tag_attrs',
            'sanitize_html'    => 'smarty_modifier_sanitize_html',
            'sizeof'           => 'smarty_modifier_sizeof',
            'sort_by'          => 'smarty_modifier_sort_by',
            'to_json'          => 'smarty_modifier_to_json',
            'to_relative_url'  => 'smarty_modifier_to_relative_url',
            'trim'             => 'smarty_modifier_trim',
            'truncate'         => 'smarty_modifier_truncate',
            'unpuny'           => 'smarty_modifier_unpuny',
            'unset_key'        => 'smarty_modifier_unset_key',
        ];

        if (isset($modifiers[$modifier_name])) {
            return $modifiers[$modifier_name];
        }

        if (is_callable($modifier_name)) {
            return $modifier_name;
        }

        return null;
    }

    /**
     * @param string $function_name Function name
     */
    public function getFunctionHandler(string $function_name): ?FunctionHandlerInterface
    {
        if (isset($this->function_handlers[$function_name])) {
            return $this->function_handlers[$function_name];
        }

        //phpcs:disable
        switch ($function_name) {
            case 'array_to_fields':  $this->function_handlers[$function_name] = new ArrayToFields(); break;
            case 'eval_string':      $this->function_handlers[$function_name] = new EvalString(); break;
            case 'safe_eval_string': $this->function_handlers[$function_name] = new SafeEvalString(); break;
            case 'include_ext':      $this->function_handlers[$function_name] = new IncludeExt(); break;
            case 'live_edit':        $this->function_handlers[$function_name] = new LiveEdit(); break;
            case 'render_block':     $this->function_handlers[$function_name] = new RenderBlock(); break;
            case 'render_location':  $this->function_handlers[$function_name] = new RenderLocation(); break;
            case 'set_id':           $this->function_handlers[$function_name] = new SetId(); break;
            case 'split':            $this->function_handlers[$function_name] = new Split(); break;
            case 'style':            $this->function_handlers[$function_name] = new Style(); break;
            case 'script':           $this->function_handlers[$function_name] = new Script(); break;
        }
        //phpcs:enable

        return $this->function_handlers[$function_name] ?? null;
    }

    /**
     * @param string $block_tag_name Block tag name
     */
    public function getBlockHandler(string $block_tag_name): ?BlockHandlerInterface
    {
        //phpcs:disable
        switch ($block_tag_name) {
            case 'inline_script': return new InlineScript();
            case 'scripts':       return new Scripts();
            case 'notes':         return new Notes();
            case 'hook':          return new Hook();
            case 'styles':        return new Styles();
            case 'component':     return new Component();
        }
        //phpcs:enable

        return null;
    }

    /**
     * @return FilterInterface[]
     */
    public function getPreFilters(): array
    {
        $filters =  [];

        if (SiteArea::isAdmin($this->area)) {
            $filters[] = new PreScript();
        } elseif (SiteArea::isStorefront($this->area)) {
            $filters[] = new TemplateWrapper();
        }

        return $filters;
    }

    /**
     * @return Translation[]
     */
    public function getPostFilters(): array
    {
        return [
            new Translation(),
        ];
    }

    /**
     * @return FilterInterface[]
     */
    public function getOutputFilters(): array
    {
        $filters = [];

        if (
            SiteArea::isAdmin($this->area)
            && !empty($this->auth['user_id'])
            && fn_allowed_for('ULTIMATE')
        ) {
            $filters[] = new Sharing();
        }

        if (SiteArea::isStorefront($this->area)) {
            if (Registry::get('runtime.customization_mode.design')) {
                $filters[] = new TemplateIds();
            }

            if (Registry::get('runtime.customization_mode.live_editor')) {
                $filters[] = new LiveEditorWrapper();
            }

            $filters[] = new OutputScript();
        }

        if (Embedded::isEnabled()) {
            $filters[] = new EmbeddedUrl();
        }

        if (fn_is_csrf_protection_enabled($this->auth)) {
            $filters[] = new SecurityHash();
        }

        return $filters;
    }
}
