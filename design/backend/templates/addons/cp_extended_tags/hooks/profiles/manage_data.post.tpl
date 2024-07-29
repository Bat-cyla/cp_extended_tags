
<td>
    {foreach from=$user.tags item=$tag name=user}
        {if $tag.status == 'A'}
            {$active_tags[]=$tag.tag}
        {/if}
    {/foreach}
    {foreach from=$active_tags item=$tag name=tags}
        {if $smarty.foreach.tags.last}
            {$tag}
        {else}
            {$tag},
        {/if}
    {/foreach}
</td>
