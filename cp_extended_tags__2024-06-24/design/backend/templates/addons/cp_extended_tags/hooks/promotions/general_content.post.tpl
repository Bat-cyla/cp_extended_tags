

<div class="control-group">
    <label class="control-label" for="elm_promotion_tags">{__("adding_a_tag")}:</label>
        <div class="controls">
            <input type="hidden" name="promotion_data[tags]" value="{"YesNo::NO"|enum}" />
            <input type="checkbox" name="promotion_data[tags]" id="elm_promotion_tags" value="{"YesNo::YES"|enum}" {if $promotion_data.tags == "YesNo::YES"|enum}checked="checked"{/if}/>
        </div>
</div>
