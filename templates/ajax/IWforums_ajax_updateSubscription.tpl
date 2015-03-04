{if isset($fid)}
    {switch expr=$action}
        {case expr='none'}
            <span style="font-size:1.2em; cursor:default" class="green fa fa-check" data-toggle="tooltip" title="{gt text="Everybody is subscribed"}" onclick="javascript:void(0);"></span>
        {/case}
        {case expr='add'}
            <span style="font-size:1.2em; cursor:pointer" class="disabled fa fa-check-square-o" data-toggle="tooltip" title="{gt text="Subscribe me to this forum"}" onclick="changeSubscription({$fid}, {'IWforums_Constant::SUBSCRIBE'|constant})"></span>
        {/case}
        {case expr='cancel'}
            <span style="font-size:1.2em; cursor:pointer" class="green fa fa-check-square-o" data-toggle="tooltip" title="{gt text="Cancel my subscription"}" onclick="changeSubscription({$fid}, {'IWforums_Constant::UNSUBSCRIBE'|constant} )"></span>
        {/case} 
    {/switch}
{else}                                                            
    <span style="font-size:1.2em; cursor:default" class="glyphicon glyphicon-ban-circle" data-toggle="tooltip" title="{gt text="This forum not allow subscriptions"}" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$fid}"'></span>
{/if}
                        {*
{if !is_null($fid) }
    {switch expr=$action}
        {case expr='none'}
            <span style="cursor:pointer" class="blue glyphicon glyphicon-comment" data-toggle="tooltip" title="{gt text="Everybody is subscribed"}"  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$fid}"'></span>
        {/case}
        {case expr='add'}
            <span style="cursor:pointer" class="green glyphicon glyphicon-ok-circle" data-toggle="tooltip" title="{gt text="Subscribe me to this forum"}" onclick="changeSubscription({$fid}, {'IWforums_constant::SUBSCRIBE'|constant})"></span>
        {/case}
        {case expr='cancel'}
            <span style="cursor:pointer" class="red glyphicon glyphicon-remove-circle" data-toggle="tooltip" title="{gt text="Cancel my subscription"}" onclick="changeSubscription({$fid}, {'IWforums_Constant::UNSUBSCRIBE'|constant})"></span>
        {/case} 
    {/switch}
{else}                                                            
    <span style="cursor:pointer" class="glyphicon glyphicon-ban-circle" data-toggle="tooltip" title="{gt text="This forum not allow subscriptions"}"  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$fid}"'></span>
{/if}
*}