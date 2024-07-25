<?php
/*****************************************************************************
 *                                                        © 2013 Cart-Power   *
 *           __   ______           __        ____                             *
 *          / /  / ____/___ ______/ /_      / __ \____ _      _____  _____    *
 *      __ / /  / /   / __ `/ ___/ __/_____/ /_/ / __ \ | /| / / _ \/ ___/    *
 *     / // /  / /___/ /_/ / /  / /_/_____/ ____/ /_/ / |/ |/ /  __/ /        *
 *    /_//_/   \____/\__,_/_/   \__/     /_/    \____/|__/|__/\___/_/         *
 *                                                                            *
 *                                                                            *
 * -------------------------------------------------------------------------- *
 * This is commercial software, only users who have purchased a valid license *
 * and  accept to the terms of the License Agreement can install and use this *
 * program.                                                                   *
 * -------------------------------------------------------------------------- *
 * website: https://store.cart-power.com                                      *
 * email:   sales@cart-power.com                                              *
 ******************************************************************************/
use Tygh\Tygh;


if (!defined('BOOTSTRAP')) { die('Access denied'); }
$user_type=Tygh::$app['session']['auth']['user_type'];
$user_id=Tygh::$app['session']['auth']['user_id'];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
}
if($mode=='details'){
    if(isset($_REQUEST['order_id'])) {
        $tags=fn_cp_extended_tags_get_object_tags_data($_REQUEST['order_id'],'O',$user_type,$user_id);
    }
    $order_info=Tygh::$app['view']->getTemplateVars('order_info');
    $order_info['tags']=$tags;
    Tygh::$app['view']->assign('order_info', $order_info);
}
elseif($mode=='manage'){

    $orders=Tygh::$app['view']->getTemplateVars('orders');
    foreach($orders as $key=>$order){
        $orders[$key]['tags']=fn_cp_extended_tags_get_object_tags_data($order['order_id'],'O',$user_type,$user_id);
    }
    $tags=fn_cp_extended_tags_get_distinct_tag('O',$user_id);
    Tygh::$app['view']->assign([
        'tags'=>$tags,
        'orders'=>$orders,
        'user_type'=>$user_type,
        'user_id'=>$user_id,
    ]);


}


