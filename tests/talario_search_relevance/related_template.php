<?php

$template = file_get_contents(__DIR__ . '/../../design/themes/responsive/templates/addons/talario_search_relevance/overrides/views/products/search.tpl');

if ($template === false) {
    fwrite(STDERR, "Unable to read related-card template\n");
    exit(1);
}

if (strpos($template, 'blocks/list_templates/grid_list.tpl') !== false) {
    fwrite(STDERR, "Related cards must not hard-code a separate grid renderer\n");
    exit(1);
}

if (strpos($template, '`$layouts.$selected_layout.template`') === false) {
    fwrite(STDERR, "Related cards must reuse the selected catalog layout\n");
    exit(1);
}

echo "TALARIO_RELATED_TEMPLATE_CONTRACT=PASS\n";
