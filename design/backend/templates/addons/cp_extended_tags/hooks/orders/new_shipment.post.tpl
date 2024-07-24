<div class="control-group shift-top">
    {include file="common/subheader.tpl" title=__("Tags")}
</div>
    {script src="js/addons/cp_extended_tags/tags_autocomplete.js"}
    <div class="cp-control-group {if $allow_save}cm-no-hide-input{/if}">
        <div class="cp-controls">
            <span class="cp-input">
            <ul id="my_tags" class="cp-input">
                {foreach $order_info.tags as $tag}
                <textarea name="order_tags[{$tag.tag_id}]" type="text" data-ca-tags="TagsValues" class="hidden-accessible">{$tag.tag}{if !$tag@last}{/if}</textarea>
                {/foreach}
                <input type="hidden" id="object_name" value="tags[]"/>
                {foreach from=$order_info.tags item="tag" name="tag"}
                    <li>{$tag.tag}</li>
                {/foreach}
            </ul>
            </span>
        </div>
    </div>
