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

function fn_cp_get_extended_tags($params = array(), $items_per_page = 0):array
{
    // Set default values to input params
    $default_params = array(
        'page' => 1,
        'items_per_page' => $items_per_page
    );

    $params = array_merge($default_params, $params);


    $sortings = array(
        'timestamp' => '?:cp_extended_tags.timestamp',
        'tag' => '?:cp_extended_tags.tag',
        'status' => '?:cp_extended_tags_links.status',
        'popularity'=>'popularity'
    );

    $condition = $limit = $join = '';

    if(!empty($params['object_type'])){
        $condition = db_quote(' AND object_type = ?s', $params['object_type']);
    }

    $object_type=$params['object_type'];

    if (!empty($params['limit'])) {
        $limit = db_quote(' LIMIT 0, ?i', $params['limit']);
    }

    $sorting = db_sort($params, $sortings, 'tag', 'desc');

    if(isset($params['is_search'])){
       if(!empty($params['status'])){
           $condition = db_quote(' AND status = ?s AND object_type=?s', $params['status'],$object_type);
       }
       if(!empty($params['tag'])){
           $tag=$params['tag'];
           $condition = db_quote( "AND ?:cp_extended_tags.tag LIKE ?l AND object_type=?s", "%".trim($tag)."%" , $object_type);
       }
    }
    if(!empty($params['user_id'])){
        $condition .= db_quote(' AND user_id = ?s', $params['user_id']);
    }else{
        return [];
    }

    if (!empty($params['status'])) {
        $condition .= db_quote(' AND ?:cp_extended_tags.status = ?s', $params['status']);
    }

    $fields = array(
        '?:cp_extended_tags.tag_id',
        '?:cp_extended_tags.tag',
        '?:cp_extended_tags_links.object_id as object_id',
        '?:cp_extended_tags_links.status',
        '?:cp_extended_tags.timestamp',
    );
    /**
     * This hook allows you to change parameters of the tags selection before making an SQL query.
     *
     * @param array $params The parameters of the user's query (limit, period, item_ids, etc)
     * @param string $condition The conditions of the selection
     * @param string $sorting Sorting (ask, desc)
     * @param string $limit The LIMIT of the returned rows
     * @param array $fields Selected fields
     */
    fn_set_hook('get_cp_extended_tags', $params, $condition, $sorting, $limit, $fields);
    $user_id=$params['user_id'];
    $join .= db_quote(' LEFT JOIN ?:cp_extended_tags_links ON ?:cp_extended_tags.tag_id = ?:cp_extended_tags_links.tag_id ');

    $fields[]="(SELECT COUNT(tag_id) FROM ?:cp_extended_tags_links WHERE tag_id=?:cp_extended_tags.tag_id AND object_type='$object_type' AND user_id='$user_id') as popularity";

    if (!empty($params['items_per_page'])) {
        $params['total_items'] = db_get_field("SELECT COUNT(tag) FROM ?:cp_extended_tags $join WHERE 1 $condition");
        $limit = db_paginate($params['page'], $params['items_per_page'], $params['total_items']);
    }


    $tags = db_get_hash_array(
        "SELECT ?p FROM ?:cp_extended_tags " .
        $join .
        "WHERE 1 ?p ?p ?p",
        'tag_id', implode(', ', $fields), $condition, $sorting, $limit
    );

    fn_set_hook('get_cp_extended_tags_post', $tags, $params);

    return array($tags, $params);
}


