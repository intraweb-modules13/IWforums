{include file="IWforums_user_menu.tpl" m5=1 m7=1 m8=1 fid=$fid ftid=$ftid fmid=$fmid u=$u}
<div class="usercontainer">
    <div class="userpageicon">{img modname='core' src='info.png' set='icons/large'}</div>
<h2>
{gt text="The message has been read by:"}
</h2>
    <table>
        {foreach item=reader from=$readers}
            <tr>
                <td>
                    {$reader.user}
                </td>
            </tr>
        {/foreach}
    </table>
</div>