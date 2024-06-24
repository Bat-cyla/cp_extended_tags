
{include file="common/subheader.tpl" title=__("extended_tags")}

{script src="js/addons/tags_ext/tags_autocomplete.js"}
{$company_id=fn_get_runtime_company_id()}
<div class="control-group {if $allow_save}cm-no-hide-input{/if}">
    <label class="control-label">{__("tags")}:</label>
    <div class="controls">
        <ul id="my_tags">
            {foreach $user_data.tags as $tag}
                <textarea name="user_tags[{$tag.tag_id}]" type="text" data-ca-tags="TagsValues" class="hidden-accessible">{$tag.tag}{if !$tag@last}{/if}</textarea>
            {/foreach}
            <input type="hidden" id="object_name" value="user_data[tags][]" />
            {foreach from=$user_data.tags item="tag" name="tag"}
                <li>{$tag.tag}</li>
            {/foreach}
        </ul>
    </div>
</div>

{capture name="buttons"}
    {if $tags}
        {include file="buttons/save.tpl" but_name="dispatch[profiles.update]" but_role="submit-link" but_target_form="profile_form"}
    {/if}
{/capture}