{capture name="mainbox"}
    {assign var="object_type" value=$object_type}
    {$c_url=$config.current_url|fn_query_remove:"sort_by":"sort_order"}
    {$tags_statuses=""|fn_get_default_statuses:false}
    {$rev=$smarty.request.content_id|default:"pagination_contents_tags"}
    {include_ext file="common/icon.tpl" class="icon-`$search.sort_order_rev`" assign=c_icon}
    {include_ext file="common/icon.tpl" class="icon-dummy" assign=c_dummy}
    <form class="form-horizontal form-edit" action="{""|fn_url}" method="post" name="cp_extended_tags_form">

        {include file="common/pagination.tpl" save_current_page=true save_current_url=true}
        {if $tags}
            {capture name="tags_table"}
                <div class="table-responsive-wrapper longtap-selection">
                    <table width="100%" class="table table-sort table-middle table--relative table-responsive">
                        <thead
                                data-ca-bulkedit-default-object="true"
                                data-ca-bulkedit-component="defaultObject"
                        >
                        <tr>
                            <th class="left mobile-hide" width="6%">
                                {include file="common/check_items.tpl" check_statuses=$tags_statuses}

                                <input type="checkbox"
                                       class="bulkedit-toggler hide"
                                       data-ca-bulkedit-disable="[data-ca-bulkedit-default-object=true]"
                                       data-ca-bulkedit-enable="[data-ca-bulkedit-expanded-object=true]"
                                />
                            </th>
                            <th width="40%">
                                <a class="cm-ajax"
                                   href="{"`$c_url`&sort_by=tag&sort_order=`$search.sort_order_rev`"|fn_url}"
                                   data-ca-target-id="pagination_contents">
                                    {__("tag")}
                                    {if $search.sort_by=="tag"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}
                                </a>
                            </th>
                            <th width="8%" class="center">
                                <a class="cm-ajax"
                                   href="{"`$c_url`&sort_by=popularity&sort_order=`$search.sort_order_rev`"|fn_url}"
                                   data-ca-target-id="pagination_contents">
                                    {if $object_type=='O'}
                                    {__('orders')}
                                    {else}
                                    {__('customers')}
                                    {/if}
                                    {if $search.sort_by=="popularity"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}
                                </a></th>
                            <th width="8%">&nbsp;</th>
                            <th width="8%" class="right">
                                <a class="cm-ajax"
                                   href="{"`$c_url`&sort_by=status&sort_order=`$search.sort_order_rev`"|fn_url}"
                                   data-ca-target-id="pagination_contents">{__('order_list_display')}
                                    {if $search.sort_by=="status"}{$c_icon nofilter}{else}{$c_dummy nofilter}{/if}
                                </a>
                            </th>
                        </tr>
                        </thead>
                        {foreach from=$tags item="tag"}
                        <tbody>
                        <tr class="cm-row-status-{$tag.status|lower} cm-longtap-target"
                            data-ca-longtap-action="setCheckBox"
                            data-ca-longtap-target="input.cm-item"
                            data-ca-id="{$tag.tag_id}"
                        >
                            <td width="6%" class="left mobile-hide">

                            </td>
                            <td width="40%" data-th="{__("tag")}">
                                <input type="text" value="{$tag.tag}" size="20" class="input-hidden">
                            </td>

                                <td class="center" width="8%" ">
                                  {$tag.popularity}
                                </td>
                            <td width="8%" ">
                            </td>
                                <td class="center" width="8%" >
                                    <input type="hidden" value="{$tag.tag_id}" name="tags_ids[]">
                                <input type="checkbox" name="active_tags_ids[]" value="{$tag.tag_id}"
                                            {if $tag.status == 'A'}
                                                checked
                                            {/if}
                                />
                                </td>
                            <td width="8%" data-th="{__("tools")}">
                                <div class="hidden-tools">
                                    {capture name="tools_list"}
                                        <li>{btn type="list" class="cm-confirm" text=__("delete") href="cp_extended_tags.delete?tag_id=`$tag.tag_id`?object_type={$object_type}" method="POST"}</li>
                                    {/capture}
                                    {dropdown content=$smarty.capture.tools_list}
                                </div>
                            </td>
                        </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
            {/capture}

            {include file="common/context_menu_wrapper.tpl"
            form="tags_form"
            object="tags"
            items=$smarty.capture.tags_table
            }

        {else}
            <p class="no-items">{__("no_data")}</p>
        {/if}

        {include file="common/pagination.tpl"}

    </form>


{/capture}

{capture name="buttons"}
    {capture name="tools_list"}
        {if $tags}
            {hook name="tags:list_extra_links"}
            {/hook}
        {/if}
    {/capture}
    {dropdown content=$smarty.capture.tools_list}
    {if $tags}
        {include file="buttons/save.tpl" but_name="dispatch[cp_extended_tags.m_update]" but_role="submit-link" but_target_form="cp_extended_tags_form"}
    {/if}
{/capture}

{capture name="sidebar"}
    {include file="addons/cp_extended_tags/views/cp_extended_tags/components/tags_search_form.tpl" dispatch="cp_extended_tags.manage"}
{/capture}

{include file="common/mainbox.tpl" title=__("tags") content=$smarty.capture.mainbox buttons=$smarty.capture.buttons sidebar=$smarty.capture.sidebar}