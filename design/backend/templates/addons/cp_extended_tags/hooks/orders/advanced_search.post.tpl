<div class="group">

<div class="row-fluid">
    <div class="control-group">
        <label class="control-label" for="tags">{__("tag")}</label>
        <tr valign="top" class="delim">
            <td width="350px" data-th="test">
                <div class="object-selector object-selector--mobile-full-width object-selector--half-width">
                    <select id="tags" class="cm-object-selector select2-hidden-accessible" multiple=""
                            name="tags[]" data-ca-placeholder={__("browse")} data-ca-enable-images="true" data-ca-image-width="30"
                            data-ca-image-height="30" data-ca-enable-search="true" data-ca-page-size="10"
                            data-ca-data-url="dispatch=product_features.get_variants_list"
                            data-ca-close-on-select="false" data-select2-id="feature_variants_550" tabindex="-1" aria-hidden="true">
                        {foreach from=$tags item=$tag}
                            <option value="{$tag}" {if $tag|in_array:$search.tags} selected="selected"{/if}>{$tag}</option>
                        {/foreach}
                    </select>
                </div>
            </td>

        </tr>
    </div>
</div>
</div>