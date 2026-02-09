<?php


use Tygh\Registry;
use Tygh\Settings;
use Tygh\Addons\OfdFerma\OfdFerma;

/**
 * Hook handler: after order status changed.
 *
 * @param string $status_to     Order status to
 * @param string $status_from   Order status from
 * @param array  $order_info    Order data
 */
function fn_rus_ofd_ferma_change_order_status($status_to, $status_from, $order_info)
{
    if ($order_info['is_parent_order'] === 'Y') {
        return;
    }

    $status = Registry::get('addons.rus_ofd_ferma.setting_status');  
    
    if(isset($status[$status_to])){
        $ofdferma = Tygh::$app['addons.rus_ofd_ferma.ofd_ferma']; 
        
        $ofdferma->setDebug(1);
        $ofdferma->OFDcreate($order_info['order_id']);
        $ofdferma->setDebug(0);
    }
} 

function fn_rus_ofd_ferma_show_notify($data, $ok = ''){
    
    if ($data && isset($data['status'])){
        if ($data['status']){
            
            $mess = $data['mess'];
            if ($ok){
                $mess = $ok;
            }
            
            if ($mess){
                fn_set_notification('N', __('notice'), $mess);
            }
        }else{
            if ($data['mess']){
                fn_set_notification('E', __('error'), $data['mess']);
            }
        }
    }
}

function fn_rus_ofd_ferma_send($to, $subject, $mess){
    /** @var \Tygh\Mailer\Mailer $mailer */
    $mailer = Tygh::$app['mailer'];

    $mailer->send(array(
        'to'        => $to,
        'from'      => 'company_users_department',
        'subject'   => $subject,
        'body'      => $mess,
    ), 'A');
}
