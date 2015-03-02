{pageaddvar name='stylesheet' value='vendor/bootstrap/css/bootstrap.css'}
{pageaddvar name='stylesheet' value='modules/IWforums/style/bsRewrite.css'}
{pageaddvar name='javascript' value='jQuery'}
{pageaddvar name='javascript' value='vendor/bootstrap/js/bootstrap.js'}
{include file="IWforums_user_menu.htm" start="" end=""}
{userloggedin assign=userid}
{*<pre>{$forumSubscriptions|@print_r}</pre>*}
<div class="usercontainer">
    <div class="userpageicon">{img modname='core' src='windowlist.png' set='icons/large'}</div>
    <h2>{gt text="Forums list"}</h2>
    <div style="height:15px;">&nbsp;</div>
    <table class="table table-striped">
        <thead>
            <tr>
                {if $userid neq ''}
                <th></th>
                {/if}
                    <th>{gt text="Forum name"}</th>
                    <th>{gt text="Description"}</th>
                {if $userid neq ''}
                    <th>{gt text="Access type"}</th>
                    <th></th>
                {/if}
                    <th class="text-center">{gt text="Topics"}</th>
                    <th class="text-center">{gt text="Messages"}</th>
                {if $userid neq ''}
                    <th class="text-center">{gt text="Unreaded"}</th>
                {/if}
            </tr>
        </thead>
        <tbody>
            {foreach item=forum from=$forums}
            <tr id="row_{$forum.fid}">
                {if $userid neq ''}
                <td align="center"  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}"'>
                    <div>
                        <a href="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}">
                            {if $forum.marcats neq 0}
                            {*img modname='IWforums' src='marcat.gif'*}
                            <span class="fs1em glyphicon glyphicon-flag"  data-toggle="tooltip" title="{gt text="Contains marked messages"}"></span>
                            {/if}
                        </a>
                    </div>

                </td>
                {/if}
                <td align="left" valign="top" style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}"'>
                    <a href="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}">
                        {$forum.nom_forum}
                    </a>
                </td>
                <td align="left" valign="top"  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}"'>{$forum.descriu|nl2br}</td>
                {if $userid neq ''}
                    <td  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}"'>
                        {if $forum.access eq 4}
                        {gt text="Moderation"}
                        {elseif $forum.access eq 3}
                        {gt text="Read, write and topics creation"}
                        {elseif $forum.access eq 2}
                        {gt text="Read and write"}
                        {else}
                        {gt text="Read only"}
                        {/if}
                    </td>
                    {* Subscription info *}
                    <td>
                        <div id="sm_{$forum.fid}">
                        {if isset($forumSubscriptions[$forum.fid])}
                            {switch expr=$forumSubscriptions[$forum.fid].action}
                                {case expr='none'}
                                    <span style="cursor:pointer" class="blue glyphicon glyphicon-comment" data-toggle="tooltip" title="{gt text="Everybody is subscribed"}"  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}"'></span>
                                {/case}
                                {case expr='add'}
                                    <span style="cursor:pointer" class="green glyphicon glyphicon-ok-circle" data-toggle="tooltip" title="{gt text="Subscribe me to this forum"}" onclick="changeSubscription({$forum.fid}, 1)"></span>
                                {/case}
                                {case expr='cancel'}
                                    <span style="cursor:pointer" class="red glyphicon glyphicon-remove-circle" data-toggle="tooltip" title="{gt text="Cancel my subscription"}" onclick="changeSubscription({$forum.fid}, 0 )"></span>
                                {/case} 
                            {/switch}
                        {else}                                                            
                            <span style="cursor:pointer" class="glyphicon glyphicon-ban-circle" data-toggle="tooltip" title="{gt text="This forum not allow subscriptions"}"  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}"'></span>
                        {/if}
                        </div>
                    </td>
                {/if}
                <td align="center" valign="top"  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}"'>{$forum.n_temes}</td>
                {userloggedin assign=userid}
                {if $userid neq ''}
                <td align="center" valign="top"  style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}"'>
                    {$forum.n_msg}
                </td>
                <td align="center" valign="top" style="cursor:pointer" onclick='window.location ="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}"'>
                    <div>
                        {if $forum.n_msg_no_llegits neq 0}                                 
                            <span style="background-color:#AC2013" class="badge">{$forum.n_msg_no_llegits}</span>
                        {else}
                            0
                        {/if}
                    </div>
                </td>
                {/if}
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
<script>
    jQuery('[data-toggle="tooltip"]').tooltip();
</script>