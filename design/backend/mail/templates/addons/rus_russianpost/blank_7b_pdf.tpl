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

    <body style="width: 509px; height: 720px;">
        <div style="width: 509px; height: 720px; position: relative;" >

            {if $data.print_bg == 'Y'}
                <img style="width: 509px; height: 720px;" src="{$images_dir}/addons/rus_russianpost/blank_7b.jpg" />
            {/if}

            {if !empty($data.not_total) && $data.not_total == 'Y'}
                <span style="position: absolute; height: 29px; width: 87px; top: 87px; left: 306px; text-align: center; font: 8pt 'Arial';">{$data.declared_rub} {$smarty.const.RUSSIANPOST_RUB} {$data.declared_kop} {$smarty.const.RUSSIANPOST_KOP}</span>
            {/if}

            {if !empty($data.imposed_total) && $data.imposed_total == 'Y'}
                <span style="position: absolute; height: 29px; width: 87px; top: 87px; left: 406px; text-align: center; font: 8pt 'Arial';">{$data.imposed_rub} {$smarty.const.RUSSIANPOST_RUB} {$data.imposed_kop} {$smarty.const.RUSSIANPOST_KOP}</span>
            {/if}

            <span style="position: absolute; height: 24px; width: 371px; top: 276px; left: 131px; font: 13pt 'Arial';">{$data.whom}</span>
            <span style="position: absolute; height: 24px; width: 476px; top: 304px; left: 29px; font: 13pt 'Arial';">{$data.whom2}</span>
            <span style="position: absolute; height: 99px; width: 422px; top: 332px; left: 81px; font: 12pt 'Arial'; line-height: 20pt;">{$data.where} {$data.where2}</span>

            {if $data.sms_for_sender == 'Y'}
                <div style="position: absolute; height: 19px; width: 191px; top: 438px; left: 49px; font: 13pt 'Arial'; margin:0;">
                    <span style="position: absolute; left: 3px;">{$data.company_phone.0}</span>
                    <span style="position: absolute; left: 22px;">{$data.company_phone.1}</span>
                    <span style="position: absolute; left: 41px;">{$data.company_phone.2}</span>
                    <span style="position: absolute; left: 63px;">{$data.company_phone.3}</span>
                    <span style="position: absolute; left: 81px;">{$data.company_phone.4}</span>
                    <span style="position: absolute; left: 100px;">{$data.company_phone.5}</span>
                    <span style="position: absolute; left: 118px;">{$data.company_phone.6}</span>
                    <span style="position: absolute; left: 137px;">{$data.company_phone.7}</span>
                    <span style="position: absolute; left: 156px;">{$data.company_phone.8}</span>
                    <span style="position: absolute; left: 175px;">{$data.company_phone.9}</span>
                </div>
                <span style="position: absolute; height: 13px; width: 13px; top: 457px; left: 27px; text-align: center; font: 14pt 'Arial';">&#xD7;</span>
            {/if}

            <span style="position: absolute; height: 33px; width: 110px; top: 439px; left: 358px; font: 20pt 'Arial'; text-align: center; letter-spacing: 7.6pt;">
                <span style="position: absolute; left: 3px;">{$data.index.0}</span>
                <span style="position: absolute; left: 20px;">{$data.index.1}</span>
                <span style="position: absolute; left: 37px;">{$data.index.2}</span>
                <span style="position: absolute; left: 54px;">{$data.index.3}</span>
                <span style="position: absolute; left: 71px;">{$data.index.4}</span>
                <span style="position: absolute; left: 88px;">{$data.index.5}</span>
            </span>

            <span style="position: absolute; height: 24px; width: 371px; top: 512px; left: 119px; font: 13pt 'Arial';">{$data.from_whom}</span>
            <span style="position: absolute; height: 24px; width: 476px; top: 537px; left: 25px; font: 13pt 'Arial';">{$data.from_whom2}</span>
            <span style="position: absolute; height: 104px; width: 422px; top: 565px; left: 80px; font: 12pt 'Arial'; line-height: 19pt;">{$data.sender_address} {$data.sender_address2}</span>

            {if $data.sms_for_recepient == 'Y'}
                <div style="position: absolute; height: 19px; width: 191px; top: 680px; left: 49px; font: 13pt 'Arial'; margin:0;">
                    <span style="position: absolute; left: 3px;">{$data.recipient_phone.0}</span>
                    <span style="position: absolute; left: 22px;">{$data.recipient_phone.1}</span>
                    <span style="position: absolute; left: 41px;">{$data.recipient_phone.2}</span>
                    <span style="position: absolute; left: 63px;">{$data.recipient_phone.3}</span>
                    <span style="position: absolute; left: 81px;">{$data.recipient_phone.4}</span>
                    <span style="position: absolute; left: 100px;">{$data.recipient_phone.5}</span>
                    <span style="position: absolute; left: 118px;">{$data.recipient_phone.6}</span>
                    <span style="position: absolute; left: 137px;">{$data.recipient_phone.7}</span>
                    <span style="position: absolute; left: 156px;">{$data.recipient_phone.8}</span>
                    <span style="position: absolute; left: 175px;">{$data.recipient_phone.9}</span>
                </div>
                <span style="position: absolute; height: 13px; width: 13px; top: 699px; left: 28px; text-align: center; font: 14pt 'Arial';">&#xD7;</span>
            {/if}

            <span style="position: absolute; height: 33px; width: 110px; top: 681px; left: 357px; font: 20pt 'Arial'; text-align: center; letter-spacing: 7.6pt;">
                <span style="position: absolute; left: 3px;">{$data.from_index.0}</span>
                <span style="position: absolute; left: 20px;">{$data.from_index.1}</span>
                <span style="position: absolute; left: 37px;">{$data.from_index.2}</span>
                <span style="position: absolute; left: 54px;">{$data.from_index.3}</span>
                <span style="position: absolute; left: 71px;">{$data.from_index.4}</span>
                <span style="position: absolute; left: 88px;">{$data.from_index.5}</span>
            </span>
        </div>
    </body>
</html>