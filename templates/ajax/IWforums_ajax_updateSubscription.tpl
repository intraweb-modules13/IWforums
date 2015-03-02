{if isset($fid)}
    {switch expr=$action}
        {case expr='none'}
            <span style="cursor:pointer" class="blue glyphicon glyphicon-comment" data-toggle="tooltip" title="{gt text="Everybody is subscribed"}"  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$fid}"'></span>
        {/case}
        {case expr='add'}
            <span style="cursor:pointer" class="green glyphicon glyphicon-ok-circle" data-toggle="tooltip" title="{gt text="Subscribe me to this forum"}" onclick="changeSubscription({$fid}, 1)"></span>
        {/case}
        {case expr='cancel'}
            <span style="cursor:pointer" class="red glyphicon glyphicon-remove-circle" data-toggle="tooltip" title="{gt text="Cancel my subscription"}" onclick="changeSubscription({$fid}, 0)"></span>
        {/case} 
    {/switch}
{else}                                                            
    <span style="cursor:pointer" class="glyphicon glyphicon-ban-circle" data-toggle="tooltip" title="{gt text="This forum not allow subscriptions"}"  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$fid}"'></span>
{/if}