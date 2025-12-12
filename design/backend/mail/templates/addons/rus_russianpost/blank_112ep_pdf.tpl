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

    <body style="width: 793px; height: 1107px;">
        <div style="width: 793px; height: 1107px; position: relative;" >
            {if $data.print_bg == 'Y'}
                <img style="width: 793px; height: 1107px;" src="{$images_dir}/addons/rus_russianpost/blank_112ep.jpg" />
            {/if}

            {if !empty($data.imposed_total) && $data.imposed_total == 'Y'}
                <span style="position: absolute; height: 22px; width: 22px; top: 115px; left: 48px; text-align: center; font: 25pt 'Arial';">&#xD7;</span>
                <span style="position: absolute; height: 31px; width: 72px; top: 86.5px; left: 61px; text-align: center; font: 11pt 'Arial';">{$data.imposed_rub}</span>
                <span style="position: absolute; height: 31px; width: 33px; top: 86.5px; left: 157px; text-align: center; font: 11pt 'Arial';">{$data.imposed_kop}</span>
                <span style="position: absolute; height: 42px; width: 346px; top: 78px; left: 231px; text-align: center; font: 10pt 'Arial'; line-height: 1.6">{$data.t_imposed}</span>
            {/if}

            {if $data.sms_for_sender == 'Y'}
                <div style="position: absolute; height: 14px; width: 171px; top: 159px; left: 405px; font: 11pt 'Arial'; margin:0;">
                    <span style="position: absolute; left: 5px;">{$data.company_phone.0}</span>
                    <span style="position: absolute; left: 22px;">{$data.company_phone.1}</span>
                    <span style="position: absolute; left: 39px;">{$data.company_phone.2}</span>
                    <span style="position: absolute; left: 56px;">{$data.company_phone.3}</span>
                    <span style="position: absolute; left: 74px;">{$data.company_phone.4}</span>
                    <span style="position: absolute; left: 90px;">{$data.company_phone.5}</span>
                    <span style="position: absolute; left: 108px;">{$data.company_phone.6}</span>
                    <span style="position: absolute; left: 125px;">{$data.company_phone.7}</span>
                    <span style="position: absolute; left: 142px;">{$data.company_phone.8}</span>
                    <span style="position: absolute; left: 160px;">{$data.company_phone.9}</span>
                </div>
            {/if}

            {if $data.sms_for_recepient == 'Y'}
                <div style="position: absolute; height: 14px; width: 171px; top: 176px; left: 405px; font: 11pt 'Arial'; margin:0;">
                    <span style="position: absolute; left: 5px;">{$data.recipient_phone.0}</span>
                    <span style="position: absolute; left: 22px;">{$data.recipient_phone.1}</span>
                    <span style="position: absolute; left: 39px;">{$data.recipient_phone.2}</span>
                    <span style="position: absolute; left: 56px;">{$data.recipient_phone.3}</span>
                    <span style="position: absolute; left: 74px;">{$data.recipient_phone.4}</span>
                    <span style="position: absolute; left: 90px;">{$data.recipient_phone.5}</span>
                    <span style="position: absolute; left: 108px;">{$data.recipient_phone.6}</span>
                    <span style="position: absolute; left: 125px;">{$data.recipient_phone.7}</span>
                    <span style="position: absolute; left: 142px;">{$data.recipient_phone.8}</span>
                    <span style="position: absolute; left: 160px;">{$data.recipient_phone.9}</span>
                </div>
                <span style="position: absolute; height: 15px; width: 16px; top: 123px; left: 391px; text-align: center; font: 15pt 'Arial';">&#xD7;</span>
            {/if}

            <span style="position: absolute; height: 15px; width: 655px; top: 197px; left: 105px; font: 11pt 'Arial';">{$data.whom} {$data.whom2}</span>
            <span style="position: absolute; height: 15px; width: 678px; top: 220px; left: 82px; font: 11pt 'Arial'; line-height: 15pt;">{$data.where}</span>
            <span style="position: absolute; height: 15px; width: 556px; top: 243px; left: 46px; font: 11pt 'Arial'; line-height: 15pt;">{$data.where2}</span>

            <span style="position: absolute; height: 15px; width: 101px; top: 245px; left: 657px; font: 11pt 'Arial'; letter-spacing: 7.6pt;">
                <span style="position: absolute; left: 3px;">{$data.index.0}</span>
                <span style="position: absolute; left: 20px;">{$data.index.1}</span>
                <span style="position: absolute; left: 38px;">{$data.index.2}</span>
                <span style="position: absolute; left: 55px;">{$data.index.3}</span>
                <span style="position: absolute; left: 72px;">{$data.index.4}</span>
                <span style="position: absolute; left: 90px;">{$data.index.5}</span>
            </span>

            <div style="position: absolute; height: 15px; width: 621px; top: 263px; left: 137px; font: 11pt 'Arial'; margin: 0;">
                <span style="position: absolute; left: 4px;">{$data.text1.0}</span>
                <span style="position: absolute; left: 21px;">{$data.text1.1}</span>
                <span style="position: absolute; left: 38px;">{$data.text1.2}</span>
                <span style="position: absolute; left: 55px;">{$data.text1.3}</span>
                <span style="position: absolute; left: 72px;">{$data.text1.4}</span>
                <span style="position: absolute; left: 90px;">{$data.text1.5}</span>
                <span style="position: absolute; left: 107px;">{$data.text1.6}</span>
                <span style="position: absolute; left: 124px;">{$data.text1.7}</span>
                <span style="position: absolute; left: 142px;">{$data.text1.8}</span>
                <span style="position: absolute; left: 159px;">{$data.text1.9}</span>
                <span style="position: absolute; left: 176px;">{$data.text1.10}</span>
                <span style="position: absolute; left: 194px;">{$data.text1.11}</span>
                <span style="position: absolute; left: 211px;">{$data.text1.12}</span>
                <span style="position: absolute; left: 229px;">{$data.text1.13}</span>
                <span style="position: absolute; left: 246px;">{$data.text1.14}</span>
                <span style="position: absolute; left: 263px;">{$data.text1.15}</span>
                <span style="position: absolute; left: 280px;">{$data.text1.16}</span>
                <span style="position: absolute; left: 298px;">{$data.text1.17}</span>
                <span style="position: absolute; left: 316px;">{$data.text1.18}</span>
                <span style="position: absolute; left: 333px;">{$data.text1.19}</span>
                <span style="position: absolute; left: 350px;">{$data.text1.20}</span>
                <span style="position: absolute; left: 367px;">{$data.text1.21}</span>
                <span style="position: absolute; left: 385px;">{$data.text1.22}</span>
                <span style="position: absolute; left: 403px;">{$data.text1.23}</span>
                <span style="position: absolute; left: 419px;">{$data.text1.24}</span>
                <span style="position: absolute; left: 436px;">{$data.text1.25}</span>
                <span style="position: absolute; left: 454px;">{$data.text1.26}</span>
                <span style="position: absolute; left: 471px;">{$data.text1.27}</span>
                <span style="position: absolute; left: 489px;">{$data.text1.28}</span>
                <span style="position: absolute; left: 506px;">{$data.text1.29}</span>
                <span style="position: absolute; left: 523px;">{$data.text1.30}</span>
                <span style="position: absolute; left: 541px;">{$data.text1.31}</span>
                <span style="position: absolute; left: 559px;">{$data.text1.32}</span>
                <span style="position: absolute; left: 575px;">{$data.text1.33}</span>
                <span style="position: absolute; left: 593px;">{$data.text1.34}</span>
                <span style="position: absolute; left: 610px;">{$data.text1.35}</span>
            </div>

            <div style="position: absolute; height: 15px; width: 621px; top: 281px; left: 137px; font: 11pt 'Arial'; margin: 0;">
                <span style="position: absolute; left: 4px;">{$data.text2.0}</span>
                <span style="position: absolute; left: 21px;">{$data.text2.1}</span>
                <span style="position: absolute; left: 38px;">{$data.text2.2}</span>
                <span style="position: absolute; left: 55px;">{$data.text2.3}</span>
                <span style="position: absolute; left: 72px;">{$data.text2.4}</span>
                <span style="position: absolute; left: 90px;">{$data.text2.5}</span>
                <span style="position: absolute; left: 107px;">{$data.text2.6}</span>
                <span style="position: absolute; left: 124px;">{$data.text2.7}</span>
                <span style="position: absolute; left: 142px;">{$data.text2.8}</span>
                <span style="position: absolute; left: 159px;">{$data.text2.9}</span>
                <span style="position: absolute; left: 176px;">{$data.text2.10}</span>
                <span style="position: absolute; left: 194px;">{$data.text2.11}</span>
                <span style="position: absolute; left: 211px;">{$data.text2.12}</span>
                <span style="position: absolute; left: 229px;">{$data.text2.13}</span>
                <span style="position: absolute; left: 246px;">{$data.text2.14}</span>
                <span style="position: absolute; left: 263px;">{$data.text2.15}</span>
                <span style="position: absolute; left: 280px;">{$data.text2.16}</span>
                <span style="position: absolute; left: 298px;">{$data.text2.17}</span>
                <span style="position: absolute; left: 316px;">{$data.text2.18}</span>
                <span style="position: absolute; left: 333px;">{$data.text2.19}</span>
                <span style="position: absolute; left: 350px;">{$data.text2.20}</span>
                <span style="position: absolute; left: 367px;">{$data.text2.21}</span>
                <span style="position: absolute; left: 385px;">{$data.text2.22}</span>
                <span style="position: absolute; left: 403px;">{$data.text2.23}</span>
                <span style="position: absolute; left: 419px;">{$data.text2.24}</span>
                <span style="position: absolute; left: 436px;">{$data.text2.25}</span>
                <span style="position: absolute; left: 454px;">{$data.text2.26}</span>
                <span style="position: absolute; left: 471px;">{$data.text2.27}</span>
                <span style="position: absolute; left: 489px;">{$data.text2.28}</span>
                <span style="position: absolute; left: 506px;">{$data.text2.29}</span>
                <span style="position: absolute; left: 523px;">{$data.text2.30}</span>
                <span style="position: absolute; left: 541px;">{$data.text2.31}</span>
                <span style="position: absolute; left: 559px;">{$data.text2.32}</span>
                <span style="position: absolute; left: 575px;">{$data.text2.33}</span>
                <span style="position: absolute; left: 593px;">{$data.text2.34}</span>
                <span style="position: absolute; left: 610px;">{$data.text2.35}</span>
            </div>

            <div style="position: absolute; height: 15px; width: 206px; top: 316px; left: 95px; font: 11pt 'Arial'; margin: 0;">
                <span style="position: absolute; left: 3px;">{$data.inn.0}</span>
                <span style="position: absolute; left: 20px;">{$data.inn.1}</span>
                <span style="position: absolute; left: 38px;">{$data.inn.2}</span>
                <span style="position: absolute; left: 55px;">{$data.inn.3}</span>
                <span style="position: absolute; left: 72px;">{$data.inn.4}</span>
                <span style="position: absolute; left: 89px;">{$data.inn.5}</span>
                <span style="position: absolute; left: 107px;">{$data.inn.6}</span>
                <span style="position: absolute; left: 124px;">{$data.inn.7}</span>
                <span style="position: absolute; left: 142px;">{$data.inn.8}</span>
                <span style="position: absolute; left: 160px;">{$data.inn.9}</span>
                <span style="position: absolute; left: 177px;">{$data.inn.10}</span>
                <span style="position: absolute; left: 193px;">{$data.inn.11}</span>
            </div>

            <div style="position: absolute; height: 15px; width: 346px; top: 316px; left: 415px; font: 11pt 'Arial'; margin: 0;">
                <span style="position: absolute; left: 3px;">{$data.kor.0}</span>
                <span style="position: absolute; left: 20px;">{$data.kor.1}</span>
                <span style="position: absolute; left: 37px;">{$data.kor.2}</span>
                <span style="position: absolute; left: 54px;">{$data.kor.3}</span>
                <span style="position: absolute; left: 72px;">{$data.kor.4}</span>
                <span style="position: absolute; left: 89px;">{$data.kor.5}</span>
                <span style="position: absolute; left: 107px;">{$data.kor.6}</span>
                <span style="position: absolute; left: 124px;">{$data.kor.7}</span>
                <span style="position: absolute; left: 142px;">{$data.kor.8}</span>
                <span style="position: absolute; left: 159px;">{$data.kor.9}</span>
                <span style="position: absolute; left: 177px;">{$data.kor.10}</span>
                <span style="position: absolute; left: 194px;">{$data.kor.11}</span>
                <span style="position: absolute; left: 211px;">{$data.kor.12}</span>
                <span style="position: absolute; left: 229px;">{$data.kor.13}</span>
                <span style="position: absolute; left: 245px;">{$data.kor.14}</span>
                <span style="position: absolute; left: 263px;">{$data.kor.15}</span>
                <span style="position: absolute; left: 280px;">{$data.kor.16}</span>
                <span style="position: absolute; left: 297px;">{$data.kor.17}</span>
                <span style="position: absolute; left: 315px;">{$data.kor.18}</span>
                <span style="position: absolute; left: 332px;">{$data.kor.19}</span>
            </div>

            <span style="position: absolute; height: 15px; width: 602px; top: 335px; left: 157px; font: 11pt 'Arial';">{$data.bank}</span>
            <div style="position: absolute; height: 15px; width: 345px; top: 353px; left: 94px; font: 11pt 'Arial'; margin: 0;">
                <span style="position: absolute; left: 4px;">{$data.ras.0}</span>
                <span style="position: absolute; left: 21px;">{$data.ras.1}</span>
                <span style="position: absolute; left: 39px;">{$data.ras.2}</span>
                <span style="position: absolute; left: 56px;">{$data.ras.3}</span>
                <span style="position: absolute; left: 73px;">{$data.ras.4}</span>
                <span style="position: absolute; left: 91px;">{$data.ras.5}</span>
                <span style="position: absolute; left: 108px;">{$data.ras.6}</span>
                <span style="position: absolute; left: 126px;">{$data.ras.7}</span>
                <span style="position: absolute; left: 143px;">{$data.ras.8}</span>
                <span style="position: absolute; left: 160px;">{$data.ras.9}</span>
                <span style="position: absolute; left: 177px;">{$data.ras.10}</span>
                <span style="position: absolute; left: 195px;">{$data.ras.11}</span>
                <span style="position: absolute; left: 212px;">{$data.ras.12}</span>
                <span style="position: absolute; left: 230px;">{$data.ras.13}</span>
                <span style="position: absolute; left: 247px;">{$data.ras.14}</span>
                <span style="position: absolute; left: 264px;">{$data.ras.15}</span>
                <span style="position: absolute; left: 282px;">{$data.ras.16}</span>
                <span style="position: absolute; left: 299px;">{$data.ras.17}</span>
                <span style="position: absolute; left: 316px;">{$data.ras.18}</span>
                <span style="position: absolute; left: 334px;">{$data.ras.19}</span>
            </div>

            <div style="position: absolute; height: 15px; width: 155px; top: 353px; left: 603px; font: 11pt 'Arial'; margin: 0;">
                <span style="position: absolute; left: 6px;">{$data.bik.0}</span>
                <span style="position: absolute; left: 23px;">{$data.bik.1}</span>
                <span style="position: absolute; left: 40px;">{$data.bik.2}</span>
                <span style="position: absolute; left: 58px;">{$data.bik.3}</span>
                <span style="position: absolute; left: 75px;">{$data.bik.4}</span>
                <span style="position: absolute; left: 93px;">{$data.bik.5}</span>
                <span style="position: absolute; left: 110px;">{$data.bik.6}</span>
                <span style="position: absolute; left: 127px;">{$data.bik.7}</span>
                <span style="position: absolute; left: 144px;">{$data.bik.8}</span>
            </div>

            <span style="position: absolute; height: 15px; width: 619px; top: 374px; left: 140px; font: 11pt 'Arial';">{$data.from_whom} {$data.from_whom2}</span>
            <span style="position: absolute; height: 15px; width: 584px; top: 400px; left: 178px; font: 11pt 'Arial'; line-height: 14pt;">{$data.sender_address}</span>
            <span style="position: absolute; height: 15px; width: 558px; top: 423px; left: 45px; font: 11pt 'Arial'; line-height: 14pt;">{$data.sender_address2}</span>

            <span style="position: absolute; height: 15px; width: 104px; top: 424px; left: 655px; font: 11pt 'Arial'; letter-spacing: 7.6pt;">
                <span style="position: absolute; left: 6px;">{$data.from_index.0}</span>
                <span style="position: absolute; left: 23px;">{$data.from_index.1}</span>
                <span style="position: absolute; left: 41px;">{$data.from_index.2}</span>
                <span style="position: absolute; left: 58px;">{$data.from_index.3}</span>
                <span style="position: absolute; left: 75px;">{$data.from_index.4}</span>
                <span style="position: absolute; left: 92px;">{$data.from_index.5}</span>
            </span>
        </div>
    </body>
</html>
