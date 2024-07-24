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

function fn_exim_orders_get_tags($order_id):string
{
    $join=db_quote(' LEFT JOIN ?:cp_extended_tags_links as links ON ?:cp_extended_tags.tag_id = links.tag_id');
    $data= array(
        'object_id' => $order_id,
        'object_type' => 'O',
    );
    $tags=db_get_fields("SELECT tag FROM ?:cp_extended_tags ?p WHERE ?w",$join,$data);
    if(!empty($tags)){
       return implode(',',$tags);
    }
    return '';
}
function fn_exim_orders_set_tags($tag_data, $order_id):bool
{

    $company_id=0;
    $object_type='O';
    if(!empty($tag_data)){
        $tag_data=explode(',',$tag_data);

    }
    fn_cp_extended_tags_delete_tags_before_import($order_id);
    if (is_array($tag_data)) {
        fn_cp_extended_tags_manage_tags($tag_data,$order_id,$object_type,$company_id);
    }
    return true;
}