function fn_cp_extended_tags_get_orders($params, $fields, $sortings, &$condition, &$join, &$group){

    if(isset($params['is_search']) && isset($params['tags'])){

            $user_id = $params['user_id'];
            $user_type=$params['user_type'];
            $data=[
                [
                    'tag',
                    'IN',
                    $params['tags']
                ]
            ];
            $tag_ids= db_get_fields('SELECT tag_id FROM ?:cp_extended_tags WHERE ?w',$data);
            $join .= db_quote(" LEFT JOIN ?:cp_extended_tags_links as links ON ?:orders.order_id=links.object_id");
            $condition = db_quote(" AND links.tag_id IN (?n)",$tag_ids);
            $condition .= db_quote(" AND links.user_type=?s", $user_type);
            $condition .= db_quote(" AND links.user_id=?i", $user_id);

            $group = ' GROUP BY ?:orders.order_id';
    }


    if(isset($params['tag'])){

        $tag=$params['tag'];
        $tag_id=db_get_field('SELECT tag_id FROM ?:cp_extended_tags WHERE tag=?s',$tag);

        $data=[
            'tag_id'=>$tag_id,
            'object_type'=>'O',
            'user_type'=>$params['cp_user_type'],
            'user_id'=>$params['cp_user_id'],

        ];
        $order_ids=db_get_fields('SELECT object_id FROM ?:cp_extended_tags_links WHERE ?w',$data);

        $condition .= db_quote(" AND ?:orders.order_id IN (?n)",$order_ids);
    }

}
function fn_cp_extended_tags_update_order_details_post($params, $order_info):void
{

    $auth=Tygh::$app['session']['auth'];
    $user_type=$auth['user_type'];
    $user_id= $auth['user_id'] ?? 0;
    $object_type='O';
    $order_id = $params['order_id'];
    if(!empty($params['tags'])) {
        $tags = $params['tags'];
        fn_cp_extended_tags_manage_tags($tags, $order_id, $object_type,$user_type,$user_id);
        if (!empty($params['order_tags'])) {

            $exist_tags = array_keys($params['order_tags']);
            fn_cp_extended_tags_delete_tags_links_by_ids($tags, $exist_tags, $order_id, $object_type,$user_type,$user_id);
        }
    }else{
        $tags=[];
        $exist_tags = array_keys($params['order_tags']);
        fn_cp_extended_tags_delete_tags_links_by_ids($tags,$exist_tags,$order_id,$object_type,$user_type,$user_id);
    }
    fn_cp_extended_tags_delete_tags();
}

function fn_cp_extended_tags_manage_tags($tags,$object_id,$object_type,$user_type,$user_id){

    foreach ($tags as $tag) {
        if (!empty($tag)) {
            //Checking if there is tag with that name in db
            if (!empty(fn_cp_extended_tags_check_if_exist($tag))) {

                $tag_id = fn_cp_extended_tags_check_if_exist($tag);

                //Checking if there is a link to this tag
                if (!empty(fn_cp_extended_tags_check_ids_match($tag_id, $object_id, $object_type, $user_type,$user_id))) {
                    //Updating tag info
                    $tag_status=fn_cp_extended_tags_get_status($tag_id, $object_id, $object_type, $user_type,$user_id);

                    fn_cp_extended_tags_update_tags_info($tag_id,$tag_status, $object_id,$user_type,$user_id);
                } else {
                    //Adding a link to an existing tag

                    fn_cp_extended_tags_add_link($tag_id, $object_id, $object_type, $user_type,$user_id);
                }
            } else {
                //Adding tag
                fn_cp_extended_tags_add_tag($object_id, $object_type, $tag, $user_type,$user_id);
            }
        }

    }
}

