<p>{gt text="A new message has been sent to the forum by %s on %s at %s. The content of the message is:" tag1=$usuari tag2=$data tag3=$hora}</p>
<hr />
<p><strong>{$titol}</strong></p>
<p>{$msg|nl2br}</p>
<hr />
<p>{gt text="You can access to the original content from"} <a href="{getbaseurl}{modurl modname='IWforums' type='user' func='msg' fid=$fid fmid=$fmid oid=$fmid ftid=$ftid}" target="_blank">{gt text="this link"}</a>.</p>

{if $subscriptions == 1 or $subscriptions == 2}
    <p>
        {gt text="If you do not want to receive automatic notifications from this forum you can remove your subscription from"} <a href="{getbaseurl}{modurl modname='IWforums' type='user' func='forum' fid=$fid}" target="_blank">{gt text="the forum"}</a>.
    </p>
{/if}
