{* Generate report pending messages per user*}
<div>
{gt text = "This is your daily digest of messages sent to forums"}
</div>
<br>
{foreach item="forum" from=$info}
    {gt text ="Forum"} -> <a href="{modurl modname='IWforums' type='user' func='forum' fid=$forum.fid}">{$forum.nom_forum}</a><br>
    {foreach item="topic" from=$forum.topics key="ftid"}
        <span style="margin-left:20px">
        {gt text ="Topic"} -> <a href="{modurl modname='IWforums' type='user' func='llista_msg' fid=$forum.fid ftid=$ftid}">{$topic.titol}</a><br>
        </span>
        {foreach item=msg from=$topic.messages key="msgId"}
            <span style="margin-left:40px">
            <a href="{modurl modname='IWforums' type='user' func='llista_msg' fid=$forum.fid ftid=$ftid}#msgHeader{$msgId}">{$msg.title} </a>{gt text = "by"} <i>{$msg.author}</i> - <b>{$msg.date}</b><br>        
            </span>
        {/foreach}
    {/foreach}
    <span style="font-size:10px">
    {switch expr=$forum.subscriptionMode}
        {case expr= 'IWforums_Constant::COMPULSORY'|constant}
            {gt text="Everybody is subscribed"}
        {/case}
        {case expr='IWforums_Constant::VOLUNTARY'|constant}
            <a href="{modurl modname='IWforums' type='user' func='main'}"><u>{gt text="Cancel my subscription"}</u></a>
        {/case} 
        {case expr='IWforums_Constant::OPTIONAL'|constant}    
            <a href="{modurl modname='IWforums' type='user' func='main'}"><u>{gt text="Cancel my subscription"}</u></a>
        {/case} 
    {/switch}
    </span>
    <hr>
{/foreach}
