a
{$fid}
{$action|@print_r}

{if $action[$fid].action eq "add"} add {/if}
{if isset($fid)}
    {switch expr=$action.action}
        {case expr='add'}
        add
            <span style="font-size:1.2em; cursor:pointer" class="disabled fa fa-check-square-o" data-toggle="tooltip" title="{gt text="Subscribe me to this forum"}" onclick="changeSubscription({$fid}, {'IWforums_Constant::SUBSCRIBE'|constant})"></span>
        {/case}
        {case expr='cancel'}
        cancel
            <span style="font-size:1.2em; cursor:pointer" class="green fa fa-check-square-o" data-toggle="tooltip" title="{gt text="Cancel my subscription"}" onclick="changeSubscription({$fid}, {'IWforums_Constant::UNSUBSCRIBE'|constant} )"></span>
        {/case} 
    {/switch}
{/if}