{include file="IWforums_admin_menu.tpl"}
<div class="z-admincontainer">
    <div class="z-adminpageicon">{img modname='core' src='windowlist.png' set='icons/large'}</div>
    <div style="height:20px;">&nbsp;</div>
    <h2>{gt text="Forums list"}</h2>
    <table class="z-datatable">
        <thead>
            <tr>
                <th>{gt text="Forum name"}</th>
                <th>{gt text="Description"}</th>
                <th>{gt text="Group"}</th>
                <th>{gt text="Moderators"}</th>
                <th>{gt text="Features"}</th>
                <th>{gt text="Observations"}</th>
                <th>{gt text="Options"}</th>
            </tr>
        </thead>
        <tbody>
            {foreach item=forum from=$forums}
            <tr class="{cycle values="z-odd,z-even"}">
                <td align="left" valign="top">{$forum.nom_forum}</td>
                 <td align="left" valign="top">{$forum.descriu}</td>
                 <td align="left" valign="top" style="text-align:right;">
                     {foreach item=group from=$forum.groups}
                     <div id="groupId_{$group.id}_{$forum.fid}">
                         {$group.groupName} =>
                         {if $group.accessType eq 2}
                         {gt text="Read and write"}
                         {elseif $group.accessType eq 3}
                         {gt text="Read, write and topics creation"}
                         {elseif $group.accessType eq 4}
                         {gt text="Moderation"}
                         {else}
                         {gt text="Read only"}
                         {/if}
                         <a href="javascript:deleteGroup('{$group.id}',{$forum.fid})">
                             {img modname='core' src='delete_group.png' set='icons/extrasmall' __alt="Delete" __title="Delete"}
                         </a>
                     </div>
                     {/foreach}
                     <div class="formOptions"  style="float: right;">
                     <a href="{modurl modname='IWforums' type='admin' func='addGroup' fid=$forum.fid}">
                         {img modname='core' src='add_group.png' set='icons/extrasmall' __alt="Add a group with access to the forum" __title="Add a group with access to the forum"}
                     </a>
                 </div>
             </td>
             <td valign="top"  style="text-align:right;">
                 {foreach item=mod from=$forum.mods}
                 <div id="mod_{$forum.fid}_{$mod.id}">
                     {$mod.name}
                     <a href="javascript:deleteModerator({$forum.fid},{$mod.id})">
                         {img modname='core' src='delete_user.png' set='icons/extrasmall' __alt="Delete" __title="Delete"}
                     </a>
                 </div>
                 {/foreach}
                 <div class="formOptions" style="float: right;">
                     <a href="{modurl modname='IWforums' type='admin' func='addModerator' fid=$forum.fid}">
                         {img modname='core' src='add_user.png' set='icons/extrasmall' __alt="Add a moderator" __title="Add a moderator"}
                     </a>
                 </div>
             </td>
             <td align="left" valign="top">
                 <div id="forumChars_{$forum.fid}" name="forumChars_{$forum.fid}">
                     {include file="IWforums_admin_mainChars.tpl" forum=$forum}
                 </div>
             </td>
             <td align="left" valign="top">{$forum.observacions|nl2br}</td>
             <td align="left" valign="top">
                 <div>
                     <a href="{modurl modname='IWforums' type='admin' func='newItem' fid=$forum.fid m=e}">
                         {gt text="Edit"}
                     </a>
                 </div>
                 <div>
                     <a href="{modurl modname='IWforums' type='admin' func='delete' fid=$forum.fid}">
                         {gt text="Delete"}
                     </a>
                 </div>
                 <div>
                     <a href="{modurl modname='IWforums' type='admin' func='newItem' fid='$forum.fid' m=c}">
                         {gt text="Copy"}
                     </a>
                 </div>
             </td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="10">
                    {gt text="There are no forum created"}
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</div>
<script type="text/javascript">
    var modifyingfield = '{{gt text="...modifying..."}}';
    var deleteConfirmation = '{{gt text="Confirm the deletion"}}';
    var deleteModConfirmation = '{{gt text="Delete moderator"}}';
</script>