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
    $_REQUEST['company_id']=fn_get_runtime_company_id();
        list($tags, $params) = fn_cp_get_extended_tags($_REQUEST, Registry::get('settings.Appearance.admin_elements_per_page'));
        $object_type=$params['object_type'];
    Tygh::$app['view']->assign(array(
        'tags'  => $tags,
        'search' => $params,
        'object_type'=>$object_type
    ));
}


