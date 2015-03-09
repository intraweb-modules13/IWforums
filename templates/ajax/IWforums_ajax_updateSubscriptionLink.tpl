{if isset($fid)}
    {switch expr=$action[$fid].action}
        {case expr='add'}
            <a style="float:right;" href="javascript:changeSubscription({$fid}, {'IWforums_Constant::SUBSCRIBE'|constant}, 'IWforums_ajax_updateSubscriptionLink.tpl')">{gt text="Subscribe me to this forum"}</a>
        {/case}
        {case expr='cancel'}
            <a style="float:right;" href="javascript:changeSubscription({$fid}, {'IWforums_Constant::UNSUBSCRIBE'|constant}, 'IWforums_ajax_updateSubscriptionLink.tpl' )">{gt text="Cancel my subscription"}</a>
        {/case} 
    {/switch}
{/if}
