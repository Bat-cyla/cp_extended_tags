
<div class="sidebar-row">
<h6>{__("admin_search_title")}</h6>
<form action="{""|fn_url}" name="tags_search_form" method="get">
    {capture name="simple_search"}
    <div class="sidebar-field">
        <label for="elm_tag">{__("tag")}</label>
        <input type="text" id="elm_tag" name="tag" size="20" value="{$search.tag}" onfocus="this.select();" class="input-text" />
    </div>
    <div class="sidebar-field">
        <input type="hidden" name="object_type" value="{$object_type}">
    </div>
    <div class="sidebar-field">
        <label for="tag_status_identifier">{__("show")}</label>
        <select name="status" id="tag_status_identifier">
            <option value="">{__("all")}</option>
            <option value="A"{if $search.status == "A"} selected="selected"{/if}>{__("active")}</option>
            <option value="D"{if $search.status == "D"} selected="selected"{/if}>{__("disabled")}</option>
        </select>
    </div>
    {/capture}



     {include file="common/advanced_search.tpl" no_adv_link=true simple_search=$smarty.capture.simple_search dispatch=$dispatch view_type="tags"}

    </form>
</div>