function fn_cp_extended_tags_get_distinct_tag($object_type,$user_id):array
{
    $data=[
        'object_type'=>$object_type,
        'user_id'=>$user_id
    ];

    return db_get_fields("SELECT  DISTINCT(tag) FROM ?:cp_extended_tags LEFT JOIN ?:cp_extended_tags_links as links ON ?:cp_extended_tags.tag_id=links.tag_id WHERE ?w",$data);
}
function fn_cp_extended_tags_get_object_tags_data($id,$object_type,$user_type,$user_id):array
{
    $data=array(
        'object_id'=>$id,
        'object_type'=>$object_type,
        'user_type'=>$user_type,
        'user_id'=>$user_id
    );
    return  db_get_array("SELECT * FROM ?:cp_extended_tags LEFT JOIN ?:cp_extended_tags_links as links ON ?:cp_extended_tags.tag_id=links.tag_id WHERE ?w",$data);

}
function fn_cp_extended_tags_get_users($params, $fields, $sortings, &$condition, &$join, $auth){

    if(isset($params['is_search']) && isset($params['tags'])){

            $user_type=$params['user_type'];
            $user_id=$params['user_id'];
            $join=db_quote('LEFT JOIN ?:cp_extended_tags as tags ON ?:cp_extended_tags_links.tag_id=tags.tag_id');
            $search_tags=$params['tags'];
            $ids=db_get_fields('SELECT object_id  FROM ?:cp_extended_tags_links ?p WHERE tag in (?a) AND object_type=?s',$join,$search_tags,$params['user_type']);
            if(isset($ids)){
                $condition=db_quote(' AND ?:users.user_id IN (?n)', $ids);
                $condition .= db_quote(" AND links.user_type=?s", $user_type);
                $condition .= db_quote(" AND links.user_id=?i", $user_id);
            }

    }
    if(isset($params['tag'])){
        $tag=$params['tag'];
        $tag_id=db_get_field('SELECT tag_id FROM ?:cp_extended_tags WHERE tag=?s',$tag);

        $data=[
            'tag_id'=>$tag_id,
            'object_type'=>'C',
            'user_type'=>$params['cp_user_type'],
            'user_id'=>$params['cp_user_id']
        ];
        $user_ids=db_get_fields('SELECT object_id FROM ?:cp_extended_tags_links WHERE ?w',$data);

        $condition['filter_id']= db_quote(" AND ?:users.user_id IN (?n)", $user_ids);

    }
}

function fn_cp_extended_tags_get_status($tag_id, $object_id, $object_type, $user_type,$user_id)
{
    $data=[
        'tag_id'=>$tag_id,
        'object_id'=>$object_id,
        'object_type'=>$object_type,
        'user_type'=>$user_type,
        'user_id'=>$user_id
    ];
    return db_get_field('SELECT status FROM ?:cp_extended_tags_links WHERE ?w',$data);
}
function fn_cp_extended_tags_update_tags_info($tag_id,$tag_status,$object_type,$user_type,$user_id):void
{
    $data=[
        'user_type'=>$user_type,
        'user_id'=>$user_id,
        'object_type'=>$object_type,
        'tag_id'=>$tag_id,
    ];
    $tag_data = [
            'status'=> $tag_status,
    ];

    db_query('UPDATE ?:cp_extended_tags_links SET ?u WHERE ?w', $tag_data, $data);
}

function fn_cp_extended_tags_add_tag($object_id,$object_type,$tag,$user_type,$user_id):void
{
    $tag_data = array(
        'tag' => $tag,
        'timestamp'=>TIME,
        'status'=>'D'
    );
    db_query("INSERT INTO ?:cp_extended_tags ?e",$tag_data);
    $data=array(
        'tag' => $tag,
    );
    $tag_id=db_get_field('SELECT tag_id FROM ?:cp_extended_tags WHERE ?w',$data);
    $tag_link_data = array(
        'tag_id'=>$tag_id,
        "object_id" => $object_id,
        "object_type"=> $object_type,
        'user_type' =>$user_type,
        'user_id'=>$user_id
    );

    db_query("INSERT INTO ?:cp_extended_tags_links ?e",$tag_link_data);

}

function fn_cp_extended_tags_check_if_exist($tag):string
{
    $tag_data=array(
        'tag'=>$tag,

    );
          return  db_get_field("SELECT tag_id FROM ?:cp_extended_tags WHERE ?w",$tag_data);
}

