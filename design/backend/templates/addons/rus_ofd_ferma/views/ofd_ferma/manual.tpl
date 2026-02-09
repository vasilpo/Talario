{capture name="mainbox"}

{assign var="return_current_url" value=$config.current_url|escape:url}



        <h2> Инструкция по использованию</h2>
        <p>Сначала вам необходимо заполнить настройки модуля ( <a href="{'addons.manage'|fn_url}"> Модули / Управление модулями /</a> нажмите по заголовку модуля "Ferma OFD.ru" )</p>            
        <p>Следить за работой модуля можно через лог файл - /var/ofd_ferma.log </p>        

        <h2>Дополнительная автоматизация (настройка cron'а)</h2>   
        <p>Для автоматического обновления статуса чеков рекомендуем настроить cron. Строка для cron'a: </p>
        <p> <strong>GET {$cron_url}</strong> </p>    
        <p> или так: </p>      
        <p> <strong> /20 * * * * wget -q -O - {$cron_url} /dev/null 2&gt;&amp;1 </strong></p>
        <p>Параметры уточните у вашего хостинг провайдера. Интервал: один раз в 20 - 30 мин. </p>     

{/capture}  

{include file="common/mainbox.tpl" title=__("rus_ofd_ferma.receipts_list.title_manual") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar}