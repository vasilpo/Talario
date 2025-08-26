<?php
/*******************************************************************************************
*   ___  _          ______                     _ _                _                        *
*  / _ \| |         | ___ \                   | (_)              | |              © 2024   *
* / /_\ | | _____  _| |_/ /_ __ __ _ _ __   __| |_ _ __   __ _   | |_ ___  __ _ _ __ ___   *
* |  _  | |/ _ \ \/ / ___ \ '__/ _` | '_ \ / _` | | '_ \ / _` |  | __/ _ \/ _` | '_ ` _ \  *
* | | | | |  __/>  <| |_/ / | | (_| | | | | (_| | | | | | (_| |  | ||  __/ (_| | | | | | | *
* \_| |_/_|\___/_/\_\____/|_|  \__,_|_| |_|\__,_|_|_| |_|\__, |  \___\___|\__,_|_| |_| |_| *
*                                                         __/ |                            *
*                                                        |___/                             *
* ---------------------------------------------------------------------------------------- *
* This is commercial software, only users who have purchased a valid license and accept    *
* to the terms of the License Agreement can install and use this program.                  *
* ---------------------------------------------------------------------------------------- *
* website: https://cs-cart.alexbranding.com                                                *
*   email: info@alexbranding.com                                                           *
*******************************************************************************************/
use Tygh\BlockManager\Exim;
use Tygh\BlockManager\Layout;
use Tygh\Registry;
use Tygh\Themes\Styles;
function fn_abt__ut2_mv_add_layouts($layouts, $style_id)
{
foreach ($layouts as $layout) {
$xml = simplexml_load_string(file_get_contents(Registry::get('config.dir.design_frontend') . 'abt__unitheme2/layouts/' . $layout['file']));
$layout['name'] = (string) $xml->layout->name;
$_REQUEST['layout_id'] = 0;
$_REQUEST['layout_data'] = $layout;
$layout_data = $_REQUEST['layout_data'];
$from_default_layout = [];
if (empty($_REQUEST['layout_id'])) {
$layout_data['theme_name'] = fn_get_theme_path('[theme]', 'C');
if (!empty($layout_data['from_layout_id']) && !is_numeric($layout_data['from_layout_id'])) {
list($from_default_layout['theme_name'], $from_default_layout['filename']) = explode('|', $layout_data['from_layout_id'], 2);
unset($layout_data['from_layout_id']);
}
}
$layout_id = Layout::instance()->update($layout_data, $_REQUEST['layout_id']);
if (!empty($from_default_layout) && !empty($layout_id)) {
$repo_dest = fn_get_theme_path('[themes]/' . $from_default_layout['theme_name'], 'C');
$layout_path = fn_normalize_path($repo_dest . '/layouts/' . $from_default_layout['filename']);
$exim = Exim::instance(Registry::get('runtime.company_id'), $layout_id, fn_get_theme_path('[theme]', 'C'));
$structure = $exim->getStructure($layout_path);
if (!empty($structure)) {
foreach ($layout_data as $key => $val) {
if (!empty($structure->layout->{$key})) {
$structure->layout->{$key} = $val;
}
}
if (!isset($layout_data['is_default'])) {
$structure->layout->is_default = 0;
}
$exim->import($structure, [
'import_style' => 'update',
]);
fn_create_theme_logos_by_layout_id($layout_data['theme_name'], $layout_id, Registry::get('runtime.company_id'), false, Styles::factory($layout_data['theme_name'])->getDefault());
}
}
fn_clear_cache();
fn_clear_cache('assets', 'design/');
fn_clear_cache('static');
fn_rm(Registry::get('config.dir.root') . '/var/cache');
if (!empty($layout['is_default']) && !empty($layout_id)) {
Styles::factory(fn_get_theme_path('[theme]', 'C'))->setStyle($layout_id, $style_id);
Layout::instance()->setDefault($layout_id);
}
}
return __('abt__ut2_mv.autoinstall.add_layouts');
}
function fn_abt__ut2_mv_remove_ult_layouts (){
$remove_ult_layouts = db_get_fields('SELECT layout_id FROM ?:bm_layouts WHERE theme_name = \'abt__unitheme2\' AND name NOT LIKE \'%Multivendor%\'');
if (!empty($remove_ult_layouts)){
foreach ($remove_ult_layouts as $remove_ult_layout) {
Layout::instance()->delete($remove_ult_layout);
}
}
}
