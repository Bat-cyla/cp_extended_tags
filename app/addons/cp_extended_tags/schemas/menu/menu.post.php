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

$schema['central']['orders']['items']['extended_tags_orders'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'cp_extended_tags.manage?object_type=O',
    'position' => 400
);
$schema['central']['customers']['items']['extended_tags_customers'] = array(
    'attrs' => array(
        'class'=>'is-addon'
    ),
    'href' => 'cp_extended_tags.manage?object_type=C',
    'position' => 400
);

return $schema;
