<div class="z-informationmsg">
    {if $subscriptionLevel eq 0}
        {gt text="You are not subscribed to this forum."}
    {else}
        {gt text="You are subscribed to this forum."}
    {/if}
    {if $subscriptions eq 1 || $subscriptions eq 2}
        {if $subscriptionLevel eq 0}
            <a href="{modurl modname='IWforums' type='user' func='setSubscription' fid=$fid}">
                {gt text="Subscribe to it"}.
            </a>
        {else}
            <a href="{modurl modname='IWforums' type='user' func='setSubscription' fid=$fid}">
                {gt text="Unsubscribe from it"}.
            </a>
        {/if}
    {else}
        {*subscriptions are forced*}
    {/if}
    {if $subscriptionLevel gt 0 && $sendByCron == 1}
        <div style="float: right;">
            <form id="subscriptionForm" action="{modurl modname='IWforums' type='user' func='setSubscription'}" method="post">
                <input type="hidden" name="fid" value="{$fid}" />
                <select name="subscriptionMethod" onchange="document.forms['subscriptionForm'].submit();">
                    <option {if $subscriptionLevel eq 1}selected="selected"{/if} value="1">{gt text="Send me a message for each new entry"}</option>
                    <option {if $subscriptionLevel eq 2}selected="selected"{/if} value="2">{gt text="Send me daily summary of messages"}</option>
                </select>
            </form>
        </div>
    {/if}
</div>