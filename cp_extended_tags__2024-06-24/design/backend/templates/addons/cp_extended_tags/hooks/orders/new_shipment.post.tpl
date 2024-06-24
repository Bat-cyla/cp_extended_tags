
    {script src="js/addons/tags_ext/tags_autocomplete.js"}
    <div class="control-group {if $allow_save}cm-no-hide-input{/if}">
        <label class="control-label">{__("tags")}:</label>
        <div class="controls">
            <ul id="my_tags">
                {foreach $order_info.tags as $tag}
                <textarea name="order_tags[{$tag.tag_id}]" type="text"  data-ca-tags="TagsValues" class="hidden-accessible">{$tag.tag}{if !$tag@last}{/if}</textarea>
                {/foreach}
                <input type="hidden" id="object_name" value="tags[]"/>
                {foreach from=$order_info.tags item="tag" name="tag"}
                    <li>{$tag.tag}</li>
                {/foreach}

            </ul>
        </div>
    </div>
