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

    <body style="width: 1122px; height: 793px;">
        <div style="width: 1122px; height: 793px; position: relative;" >
            {if $data.print_bg == 'Y'}
                <img style="width: 260mm; height: 185mm;" src="{$images_dir}/addons/rus_russianpost/blank_7a.jpg" />
            {/if}

            <span style="position: absolute; height: 15px; width: 15px; top: 118px; left: 323px; text-align: center; font: 10pt 'Arial';">&#xD7;</span>

            {if !empty($data.not_total) && $data.not_total == 'Y'}
                <span style="position: absolute; height: 15px; width: 15px; top: 100px; left: 496px; text-align: center; font: 10pt 'Arial';">&#xD7;</span>
                <span style="position: absolute; height: 49px; width: 378px; top: 223px; left: 498px; text-align: center; font: 11pt 'Arial';">{$data.total_declared}</span>
            {/if}

            {if !empty($data.imposed_total) && $data.imposed_total == 'Y'}
                <span style="position: absolute; height: 15px; width: 15px; top: 115px; left: 496px; text-align: center; font: 10pt 'Arial';">&#xD7;</span>
                <span style="position: absolute; height: 49px; width: 378px; top: 277px; left: 498px; text-align: center; font: 11pt 'Arial';">{$data.total_imposed}</span>
            {/if}

            <span style="position: absolute; height: 31px; width: 358px; top: 256px; left: 130px; font: 11pt 'Arial'; line-height: 10pt;">{$data.whom} {$data.whom2}</span>
            <span style="position: absolute; height: 67px; width: 380px; top: 330px; left: 106px; font: 11pt 'Arial'; line-height: 15pt;">{$data.where} {$data.where2}</span>
            {if $data.sms_for_sender == 'Y'}
                <div style="position: absolute; height: 19px; width: 206px; top: 413px; left: 133px; font: 11pt 'Arial'; margin:0;">
                    <span style="position: absolute; left: 4px;">{$data.company_phone.0}</span>
                    <span style="position: absolute; left: 24px;">{$data.company_phone.1}</span>
                    <span style="position: absolute; left: 44px;">{$data.company_phone.2}</span>
                    <span style="position: absolute; left: 70px;">{$data.company_phone.3}</span>
                    <span style="position: absolute; left: 91px;">{$data.company_phone.4}</span>
                    <span style="position: absolute; left: 111px;">{$data.company_phone.5}</span>
                    <span style="position: absolute; left: 132px;">{$data.company_phone.6}</span>
                    <span style="position: absolute; left: 152px;">{$data.company_phone.7}</span>
                    <span style="position: absolute; left: 173px;">{$data.company_phone.8}</span>
                    <span style="position: absolute; left: 193px;">{$data.company_phone.9}</span>
                </div>
                <span style="position: absolute; height: 15px; width: 15px; top: 434px; left: 114px; text-align: center; font: 10pt 'Arial';">&#xD7;</span>
            {/if}
            <span style="position: absolute; height: 18px; width: 120px; top: 413px; left: 365px; font: 11pt 'Arial'; letter-spacing: 7.6pt;">
                <span style="position: absolute; left: 3px;">{$data.index.0}</span>
                <span style="position: absolute; left: 25px;">{$data.index.1}</span>
                <span style="position: absolute; left: 46px;">{$data.index.2}</span>
                <span style="position: absolute; left: 67px;">{$data.index.3}</span>
                <span style="position: absolute; left: 88px;">{$data.index.4}</span>
                <span style="position: absolute; left: 108px;">{$data.index.5}</span>
            </span>

            <span style="position: absolute; height: 27px; width: 359px; top: 381px; left: 519px; font: 11pt 'Arial'; line-height: 10pt;">{$data.from_whom} {$data.from_whom2}</span>
            <span style="position: absolute; height: 64px; width: 382px; top: 455px; left: 496px; font: 11pt 'Arial'; line-height: 14pt;">{$data.sender_address} {$data.sender_address2}</span>
            {if $data.sms_for_recepient == 'Y'}
                <div style="position: absolute; height: 18px; width: 206px; top: 538px; left: 523px; font: 11pt 'Arial'; margin:0;">
                    <span style="position: absolute; left: 3px;">{$data.recipient_phone.0}</span>
                    <span style="position: absolute; left: 24px;">{$data.recipient_phone.1}</span>
                    <span style="position: absolute; left: 44px;">{$data.recipient_phone.2}</span>
                    <span style="position: absolute; left: 70px;">{$data.recipient_phone.3}</span>
                    <span style="position: absolute; left: 90px;">{$data.recipient_phone.4}</span>
                    <span style="position: absolute; left: 111px;">{$data.recipient_phone.5}</span>
                    <span style="position: absolute; left: 131px;">{$data.recipient_phone.6}</span>
                    <span style="position: absolute; left: 152px;">{$data.recipient_phone.7}</span>
                    <span style="position: absolute; left: 172px;">{$data.recipient_phone.8}</span>
                    <span style="position: absolute; left: 193px;">{$data.recipient_phone.9}</span>
                </div>
                <span style="position: absolute; height: 5mm; width: 5mm; top: 118mm; left: 100.5mm; text-align: center; font: 9pt 'Arial';">&#xD7;</span>
            {/if}
            <span style="position: absolute; height: 20px; width: 122px; top: 538px; left: 755px; font: 11pt 'Arial'; letter-spacing: 7.6pt;">
                <span style="position: absolute; left: 5px;">{$data.from_index.0}</span>
                <span style="position: absolute; left: 26px;">{$data.from_index.1}</span>
                <span style="position: absolute; left: 46px;">{$data.from_index.2}</span>
                <span style="position: absolute; left: 66px;">{$data.from_index.3}</span>
                <span style="position: absolute; left: 87px;">{$data.from_index.4}</span>
                <span style="position: absolute; left: 107px;">{$data.from_index.5}</span>
            </span>
        </div>
    </body>
</html>
