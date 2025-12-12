<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        {literal}
            <style media="screen,print">

            body,p,div,td {
                color: #000000;
                font: 12px Arial;
            }
            body {
                padding: 0;
                margin: 0;
            }
            a, a:link, a:visited, a:hover, a:active {
                color: #000000;
                text-decoration: underline;
            }
            a:hover {
                text-decoration: none;
            }
            </style>
        {/literal}
    </head>

    <body style="width: 1099px; height: 777px;">
        <div style="width: 1099px; height: 777px; position: relative;" >
            {if $data.print_bg == 'Y'}
                <img style="width: 1099px; height: 777px;" src="{$images_dir}/addons/rus_russianpost/blank_107.jpg" />
            {/if}

            {$p_size = 189}
            {$p_number = 0}
            {$products_amount = 0}
            {$count_page = 1}
            {$price_products = 0}

            {foreach $order_info.products as $product}
                {$p_number = $p_number + 1}

                <span style="position: absolute; height: 19px; width: 38px; top: {$p_size}px; left: 24px; text-align: center; font: 11pt 'Arial';">{$p_number}</span>
                <span style="position: absolute; height: 19px; width: 267px; top: {$p_size}px; left: 62px; font: 6pt 'Arial';">{$product.product}</span>
                <span style="position: absolute; height: 19px; width: 64px; top: {$p_size}px; left: 329px; text-align: center; font: 11pt 'Arial';">{$product.amount}</span>
                <span style="position: absolute; height: 19px; width: 122px; top: {$p_size}px; left: 394px; text-align: center; font: 11pt 'Arial';">{$product.amount * $product.price}</span>

                <span style="position: absolute; height: 19px; width: 38px; top: {$p_size}px; left: 575px; text-align: center; font: 11pt 'Arial';">{$p_number}</span>
                <span style="position: absolute; height: 19px; width: 267px; top: {$p_size}px; left: 614px; font: 6pt 'Arial';">{$product.product}</span>
                <span style="position: absolute; height: 19px; width: 64px; top: {$p_size}px; left: 881px; text-align: center; font: 11pt 'Arial';">{$product.amount}</span>
                <span style="position: absolute; height: 19px; width: 122px; top: {$p_size}px; left: 946px; text-align: center; font: 11pt 'Arial';">{$product.amount * $product.price}</span>

                {$price_products = $price_products + $product.amount * $product.price}
                {$products_amount = $products_amount + $product.amount}
                {$p_size = $p_size + 19}

                {if (($p_number - ($count_page * 14)) == 0) && count($order_info.products) > $p_number}
                    {$count_page = $count_page + 1}
                    {$p_size = $p_size + 88}
                    <span style="position: absolute; height: 32px; width: 331px; top: {$p_size}px; left: 29px; font: 11pt 'Arial';">{$data.whom} {$data.whom2}</span>
                    <span style="position: absolute; height: 32px; width: 331px; top: {$p_size}px; left: 579px; font: 11pt 'Arial';">{$data.whom} {$data.whom2}</span>

                    {$p_size = $p_size + 424}
                    {if $data.print_bg == 'Y'}
                        <img style="width: 1099px; height: 777px;" src="{$images_dir}/addons/rus_russianpost/blank_107.jpg" />
                    {/if}
                    {$p_size = $p_size + 16}
                {/if}
            {/foreach}

            {if $p_number >=  $count_page*14}
                {$p_size = $p_size + ($p_number - $count_page*14)* 19}
            {else}
                {$p_size = $p_size + ($count_page*14 - $p_number)* 19}
            {/if}

            <span style="position: absolute; height: 18px; width: 60px; top: {$p_size}px; left: 331px; font: 11pt 'Arial'; text-align: center;">{$products_amount}</span>
            <span style="position: absolute; height: 18px; width: 60px; top: {$p_size}px; left: 883px; font: 11pt 'Arial'; text-align: center;">{$products_amount}</span>
            <span style="position: absolute; height: 18px; width: 123px; top: {$p_size}px; left: 393px; font: 11pt 'Arial'; text-align: center;">{$price_products}</span>
            <span style="position: absolute; height: 18px; width: 123px; top: {$p_size}px; left: 945px; font: 11pt 'Arial'; text-align: center;">{$price_products}</span>

            {$p_size = $p_size + 87}
            <span style="position: absolute; height: 32px; width: 331px; top: {$p_size}px; left: 29px; font: 11pt 'Arial';">{$data.whom} {$data.whom2}</span>
            <span style="position: absolute; height: 32px; width: 331px; top: {$p_size}px; left: 579px; font: 11pt 'Arial';">{$data.whom} {$data.whom2}</span>
        </div>
    </body>
</html>
