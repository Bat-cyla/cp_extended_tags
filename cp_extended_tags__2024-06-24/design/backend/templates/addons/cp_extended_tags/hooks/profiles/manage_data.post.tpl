
<td>
    {foreach from=$user.tags item=$tag name=user}
        {if $tag.status == 'A'}
        {if $smarty.foreach.user.last}
            {$tag.tag}
            {else} {$tag.tag},
        {/if}
        {/if}
    {/foreach}
</td>
