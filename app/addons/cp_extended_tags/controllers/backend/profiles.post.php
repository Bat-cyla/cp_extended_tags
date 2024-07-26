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
    if($mode=='update') {
        $user_type=Tygh::$app['session']['auth']['user_type'];
        $user_id=Tygh::$app['session']['auth']['user_id'];
        $customer_data=$_REQUEST['user_data'];
        $customer_id=$_REQUEST['user_id'];
        $object_type = 'C';
        if (!empty($customer_data['tags'])) {
            $tags = $customer_data['tags'];
            fn_cp_extended_tags_manage_tags($tags, $customer_id, $object_type, $user_type,$user_id);
            if (!empty($_REQUEST['user_tags'])) {

                $exist_tags = array_keys($_REQUEST['user_tags']);
                fn_cp_extended_tags_delete_tags_links_by_ids($tags, $exist_tags, $customer_id, $object_type,$user_type,$user_id);
            }
        }
        else{
            $tags=[];
            $exist_tags = array_keys($_REQUEST['user_tags']);
            fn_cp_extended_tags_delete_tags_links_by_ids($tags, $exist_tags, $customer_id, $object_type,$user_type,$user_id);
        }
        fn_cp_extended_tags_delete_tags();
    }
}
if($mode=='manage'){
    $users=Tygh::$app['view']->getTemplateVars('users');
    foreach($users as $key=>$user){
        $users[$key]['tags']=fn_cp_extended_tags_get_object_tags_data($user['user_id'],'C',$user_type,$user_id);
    }
    $tags=fn_cp_extended_tags_get_distinct_tag('C',$user_id);
    Tygh::$app['view']->assign([
        'tags'=>$tags,
        'users'=> $users,
        'user_type'=>$user_type,
        'user_id'=>$user_id,
    ]);
}
elseif($mode=='update'){
    if(isset($_REQUEST['user_id'])){
        $tags=fn_cp_extended_tags_get_object_tags_data($_REQUEST['user_id'],'C',$user_type,$user_id);
    }
    $user_data=Tygh::$app['view']->getTemplateVars('user_data');
    $user_data['tags']=$tags;
    Tygh::$app['view']->assign('user_data', $user_data);

}