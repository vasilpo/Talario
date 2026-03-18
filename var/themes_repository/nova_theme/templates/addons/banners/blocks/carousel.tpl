{** block-description:carousel **}
{include file="design/themes/responsive/templates/addons/banners/blocks/carousel.tpl"
    block=$block
    items=$items
    carousel_outside_navigation=($block.properties.navigation === "A")
    show_carousel_wrapper=true
    navigationTextPrev="<span class=\"ty-icon ty-icon-left-open-thin\"></span>"
    navigationTextNext="<span class=\"ty-icon ty-icon-right-open-thin\"></span>"
    language_direction=$language_direction
}
