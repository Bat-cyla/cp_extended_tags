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
use Tygh\Registry;


if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($_SERVER['REQUEST_METHOD']	== 'POST') {
    if($mode == 'delete') {
        if(!empty($_REQUEST['object_type'])){
            $object_type=$_REQUEST['object_type'];
        }
        if (!empty($_REQUEST['tag_id'])) {
            fn_delete_tag_by_id($_REQUEST['tag_id']);
        }

    }
    if ($mode == 'approve') {
        $object_type=$_REQUEST['object_type'];
        db_query("UPDATE ?:cp_extended_tags SET status = 'A' WHERE tag_id IN (?n)", $_REQUEST['tag_ids']);
    }

    if ($mode == 'disapprove') {
        $object_type=$_REQUEST['object_type'];
        db_query("UPDATE ?:cp_extended_tags SET status = 'D' WHERE tag_id IN (?n)", $_REQUEST['tag_ids']);
    }

    if ($mode == 'm_delete') {
        $object_type=$_REQUEST['object_type'];
        if (!empty($_REQUEST['tag_ids'])) {
            fn_delete_tag_by_id($_REQUEST['tag_ids']);
        }
    }

    elseif ($mode == 'm_update') {
        $object_type=Tygh::$app['view']->getTemplateVars('object_type');
        if (isset($_REQUEST['active_tags_ids'])) {
            foreach($_REQUEST['tags_ids'] as $tag_id){
                in_array($tag_id,$_REQUEST['active_tags_ids']) ? fn_cp_extended_tags_update_tags_info($tag_id,'A') : fn_cp_extended_tags_update_tags_info($tag_id,'D');
            }
        }else{
            foreach($_REQUEST['tags_ids'] as $tag_id){
                fn_cp_extended_tags_update_tags_info($tag_id,'D');
            }
        }
    }
    return [CONTROLLER_STATUS_OK, "cp_extended_tags.manage?object_type=$object_type"];
}

elseif($mode == 'manage')
{
    $auth=Tygh::$app['session']['auth'];
    if($auth['user_type']=='A'){
        $_REQUEST['user_id']=$auth['user_id'];
    }elseif($auth['user_type']=='V'){
        $_REQUEST['user_id']=$auth['user_id'];
    }
    $_REQUEST['user_type']=$auth['user_type'];
        list($tags, $params) = fn_cp_get_extended_tags($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'));
        $object_type=$params['object_type'];
    Tygh::$app['view']->assign(array(
        'tags'  => $tags,
        'search' => $params,
        'object_type'=>$object_type,
        'user_id'=>$auth['user_id'],
        'user_type'=>$auth['user_type'],
    ));

}


