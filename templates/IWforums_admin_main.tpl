{pageaddvar name='javascript' value='jQuery'}
{pageaddvar name='javascript' value='vendor/bootstrap/js/bootstrap.js'}
{pageaddvar name='stylesheet' value='vendor/bootstrap/css/bootstrap.css'}
{pageaddvar name='stylesheet' value='modules/IWforums/style/bsRewrite.css'}
{include file="IWforums_admin_menu.htm"}
<div class="z-admincontainer">
    {*<div class="z-adminpageicon">{img modname='core' src='windowlist.png' set='icons/large'}
    </div>*}

    <h2><span class="glyphicon glyphicon-list"></span>&nbsp;{gt text="Forums list"}</h2>
    <table class="table">
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
                <td align="left" valign="top">
                    <a href="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}">
                        {$forum.nom_forum}
                    </a>
                </td>
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
                        <span class="fs1em glyphicon glyphicon-pencil" data-toggle="tooltip" title='{gt text="Edit"}'></span></a>
                    <a href="{modurl modname='IWforums' type='admin' func='newItem' fid=$forum.fid m=c}">
                        <span class="fs1em glyphicon glyphicon-tags" data-toggle="tooltip" title="{gt text="Duplicate"}"></span></a>
                    <a href="{modurl modname='IWforums' type='admin' func='delete' fid=$forum.fid}">
                        <span class="fs1em glyphicon glyphicon-remove" data-toggle="tooltip" data-placement="left" title="{gt text="Delete"}"></span></a> 
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
        
<!-- Modal for IWforums_admin_mainChars.tpl. Change forum subscription mode -->
    <div class="modal fade" id="selectSubscriptionMode">
        <div class="modal-dialog" style="top:25%">
            <div class="modal-content">
                <div class="modal-header btn-primary" style=" padding-top:5px; height:35px;">                            
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></i>&nbsp;{gt text="Change subscription type"}</h4>                             
                </div>
                <div class="modal-body">
                    <form>
                        <input type="hidden" id="fid">
                        <div class="form-group">
                            <label class="control-label"  for="subscrType">{gt text="Select forum subscription type"} </label>           
                            <select class="form-control" id="subscrMode" name="subscrMode">                                
                                <option value="0" {if isset($forum.subscriptionMode) && $forum.subscriptionMode eq 0}selected{/if}>{gt text = "Nobody can subscribe to this forum (No subscription)"}</option>
                                <option value="1" {if isset($forum.subscriptionMode) && $forum.subscriptionMode eq 1}selected{/if}>{gt text = "Users must subscribe to the forum (Voluntary)"}</option>
                                <option value="2" {if isset($forum.subscriptionMode) && $forum.subscriptionMode eq 2}selected{/if}>{gt text = "All users are subscribed by default and may unsubscribe (Optional)"}</option> 
                                <option value="3" {if isset($forum.subscriptionMode) && $forum.subscriptionMode eq 3}selected{/if}>{gt text = "All users are subscribed by default but can't unsubscribe (Compulsory)"}</option> 
                            </select> 
                        </div>
                    </form>   
                </div>
                <div class="modal-footer">
                    <button id="btnDelete" type="button" class="btn btn-success" data-dismiss="modal" onclick="setSubscriptionMode()"><span class="white fs1em glyphicon glyphicon-ok"></span>&nbsp;{gt text="Ok"}</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><span class="white fs1em glyphicon glyphicon-remove"></span>&nbsp;{gt text="Cancel"}</button>                            
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->         
<script type="text/javascript">
    var modifyingfield = '{{gt text="...modifying..."}}';
    var deleteConfirmation = '{{gt text="Confirm the deletion"}}';
    var deleteModConfirmation = '{{gt text="Delete moderator"}}';

    jQuery('[data-toggle="tooltip"]').tooltip();
    
    jQuery(".btnSubscriptionMode").click(function(){ 
        jQuery("#fid").val(jQuery(this).data('fid'));
        jQuery("#subscrMode option[value="+jQuery(this).data('mode')+"]").attr('selected', 'selected');
        // Applies to modal
        /*var ftid = jQuery(this).data('ftid');
        jQuery('#fid').val(jQuery(this).data('fid'));
        jQuery('#ftid').val(jQuery(this).data('ftid'));
        jQuery("#UserInfo").html(jQuery('#startedBy').html());
        jQuery("#MsgsInfo").html(jQuery('#totalMessages'+ftid).html()+'/'+jQuery('#totalUnread'+ftid).html());
        
        jQuery("#subscrMode").html(jQuery('#topicDesc'+ftid).html());*/

    });
</script>