function fn_cp_extended_tags_check_ids_match($tag_id,$object_id,$object_type,$user_type,$user_id)
{
    $tag_data=array(
        'tag_id'=>$tag_id,
        'object_id'=>$object_id,
        'object_type'=>$object_type,
        'user_type'=>$user_type,
        'user_id'=>$user_id
    );
    return db_get_field("SELECT tag_id FROM ?:cp_extended_tags_links WHERE ?w",$tag_data);
}
function fn_cp_extended_tags_add_link($tag_id,$object_id,$object_type,$user_type,$user_id):void
{
    $tag_link_data = array(
        'tag_id'=>$tag_id,
        'object_id'=>$object_id,
        'object_type'=>$object_type,
        'user_type'=>$user_type,
        'user_id'=>$user_id
    );

    db_query("INSERT INTO ?:cp_extended_tags_links ?e",$tag_link_data);
}
function fn_cp_extended_tags_delete_tags_links_by_ids($tags,$exist_tags,$object_id,$object_type,$user_type,$user_id):void
{
    if(isset($tags)){
        foreach ($exist_tags as $tag) {
            if (!in_array($tag, $tags)) {
                $tag_links_to_delete[] = $tag;
                }
            }
        }
        else {
            foreach ($exist_tags as $tag) {
                $tag_links_to_delete[] = $tag;
            }
        }

    if (!empty($tag_links_to_delete)) {
        $data=[
            [
                'tag',
                'IN',
                $tag_links_to_delete,
            ]
        ];
        $ids_to_delete=db_get_fields('SELECT tag_id FROM ?:cp_extended_tags WHERE ?w',$data);
        $data=[
            'user_type'=>$user_type,
            'user_id'=>$user_id,
            'object_id'=>$object_id,
            'object_type'=>$object_type,
            [
                'tag_id',
                'IN',
                $ids_to_delete,
            ]
        ];
        db_query('DELETE FROM ?:cp_extended_tags_links  WHERE ?w',$data);
    }

}
function fn_cp_extended_tags_delete_tags():void
{
    $links=db_get_fields('SELECT DISTINCT(tag_id) FROM ?:cp_extended_tags_links');
    db_query('DELETE FROM ?:cp_extended_tags WHERE tag_id NOT IN (?n)',$links);
}

function fn_cp_extended_tags_delete_tags_before_import($object_id,$object_type,$user_id,$user_type):void
{
    $data=[
        'object_id'=>$object_id,
        'object_type'=>$object_type,
        'user_type'=>$user_type,
        'user_id'=>$user_id
    ];
    $db_tags=db_get_fields('SELECT tag_id FROM ?:cp_extended_tags_links WHERE ?w',$data);
    if(!empty($db_tags)){
        foreach($db_tags as $tag){
            fn_delete_tag_by_id($tag,$object_type,$user_type,$user_id);
        }
    }
}

function fn_delete_tag_by_id($tag_id,$object_type,$user_type,$user_id):void
{
    $tag_id = is_array($tag_id) ? $tag_id : (array) $tag_id;
    $data=[
        'object_type'=>$object_type,
        'user_type'=>$user_type,
        'user_id'=>$user_id,
            [
                'tag_id',
                'IN',
                $tag_id
            ]
    ];
        db_query('DELETE FROM ?:cp_extended_tags_links WHERE ?w',$data);
        $ids_collector=db_get_fields('SELECT tag_id FROM ?:cp_extended_tags_links WHERE tag_id IN (?n)',$tag_id);
        if(empty($ids_collector)){
            db_query('DELETE FROM ?:cp_extended_tags WHERE tag_id IN (?n)',$ids_collector);
        }

}

function fn_cp_extended_tags_get_tag_names($params,$user_id,$user_type)
{
    $join = db_quote("LEFT JOIN ?:cp_extended_tags_links ON ?:cp_extended_tags.tag_id = ?:cp_extended_tags_links.tag_id");
    $conditions=db_quote(" AND ?:cp_extended_tags_links.user_id=?i",$user_id);
    $conditions.=db_quote(" AND ?:cp_extended_tags_links.user_type=?s",$user_type);
    $conditions .= db_quote(" AND ?:cp_extended_tags.tag LIKE ?l", "%".trim($params['tag'])."%");
    $tags=db_get_fields("SELECT DISTINCT tag FROM ?:cp_extended_tags ?p WHERE 1 ?p",$join,$conditions);

        return $tags;
}






