{include file="IWforums_user_menu.tpl" m2=1 m3=1 m4=1 m10=1 fid=$fid ftid=$ftid}
{userloggedin assign=userid}
{foreach item=missatge from=$missatges}
<div width="100%" class="msgBox">
    {if $missatge.photo neq '' AND ($userid neq '' OR $avatarsVisible eq 1)}
    <div class="photo">
        <img src="index.php?module=IWmain&type=user&func=getPhoto&fileName={$missatge.photo}" class="photoImg" />
    </div>
    {/if}
    <div>
        <font face="arial" color="#0080C0"><b>{$missatge.titol}</b></font>
    </div>
    <div>
        {gt text="From: "}
        <font color=green><b>{$users[$missatge.usuari]}</b></font>
    </div>
    <div>
        {gt text="Date"}: <font face="arial" size="2" color="#696969"><b>{$missatge.data}</b></font>
    </div>
    <div>
        {gt text="Time"}: <font face="arial" size="2" color="#696969"><b>{$missatge.hora}</b></font>
    </div>
    {if $missatge.adjunt neq ""}
    <div>
        <img src="modules/IWmain/images/fileIcons/{$missatge.fileIcon}" alt="" />
        <a href="{modurl modname='IWforums' type='user' func='download' fileName=$missatge.adjunt fmid=$missatge.fmid fid=$fid}">
            {$missatge.adjunt|safetext}
        </a>
    </div>
    {/if}
    <div class="z-clearer"></div>
    <hr />
    <div>
        {$missatge.missatge|nl2br}
    </div>
    <hr />
</div>
<br>
{/foreach}