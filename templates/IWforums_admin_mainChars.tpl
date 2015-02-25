<a href="javascript:modifyField({$forum.fid},'adjunts')" title="{gt text="Modify"}" data-toggle="tooltip" style="font-weight: 400;">
   {gt text="Attachments?"}
   {if $forum.adjunts}
   <span class="active">{gt text="Yes"}</span>
    {else}
    <span class="inactive">{gt text="No"}</span>
    {/if}
</a>
<br />
<a href="javascript:modifyField({$forum.fid},'actiu')" title="{gt text="Modify"}" data-toggle="tooltip" style="font-weight: 400;">
   {gt text="Active?"}
   {if $forum.actiu}
   <span class="active">{gt text="Yes"}</span>
    {else}
    <span class="inactive">{gt text="No"}</span>
    {/if}
</a>
<br />
<div id="subscriptionType_{$forum.fid}" data-toggle="tooltip" title="{$forum.subscrModeText.explanation}.&nbsp;{gt text="Click to modify"}">
    <span class="subscriptionMode btn btn-primary btn-xs" style="cursor:pointer;" data-toggle="modal" data-target="#selectSubscriptionMode" data-fid="{$forum.fid}" data-mode="{$forum.subscrModeText.val}">
        <span>{$forum.subscrModeText.type}</span>
    </span>
</div>
{*<a href="javascript:changeSubscrMode({$forum.subscrModeText.val});" title="{$forum.subscrModeText.explanation}" data-toggle="tooltip">{$forum.subscrModeText.type}</a>*}
{if $forum.msgEditTime neq 0}
    {gt text="Editable"} {$forum.msgEditTime} {gt text="minutes"}
{else}
    {gt text="No"} {gt text="Editable"}
{/if}
<br />
{if $forum.msgDelTime neq 0}
    {gt text="Deletable"} {$forum.msgDelTime} {gt text="minutes"}
{else}
    {gt text="No"} {gt text="Deletable"}
{/if}
<div id="foruminfo_{$forum.fid}" class="z-hide z-noteinfo">&nbsp;</div>
