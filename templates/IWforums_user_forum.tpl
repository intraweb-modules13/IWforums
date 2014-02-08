{include file="IWforums_user_menu.tpl" m1=1 m2=1 m3=1 m9=1 fid=$fid ftid=$ftid u=$u inici=$inici}
{userloggedin assign=userid}
<div class="usercontainer">
    {if $hi_ha_temes}
        <div class="userpageicon">{img modname='core' src='windowlist.png' set='icons/large'}</div>
        <h2>{$name} {if $u > 0}({$users[$u]|trim}){/if}</h2>
        {if $subscriptions gt 0}
            <div class="subscriptions">
                {include file="IWforums_user_subscriptions.tpl"}
            </div>
        {/if}
        <strong>{gt text="List of topics"}</strong>
        <table class="z-datatable">
            <tbody>
                {foreach item=tema from=$temes name="temes"}
                    <tr bgcolor="{cycle values="#ffffff, #eeeeee"}">
                        {if $userid neq ''}
                            <td align="center" width="2%" valign="top" class="topic">
                                <a href="{modurl modname='IWforums' type='user' func='llista_msg' ftid=$tema.ftid fid=$fid u=$u}">
                                    {if $tema.n_msg_no_llegits eq 0}
                                        {img modname='IWforums' src='msg.gif'}
                                    {else}
                                        {img modname='IWforums' src='msgNo.gif'}
                                    {/if}
                                </a>
                                <div>
                                    <a href="{modurl modname='IWforums' type='user' func='llista_msg' ftid=$tema.ftid fid=$fid u=$u}">
                                        {if $tema.marcats neq 0}
                                            {img modname='IWforums' src='marcat.gif'}
                                        {/if}
                                    </a>
                                </div>
                            </td>
                        {/if}
                        <td valign="top" class="topic">
                            <div>
                                <a href="{modurl modname='IWforums' type='user' func='llista_msg' ftid=$tema.ftid fid=$fid u=$u}">
                                    {$tema.titol}
                                </a>
                            </div>
                            <div>
                                {$tema.descriu|nl2br}
                            </div>
                            <div style="padding-top:10px; font-size:0.9em; font-style:italic;">
                                {gt text="topic created by:"}&nbsp;<span style="color:green; font-weight:bold;">{$users[$tema.usuari]}</span>&nbsp;{gt text="on"}&nbsp;{$tema.data}&nbsp;{gt text="at"}&nbsp;{$tema.hora}
                            </div>
                        </td>
                        <td width="27%" valign="top" class="topic">
                            <div>
                                {gt text="Messages:"}&nbsp;<strong>{$tema.n_msg}</strong>
                            </div>
                            {if $userid neq ''}
                                <div>
                                    {gt text=" Unread messages:"}&nbsp;{if $tema.n_msg_no_llegits neq 0}<font color="#FF0000">{else}<font color="#000000">{/if}<strong>{$tema.n_msg_no_llegits}</strong></font>
                                </div>
                            {/if}
                            {if $tema.last_post_exists}
                                <div class="lastPost">
                                    {gt text="Last post by"}&nbsp;{$users[$tema.lastuser]}&nbsp;{gt text="on"}&nbsp;{$tema.lastdate}&nbsp;{gt text="at"}&nbsp;{$tema.lasttime}
                                </div>
                            {/if}
                        </td>
                        {if $moderator}
                            <td align="center" width="40px" valign="top" class="topic">
                                <div>
                                    <a href="{modurl modname='IWforums' type='user' func='deltema' ftid=$tema.ftid fid=$fid u=$u}" title="{gt text="Delete the topic"}">
                                       {img modname='IWforums' src='deltema.gif' __alt="Topic and messages deletion" __title="Topic and messages deletion"}
                                    </a>
                                </div>
                                {if not $smarty.foreach.temes.first}
                                    <a href="{modurl modname='IWforums' type='user' func='order' ftid=$tema.ftid fid=$fid puts=1}" title="{gt text="Up"}">
                                        {img modname='IWforums' src='up.gif' __alt="Up" __title="Up"}
                                    </a>
                                {/if}
                                {if not $smarty.foreach.temes.last}
                                    <a href="{modurl modname='IWforums' type='user' func='order' ftid=$tema.ftid fid=$fid puts=-1}" title="{gt text="Down"}">
                                        {img modname='IWforums' src='down.gif' __alt="Down" __title="Down"}
                                    </a>
                                {/if}
                            </td>
                        {/if}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {/if}
    
    {if $hi_ha_missatges}
        {if !$hi_ha_temes}
            <div class="userpageicon">{img modname='core' src='windowlist.png' set='icons/large'}</div>
            {if $subscriptions gt 0}
                <div class="subscriptions">
                    {include file="IWforums_user_subscriptions.tpl"}
                </div>
            {/if}
        {/if}
        {if $ftid eq 0}
            {if $hi_ha_temes}
                <strong>{gt text="List of messages"}</strong>
            {else}
                <h2>{$name} - {gt text="List of messages"} {if $u > 0}({$users[$u]|trim}){/if}</h2>
            {/if}
        {else}
            <h2>{$name} => {$topicName} {if $u > 0}({$users[$u]|trim}){/if}</h2>
        {/if}
        <div style="clear: both;"></div>
        <div style="float: left;">
            {if $usuaris|@count gt 2}
                <form name="filtre" id="filtre" method="get" action="">
                    <input type="hidden" name="fid" value="{$fid}">
                    <input type="hidden" name="ftid" value="{$ftid}">
                    <input type="hidden" name="inici" value="1">
                    <input type="hidden" name="module" value="IWforums">
                    {if $ftid eq 0}
                        <input type="hidden" name="func" value="forum">
                    {else}
                        <input type="hidden" name="func" value="llista_msg">
                    {/if}
                    <select name="u" onChange="this.form.submit()">
                        {section name=usuaris loop=$usuaris}
                            <option {if $u eq $usuaris[usuaris].id}selected{/if} value="{$usuaris[usuaris].id}">{$usuaris[usuaris].name}</option>
                        {/section}
                    </select>
                </form>
            {/if}
        </div>
        <div style="float: right;">
            <a href="{modurl modname='IWforums' type='user' func='allmsg' ftid=$ftid fid=$fid u=$u inici=$inici}">
                {gt text="All the messages of the list"}
            </a>
        </div>
        <table class="z-datatable">
            <thead>
                <tr>
                    {if $userid neq ''}
                        <th>{img modname='IWforums' src='marcat.gif'}</th>
                    {/if}
                    {if $icons}
                        <th>&nbsp;</th>
                    {/if}
                    {if $adjunts}
                        <th>{img modname='IWforums' src='file.gif'}</th>
                    {/if}
                    {if $userid neq ''}
                        <th>{img modname='IWforums' src='MsgNoMsg.gif'}</th>
                    {/if}
                    <th style="text-align:left;">{gt text="Title"}</th>
                    <th style="text-align:left;">{gt text="Sender"}</th>
                    <th>{gt text="Date"}</th>
                    <th>{gt text="Time"}</th>
                    {if $access gt 1}
                        <th>{gt text="Actions"}</th>
                    {/if}
                </tr>
            </thead
            <tbody>
                {foreach item=message from=$messages}
                    <tr bgcolor="{cycle values="#ffffff, #eeeeee"}">
                        {if $userid neq ''}
                            <td align="center" width="10" onclick="javascript:mark({$fid}, {$message.fmid})">
                                {img modname='IWforums' src=$message.marcat __alt=$message.textmarca __title=$message.textmarca id=$message.fmid}
                            </td>
                        {/if}
                        {if $icons}
                            <td align="center" width="10">
                                {if $message.icon neq ""}
                                    {img modname='IWmain' src=$message.icon set='smilies'}
                                {/if}
                            </td>
                        {/if}
                        {if $adjunts}
                            <td align="center" width="10">
                                {if $message.adjunt neq ""}
                                    <a title="{$message.adjunt}">
                                        {img modname='IWforums' src='file.gif' __alt=$message.adjunt __title=$message.adjunt}
                                    </a>
                                {/if}
                            </td>
                        {/if}
                        {if $userid neq ''}
                            <td style="text-align:center;" width="10">
                                <a href="{modurl modname='IWforums' type='user' func='msg' fmid=$message.fmid ftid=$ftid fid=$fid u=$u oid=$message.oid inici=$inici}">
                                    <img src="modules/IWforums/images/{$message.imatge}" id="msgImage_{$message.fmid}" />
                                </a>
                            </td>
                        {/if}
                        <td style="padding-left:{$message.indent}px;">
                            <div class="titleRowTitle">
                                {if $message.onTop eq 1}
                                    <img src="modules/IWforums/images/onTop.gif" style="vertical-align: middle;" alt="{gt text='Main message'}" title="{gt text='Main message'}" />
                                {/if}
                                <a href="{modurl modname='IWforums' type='user' func='msg' fmid=$message.fmid ftid=$ftid fid=$fid u=$u oid=$message.oid inici=$inici}">
                                    {$message.title}
                                </a>
                            </div>
                            <div class="titleRowIcon">
                                <img src="modules/IWforums/images/msgopen.gif" onClick="javascript:openMsg({$message.fmid}, {$fid}, {$ftid}, {$u}, {$message.oid}, {$inici})" id="openMsgIcon_{$message.fmid}" name="openMsgIcon_{$message.fmid}">
                            </div>
                        </td>
                        <td style="color:green;"><strong>{$users[$message.user]}</strong></td>
                        <td style="color:#777; text-align:center;"><strong>{$message.date}</strong></td>
                        <td style="color:#777; text-align:center;"><strong>{$message.time}</strong></td>
                        {if $access gt 1}
                            <td {if $moderator}style="width:100px; text-align:center;"{/if}>
                                {if $moderator or $message.esborrable}
                                    <a href="{modurl modname='IWforums' type='user' func='del' fmid=$message.fmid ftid=$ftid fid=$fid u=$u inici=$inici}" title="{gt text='Delete the message'}">
                                        {img modname='IWforums' src='del.gif' __alt="Delete the message" __title="Delete the message"}
                                    </a>
                                {/if}
                                {if $moderator or $message.editable}
                                    <a href="{modurl modname='IWforums' type='user' func='edit_msg' fmid=$message.fmid ftid=$ftid fid=$fid u=$u inici=$inici}" title="{gt text='Edit the message'}">
                                        {img modname='IWforums' src='editar.gif' __alt="Edit the message" __title="Edit the message"}
                                    </a>
                                {/if}
                                {if $moderator}
                                    <a href="{modurl modname='IWforums' type='user' func='mou' fmid=$message.fmid ftid=$ftid fid=$fid u=$u inici=$inici}" title="{gt text='Move the message'}">
                                        {img modname='IWforums' src='moumsg.gif' __alt="Move the message" __title="Move the message"}
                                    </a>
                                    {if $message.fmid eq $message.oid}
                                        <a href="{modurl modname='IWforums' type='user' func='onTop' fmid=$message.fmid ftid=$ftid fid=$fid u=$u inici=$inici}" title="{if $message.onTop eq 0}{gt text='Set as main message'}{else}{gt text='Set as not main message'}{/if}">
                                            {if $message.onTop eq 1}
                                                {img modname='IWforums' src='onTop.gif' __alt="Move the message" __title="Set as not main message"}
                                            {else}
                                                {img modname='IWforums' src='noOnTop.gif' __alt="Move the message" __title="Set as main message"}
                                            {/if}
                                        </a>
                                    {else}
                                        {img modname='IWforums' src='blank.gif' __alt="Move the message" __title="Move the message"}
                                    {/if}
                                {/if}
                            </td>
                        {/if}
                    </tr>
                    <tr style="margin:0px; padding:0px;">
                        <td colspan="10" style="margin:0px; padding:0px;">
                            <div class="openMsg" id="openMsgRow_{$message.fmid}" name="openMsgRow_{$message.fmid}"></div>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        <div style="margin-left:20px;">{$pager}</div>
    {else}
        {if $u eq 0}
            {if $ftid neq 0}
                <div style="height:15px;">&nbsp;</div>
                <div>{gt text="This subjet has no messages."}</div>
            {/if}
        {else}
            {if $ftid neq 0}
                <div>{gt text="The user "}<strong>{$uname}</strong>{gt text=" you haven't sent any message to this topic"}</div>
            {/if}
        {/if}
    {/if}
    {if not $hi_ha_temes and not $hi_ha_missatges and $ftid eq 0}
        {if $subscriptions gt 0}
            <div class="subscriptions">
                {include file="IWforums_user_subscriptions.tpl"}
            </div>
        {/if}
        <div style="height:15px;">&nbsp;</div>
        <div>{gt text="This forum has no messages."}</div>
    {/if}
</div>