<a href="javascript:modifyField({$forum.fid},'adjunts')" title="{gt text="Modify"}" style="font-weight: 400;">
    {gt text="Attachments?"}
    {if $forum.adjunts}
        <span class="active">{gt text="Yes"}</span>
    {else}
        <span class="inactive">{gt text="No"}</span>
    {/if}
</a>
<br />
<a href="javascript:modifyField({$forum.fid},'actiu')" title="{gt text="Modify"}" style="font-weight: 400;">
    {gt text="Active?"}
    {if $forum.actiu}
        <span class="active">{gt text="Yes"}</span>
    {else}
        <span class="inactive">{gt text="No"}</span>
    {/if}
</a>
<br />
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
<br />
{gt text="Subscriptions"}:
{if $forum.subscriptions eq 0}
    {gt text="Not allowed"}
{elseif $forum.subscriptions eq 1}
    {gt text="By default"}
{elseif $forum.subscriptions eq 2}
    {gt text="Not by default"}
{elseif $forum.subscriptions eq 3}
    {gt text="Forced"}
{/if}
<div id="foruminfo_{$forum.fid}" class="z-hide z-noteinfo">&nbsp;</div>
